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
// ALREADY APPLIED PET IDs
// =========================
$applied_ids = [];
$applied_res = $conn->query("
    SELECT pet_id FROM adoption_applications
    WHERE user_id = $user_id AND is_withdrawn = 0
");
while ($row = $applied_res->fetch_assoc()) {
    $applied_ids[] = (int)$row['pet_id'];
}

// =========================
// FETCH ADOPTION PETS
// =========================
$pets = $conn->query("
    SELECT * FROM pets
    WHERE category = 'adoption'
    AND is_deleted = 0
    ORDER BY created_at DESC
");
?>

<div class="px-6 md:px-12 py-10 bg-gray-50 min-h-screen">

    <!-- PAGE TITLE -->
    <div class="mb-10">

        <h1 class="text-4xl md:text-5xl font-bold text-[#2d4d6a] flex items-center gap-3">
    Adopt Pets <i class="fas fa-paw text-[#2D6A4F]"></i>
</h1>

        <p class="text-gray-600 mt-3 text-lg">
            Find your perfect furry companion.
        </p>

    </div>

    <!-- PET GRID -->
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-10">

        <?php if ($pets->num_rows > 0): ?>

            <?php while ($p = $pets->fetch_assoc()): ?>

                <div
                    class="bg-[#c2b4a3] rounded-[50px] p-6 shadow-xl flex flex-col border border-gray-400 transition-transform hover:scale-105 duration-300">

                    <!-- IMAGE -->
                    <div class="relative w-full h-64 overflow-hidden rounded-[35px] border-2 border-[#2d4d6a]">

                        <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>"
                             alt="<?= htmlspecialchars($p['name']) ?>"
                             class="w-full h-full object-cover">

                    </div>

                    <!-- CONTENT -->
                    <div class="mt-6 flex justify-between items-start gap-4">

                        <div>

                            <!-- NAME -->
                            <h2 class="text-3xl font-bold text-gray-900">
                                <?= htmlspecialchars($p['name']) ?>
                            </h2>

                            <!-- DETAILS -->
                            <p class="text-xl text-gray-800 leading-tight mt-2">

                                Type : <?= htmlspecialchars($p['type']) ?>

                                <br>

                                Gender : <?= htmlspecialchars($p['gender']) ?>

                                <br>

                                Age : <?= htmlspecialchars($p['age']) ?>

                            </p>

                        </div>

                        <!-- STATUS -->
                        <span class="
                            px-5 py-2 rounded-xl text-gray-800 font-bold shadow-sm

                            <?= $p['status'] == 'available'
                                ? 'bg-[#d9e3c2]'
                                : '' ?>

                            <?= $p['status'] == 'adopted'
                                ? 'bg-red-200 text-red-700'
                                : '' ?>
                        ">

                            <?= ucfirst($p['status']) ?>

                        </span>

                    </div>

                    <!-- BUTTONS -->
                    <div class="mt-8 flex gap-3">

                        <!-- DETAILS -->
                        <a href="adopt-details.php?id=<?= $p['id'] ?>"
                           class="bg-[#386485] text-white flex-1 py-4 rounded-2xl font-bold text-lg hover:bg-slate-700 transition-colors text-center">

                            Details

                        </a>

                        <!-- APPLY / BLOCK -->
                        <?php
                        $already_applied = in_array((int)$p['id'], $applied_ids);
                        ?>

                        <?php if ($already_applied): ?>

                            <span class="bg-gray-300 text-gray-600 flex-1 py-4 rounded-2xl font-bold text-lg text-center cursor-not-allowed">
                                Already Applied
                            </span>

                        <?php elseif (!$is_eligible): ?>

                            <a href="not-eligible.php"
                               class="bg-red-200 text-red-700 flex-1 py-4 rounded-2xl font-bold text-lg text-center hover:bg-red-300 transition-colors">
                                Not Eligible
                            </a>

                        <?php elseif ($p['status'] !== 'available'): ?>

                            <span class="bg-gray-300 text-gray-600 flex-1 py-4 rounded-2xl font-bold text-lg text-center cursor-not-allowed">
                                Not Available
                            </span>

                        <?php else: ?>

                            <a href="apply-adoption.php?id=<?= $p['id'] ?>"
                               class="bg-[#386485] text-white flex-1 py-4 rounded-2xl font-bold text-lg hover:bg-slate-700 transition-colors text-center">

                                Apply to adopt

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-span-full text-center py-20">

                <h2 class="text-3xl font-bold text-gray-700">
                    No adoptable pets found 🐾
                </h2>

            </div>

        <?php endif; ?>

    </div>

</div>