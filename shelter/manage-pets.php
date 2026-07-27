<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// =========================
// AUTH CHECK
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$shelter = $conn->query("
    SELECT id FROM shelters WHERE user_id = $user_id
")->fetch_assoc();

$shelter_id = $shelter['id'];

$pets = $conn->query("
    SELECT * 
    FROM pets 
    WHERE shelter_id = $shelter_id
    AND owner_type = 'shelter'
    AND is_deleted = 0
");
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#2D6A4F]">
            Manage Pets 🐾
        </h1>

        <a href="add-pet.php"
           class="bg-[#2D6A4F] text-white px-4 py-2 rounded-lg">
            + Add Pet
        </a>
    </div>

    <!-- PET LIST -->
    <?php if ($pets && $pets->num_rows > 0): ?>

        <div class="grid md:grid-cols-3 gap-6">

            <?php while ($p = $pets->fetch_assoc()): ?>

   <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 group">

    <!-- IMAGE -->
    <div class="relative h-44 w-full overflow-hidden bg-gray-100">

         <img src="<?= !empty($p['image']) ? '../assets/images/'.$p['image'] : '../assets/images/default.png' ?>"
         class="w-full h-full object-cover object-center transition duration-500 group-hover:scale-105"
         onerror="this.src='../assets/images/default.png'">
        <!-- STATUS BADGE (TOP RIGHT) -->
        <div class="absolute top-3 right-3">
            <span class="px-3 py-1 text-xs rounded-full font-semibold
                <?= $p['status'] == 'available' ? 'bg-green-100 text-green-700' : '' ?>
                <?= $p['status'] == 'adopted' ? 'bg-red-100 text-red-600' : '' ?>
                <?= $p['status'] == 'fostered' ? 'bg-blue-100 text-blue-600' : '' ?>
            ">
                <?= ucfirst($p['status']) ?>
            </span>
        </div>

    </div>

    <!-- BODY -->
    <div class="p-5">

        <!-- NAME -->
        <h2 class="text-xl font-bold text-gray-800 group-hover:text-[#2D6A4F] transition">
            <?= htmlspecialchars($p['name']) ?>
        </h2>

        <!-- CATEGORY + STATUS -->
        <div class="flex gap-2 mt-2 flex-wrap">

            <!-- CATEGORY -->
            <span class="px-3 py-1 text-xs rounded-full font-semibold
                <?= $p['category'] == 'adoption' ? 'bg-purple-100 text-purple-700' : '' ?>
                <?= $p['category'] == 'foster' ? 'bg-yellow-100 text-yellow-700' : '' ?>
            ">
                <?= ucfirst($p['category']) ?>
            </span>

            <!-- STATUS -->
            <span class="px-3 py-1 text-xs rounded-full font-semibold
                <?= $p['status'] == 'available' ? 'bg-green-100 text-green-700' : '' ?>
                <?= $p['status'] == 'adopted' ? 'bg-red-100 text-red-600' : '' ?>
                <?= $p['status'] == 'fostered' ? 'bg-blue-100 text-blue-600' : '' ?>
            ">
                <?= ucfirst($p['status']) ?>
            </span>

        </div>

        <!-- DETAILS -->
        <div class="mt-3 text-sm text-gray-500 space-y-1">

            <p>🐾 Type: <span class="font-medium text-gray-700"><?= htmlspecialchars($p['type']) ?></span></p>

            <p>📅 Age: <span class="font-medium text-gray-700"><?= htmlspecialchars($p['age']) ?></span></p>

            <?php if (!empty($p['activity_level'])): ?>
                <p>⚡ Activity: <span class="font-medium text-gray-700"><?= $p['activity_level'] ?></span></p>
            <?php endif; ?>

        </div>

        <!-- FOSTER DURATION (ONLY IF EXISTS) -->
        <?php if ($p['category'] == 'foster' && !empty($p['foster_duration'])): ?>
            <div class="mt-2 text-sm text-gray-500">
                ⏳ Duration: <span class="font-medium text-gray-700">
                    <?= htmlspecialchars($p['foster_duration']) ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- DIVIDER -->
        <div class="my-4 border-t"></div>

        <!-- ACTION BUTTONS -->
        <div class="flex justify-between items-center">

            <a href="edit-pet.php?id=<?= $p['id'] ?>"
               class="px-3 py-2 text-sm rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
                Edit
            </a>

            <a href="delete-pet.php?id=<?= $p['id'] ?>"
               onclick="return confirm('Are you sure you want to delete this pet?')"
               class="px-3 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                 Delete
            </a>

        </div>

    </div>

</div>
            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <!-- EMPTY STATE -->
        <div class="text-center text-gray-500 mt-20">
            <p class="text-xl">No pets found 🐾</p>
            <p class="mt-2">Start by adding your first pet</p>

            <a href="add-pet.php"
               class="inline-block mt-4 bg-[#2D6A4F] text-white px-5 py-2 rounded-lg">
                Add Pet
            </a>
        </div>

    <?php endif; ?>

</div>