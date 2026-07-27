<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// =========================
// LOGIN CHECK
// =========================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// =========================
// ELIGIBILITY CHECK
// =========================
$attempt = $conn->query("
    SELECT is_eligible FROM eligibility_attempts
    WHERE user_id = $user_id
    ORDER BY id DESC LIMIT 1
")->fetch_assoc();
$is_eligible = $attempt ? (int)$attempt['is_eligible'] : 0;

// =========================
// ALREADY APPLIED CHECK
// =========================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =========================
// FETCH PET
// =========================
$pet = $conn->query("
    SELECT * FROM pets
    WHERE id = $id
    AND category = 'adoption'
    AND is_deleted = 0
    LIMIT 1
")->fetch_assoc();

// PET NOT FOUND
if (!$pet) {
    die("Pet not found!");
}
?>

<section class="py-10 px-4 md:px-10 lg:px-16 bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col lg:flex-row gap-8 items-stretch">

            <!-- IMAGE CARD -->
            <div
                class="bg-[#E1CBB9] p-6 rounded-[35px] flex-1 shadow-2xl border border-[#d1b8a5] overflow-hidden">

                <div
                    class="relative w-full h-full min-h-[350px] overflow-hidden rounded-[25px] border-2 border-[#2d4d6a] group">

                    <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                         alt="<?= htmlspecialchars($pet['name']) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                </div>

            </div>



            <!-- DETAILS CARD -->
            <div
                class="bg-[#E1CBB9] p-6 md:p-8 rounded-[35px] flex-1 shadow-2xl border border-[#d1b8a5] hover:shadow-[0_20px_60px_rgba(0,0,0,0.15)] transition-all duration-300 flex flex-col justify-between relative overflow-hidden">

                <!-- BG EFFECT -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">

                    <!-- TOP -->
                    <div class="flex items-start justify-between mb-6">

                        <div>

                            <h1 class="text-4xl md:text-5xl font-extrabold text-[#1f2937]">
                                <?= htmlspecialchars($pet['name']) ?>
                            </h1>

                            <p class="text-gray-700 mt-1 text-lg">
                                Friendly & Lovely <?= htmlspecialchars($pet['type']) ?>
                            </p>

                        </div>

                        <!-- STATUS -->
                        <span
                            class="
                            px-4 py-2 rounded-2xl text-sm font-bold shadow-sm border

                            <?= $pet['status'] == 'available'
                                ? 'bg-[#d9e3c2] text-[#355E3B] border-[#b8c7a1]'
                                : '' ?>

                            <?= $pet['status'] == 'adopted'
                                ? 'bg-red-200 text-red-700 border-red-300'
                                : '' ?>
                            ">

                            <?= ucfirst($pet['status']) ?>

                        </span>

                    </div>



                    <!-- INFO GRID -->
                    <div class="grid grid-cols-2 gap-4">

                        <!-- AGE -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-4 shadow-sm">

                            <p class="text-sm text-gray-500">
                                Age
                            </p>

                            <h3 class="text-lg font-bold text-gray-800">
                                <?= htmlspecialchars($pet['age']) ?>
                            </h3>

                        </div>

                        <!-- GENDER -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-4 shadow-sm">

                            <p class="text-sm text-gray-500">
                                Gender
                            </p>

                            <h3 class="text-lg font-bold text-gray-800">
                                <?= htmlspecialchars($pet['gender']) ?>
                            </h3>

                        </div>

                        <!-- TYPE -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-4 shadow-sm">

                            <p class="text-sm text-gray-500">
                                Pet Type
                            </p>

                            <h3 class="text-lg font-bold text-gray-800">
                                <?= htmlspecialchars($pet['type']) ?>
                            </h3>

                        </div>

                        <!-- ACTIVITY -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-4 shadow-sm">

                            <p class="text-sm text-gray-500">
                                Activity
                            </p>

                            <h3 class="text-lg font-bold text-gray-800">
                                <?= htmlspecialchars($pet['activity_level']) ?>
                            </h3>

                        </div>

                    </div>



                    <!-- HEALTH -->
                    <div class="mt-6 bg-white/50 backdrop-blur-sm rounded-2xl p-4 shadow-sm">

                        <p class="text-sm text-gray-500 mb-1">
                            Health Status
                        </p>

                        <h3 class="text-lg font-bold text-gray-800">
                            <?= htmlspecialchars($pet['health_status']) ?>
                        </h3>

                    </div>



                    <!-- ABOUT -->
                    <div class="mt-6">

                        <h3 class="text-2xl font-bold text-gray-900 mb-3">
                            About <?= htmlspecialchars($pet['name']) ?>
                        </h3>

                        <p class="text-gray-700 leading-relaxed text-lg">

                            <?= nl2br(htmlspecialchars($pet['description'])) ?>

                        </p>

                    </div>

                </div>



                <!-- BUTTON -->
                <div class="mt-10 relative z-10">

                    <?php
                    // Check already applied
                    $already = $conn->query("
                        SELECT id FROM adoption_applications
                        WHERE user_id = $user_id AND pet_id = {$pet['id']} AND is_withdrawn = 0
                        LIMIT 1
                    ")->fetch_assoc();
                    ?>

                    <?php if ($already): ?>

                        <button class="w-full bg-gray-400 text-white font-bold py-4 rounded-2xl text-xl cursor-not-allowed">
                            Already Applied
                        </button>

                    <?php elseif (!$is_eligible): ?>

                        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
                            <p class="text-red-700 font-semibold mb-3">
                                You are not eligible to adopt pets.
                            </p>
                            <p class="text-gray-600 text-sm mb-4">
                                Please retake the eligibility quiz to qualify for adoption.
                            </p>
                            <a href="../quiz/eligibility.php"
                               class="inline-block bg-[#2D6A4F] text-white font-bold px-8 py-3 rounded-xl hover:bg-[#40916c] transition">
                                Retake Quiz
                            </a>
                        </div>

                    <?php elseif ($pet['status'] == 'available'): ?>

                        <a href="apply-adoption.php?id=<?= $pet['id'] ?>"
                           class="block w-full bg-[#6B704C] text-white font-bold py-4 rounded-2xl text-xl hover:scale-[1.02] hover:bg-[#5c6240] transition-all duration-300 shadow-lg text-center">

                            Apply to Adopt

                        </a>

                    <?php else: ?>

                        <button class="w-full bg-gray-400 text-white font-bold py-4 rounded-2xl text-xl cursor-not-allowed">
                            Not Available
                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>