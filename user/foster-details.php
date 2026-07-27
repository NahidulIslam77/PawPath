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
$pet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pet = $conn->query("
    SELECT *
    FROM pets
    WHERE id = $pet_id
    AND category = 'foster'
    AND is_deleted = 0
    LIMIT 1
")->fetch_assoc();

if (!$pet) {
    die("Pet not found!");
}

$already = $conn->query("
    SELECT id
    FROM foster_applications
    WHERE user_id = $user_id
    AND pet_id = $pet_id
")->num_rows;
?>

<section class="py-10 px-4 md:px-10 lg:px-16">

    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col lg:flex-row gap-8 items-stretch">

            <!-- IMAGE -->
            <div
                class="bg-[#E1CBB9] p-6 rounded-[35px] flex-1 shadow-2xl border border-[#d1b8a5] flex">

                <div
                    class="relative flex-1 overflow-hidden rounded-[25px] border-2 border-[#2d4d6a] group">

                    <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                         alt="<?= htmlspecialchars($pet['name']) ?>"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                </div>

            </div>

            <!-- DETAILS -->
            <div
                class="bg-[#E1CBB9] p-8 rounded-[35px] flex-1 shadow-xl flex flex-col">

                <!-- HEADER -->
                <div class="flex justify-between items-start flex-wrap gap-4">

                    <div>

                        <h1 class="text-4xl md:text-5xl font-bold">
                            <?= htmlspecialchars($pet['name']) ?>
                        </h1>

                        <p class="text-gray-600 mt-2">
                            Foster Pet
                        </p>

                    </div>

                    <span class="bg-green-100 px-4 py-2 rounded-xl font-semibold">
                        <?= ucfirst($pet['status']) ?>
                    </span>

                </div>

                <!-- GRID INFO -->
                <div class="grid grid-cols-2 gap-4 mt-8">

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Age</p>
                        <h3 class="font-semibold"><?= $pet['age'] ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Gender</p>
                        <h3 class="font-semibold"><?= $pet['gender'] ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Type</p>
                        <h3 class="font-semibold"><?= $pet['type'] ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Activity</p>
                        <h3 class="font-semibold"><?= $pet['activity_level'] ?></h3>
                    </div>

                </div>

                <!-- HEALTH -->
                <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">

                    <p class="text-gray-500 text-sm">Health Status</p>

                    <h3 class="font-semibold break-words">
                        <?= htmlspecialchars($pet['health_status']) ?>
                    </h3>

                </div>

                <!-- FOSTER DURATION -->
                <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">

                    <p class="text-gray-500 text-sm">Foster Duration</p>

                    <h3 class="font-semibold break-words">
                        <?= htmlspecialchars($pet['foster_duration']) ?>
                    </h3>

                </div>

                <!-- DESCRIPTION -->
                <div class="mt-6 flex-grow">

                    <h3 class="text-2xl font-bold mb-3">About Pet</h3>

                    <div class="bg-white p-4 rounded-xl shadow-sm">

                        <p class="leading-relaxed text-gray-700 break-words">
                            <?= nl2br(htmlspecialchars($pet['description'])) ?>
                        </p>

                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-8">

                    <?php if ($already > 0): ?>

                        <button disabled
                                class="w-full bg-gray-400 text-white py-4 rounded-xl cursor-not-allowed">
                            Already Applied
                        </button>

                    <?php else: ?>

                        <a href="apply-foster.php?id=<?= $pet['id'] ?>"
                           class="block text-center w-full bg-[#6B704C] hover:bg-[#59603f] text-white py-4 rounded-xl transition">
                            Apply To Foster
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>