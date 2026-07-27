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

// ✅ UNION ALL দিয়ে দুটি টেবিল কম্বাইন করা হয়েছে এবং বাটন রুলসের জন্য রুট ডিফাইন করা হয়েছে
$applications = $conn->query("
    SELECT 
        a.id, a.pet_id, a.status, a.feedback, a.is_withdrawn, a.created_at,
        p.name AS pet_name, p.image, p.type AS pet_type,
        'Adoption' AS app_type,
        'adopt-details.php' AS detail_page,
        'withdraw-application.php' AS withdraw_page
    FROM adoption_applications a
    JOIN pets p ON a.pet_id = p.id
    WHERE a.user_id = $user_id

    UNION ALL

    SELECT 
        f.id, f.pet_id, f.status, f.feedback, f.is_withdrawn, f.created_at,
        p.name AS pet_name, p.image, p.type AS pet_type,
        'Foster' AS app_type,
        'foster-details.php' AS detail_page,
        'withdraw-application.php' AS withdraw_page
    FROM foster_applications f
    JOIN pets p ON f.pet_id = p.id
    WHERE f.user_id = $user_id

    ORDER BY created_at DESC
");
?>

<div class="px-6 md:px-12 py-10 bg-gray-50 min-h-screen">

    <h1 class="text-3xl font-bold mb-8 text-[#2D6A4F] flex items-center gap-2">
        <span>📩</span> My Applications
    </h1>

    <?php if ($applications->num_rows > 0): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php while ($a = $applications->fetch_assoc()): ?>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
                    
                    <div>
                        <div class="relative">
                            <img src="../assets/images/<?= htmlspecialchars($a['image']) ?>"
                                 class="h-48 w-full object-cover" alt="Pet Image">
                            
                            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold rounded-full tracking-wide bg-white/90 text-gray-800 shadow-sm backdrop-blur-sm">
                                <?= $a['app_type'] ?>
                            </span>
                        </div>

                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <h2 class="text-xl font-bold text-gray-800 truncate">
                                    <?= htmlspecialchars($a['pet_name']) ?>
                                </h2>
                                
                                <div class="shrink-0">
                                    <?php if ($a['is_withdrawn'] == 1): ?>
                                        <span class="bg-gray-200 text-gray-600 border border-gray-300 px-2.5 py-1 rounded-full text-xs font-semibold">
                                            Withdrawn
                                        </span>
                                    <?php elseif ($a['status'] == 'pending'): ?>
                                        <span class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                            Pending
                                        </span>
                                    <?php elseif ($a['status'] == 'approved'): ?>
                                        <span class="bg-green-100 text-green-800 border border-green-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                            Approved
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 border border-red-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                            Rejected
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="text-gray-500 text-sm mb-4">
                                Type: <span class="font-medium text-gray-700"><?= htmlspecialchars($a['pet_type']) ?></span>
                            </p>

                            <?php if ($a['status'] == 'rejected' && !empty($a['feedback'])): ?>
                                <div class="mt-2 bg-red-50 border border-red-100 p-3 rounded-xl text-xs text-red-700">
                                    <strong>❗ Reason:</strong> <?= htmlspecialchars($a['feedback']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-5 pt-0 mt-2 flex gap-3">

                        <?php if ($a['is_withdrawn'] == 1): ?>

                            <!-- Withdrawn: Details only, grayed out -->
                            <a href="<?= $a['detail_page'] ?>?id=<?= $a['pet_id'] ?>"
                               class="flex-1 text-center bg-gray-300 text-gray-600 font-medium py-2.5 rounded-xl text-sm">
                                Details
                            </a>

                        <?php elseif ($a['status'] == 'rejected'): ?>

                            <!-- Rejected: Details only -->
                            <a href="<?= $a['detail_page'] ?>?id=<?= $a['pet_id'] ?>"
                               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl text-sm transition shadow-sm">
                                Details
                            </a>

                        <?php else: ?>

                            <!-- Pending / Approved: Details + Withdraw -->
                            <a href="<?= $a['detail_page'] ?>?id=<?= $a['pet_id'] ?>"
                               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl text-sm transition shadow-sm">
                                Details
                            </a>

                            <a href="<?= $a['withdraw_page'] ?>?id=<?= $a['id'] ?>&type=<?= strtolower($a['app_type']) ?>"
                               onclick="return confirm('Withdraw this <?= strtolower($a['app_type']) ?> application?')"
                               class="flex-1 text-center bg-gray-500 hover:bg-gray-600 text-white font-medium py-2.5 rounded-xl text-sm transition shadow-sm">
                                Withdraw
                            </a>

                        <?php endif; ?>

                    </div>

                </div>
            <?php endwhile; ?>

        </div>
    <?php else: ?>
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-gray-200 max-w-md mx-auto mt-10">
            <p class="text-gray-500 font-medium">You haven't submitted any applications yet.</p>
        </div>
    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>