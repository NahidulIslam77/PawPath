<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// pets টেবিল থেকে ডাটা নেওয়া হচ্ছে
$pets = $conn->query("
    SELECT *
    FROM pets
    WHERE category='foster'
    AND status='available'
    AND is_deleted=0
    ORDER BY id DESC
");
?>

<div class="px-6 md:px-12 py-10 bg-gray-50 min-h-screen">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">

        <div>
            <h1 class="text-3xl font-bold text-[#2D6A4F]">
                Foster Pets 🏡
            </h1>
            <p class="text-gray-500 text-sm mt-1">Find a pet to foster or manage your listings</p>
        </div>

        <div class="flex flex-wrap gap-3">
            
            <a href="my-foster-pets.php"
               class="bg-white text-[#2D6A4F] border border-[#2D6A4F] px-5 py-3 rounded-xl font-semibold hover:bg-gray-100 transition flex items-center gap-2">
               🐾 My Pets
            </a>

            <a href="add-foster-pet.php"
               class="bg-[#2D6A4F] text-white px-5 py-3 rounded-xl font-semibold hover:bg-[#40916c] transition">
                + Add Foster Pet
            </a>
            
        </div>

    </div>

    <div class="grid md:grid-cols-3 gap-8">

        <?php while($pet = $pets->fetch_assoc()): ?>

        <?php
        $pet_id = $pet['id'];
        $pet_owner_id = (int)$pet['user_id']; // পেটের আসল মালিকের আইডি

        // ইউজার ইতিমধ্যে আবেদন করেছে কিনা তা চেক
        $already = $conn->query("
            SELECT id
            FROM foster_applications
            WHERE user_id=$user_id
            AND pet_id=$pet_id
        ")->num_rows;
        ?>

        <div class="bg-[#c2b4a3] rounded-[50px] p-6 shadow-xl flex flex-col border border-gray-400 transition-transform hover:scale-105 duration-300">

            <div class="relative w-full h-64 overflow-hidden rounded-[35px] border-2 border-[#2d4d6a]">
                <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                     class="w-full h-full object-cover">
            </div>

            <div class="mt-6 flex justify-between items-start">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        <?= htmlspecialchars($pet['name']) ?>
                    </h2>
                    <p class="text-lg text-gray-800 mt-2">
                        Type : <?= htmlspecialchars($pet['type']) ?><br>
                        Gender : <?= htmlspecialchars($pet['gender']) ?><br>
                        Age : <?= htmlspecialchars($pet['age']) ?><br>
                        Duration : <?= htmlspecialchars($pet['foster_duration']) ?>
                    </p>
                </div>
                <span class="bg-[#d9e3c2] px-5 py-2 rounded-xl text-gray-800 font-bold">
                    <?= ucfirst(htmlspecialchars($pet['status'])) ?>
                </span>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="foster-details.php?id=<?= $pet['id'] ?>"
                   class="bg-[#386485] text-white flex-1 py-4 rounded-2xl font-bold text-lg text-center">
                    Details
                </a>

                <?php if($user_id === $pet_owner_id): ?>
                    <button disabled
                        class="bg-orange-600 text-white flex-1 py-4 rounded-2xl font-bold text-base cursor-not-allowed"
                        title="You cannot foster your own pet">
                        Your Pet
                    </button>

                <?php elseif($already > 0): ?>
                    <button disabled
                        class="bg-gray-500 text-white flex-1 py-4 rounded-2xl font-bold text-lg cursor-not-allowed">
                        Already Applied
                    </button>

                <?php else: ?>
                    <a href="apply-foster.php?id=<?= $pet['id'] ?>"
                       class="bg-[#386485] text-white flex-1 py-4 rounded-2xl font-bold text-lg text-center">
                        Apply To Foster
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <?php endwhile; ?>

    </div>
</div>