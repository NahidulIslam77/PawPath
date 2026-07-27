<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// ==========================
// AUTH CHECK
// ==========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// ==========================
// STATS
// ==========================
$total_pets = $conn->query("SELECT COUNT(*) as t FROM pets WHERE is_deleted = 0")->fetch_assoc()['t'];
$total_adopted = $conn->query("SELECT COUNT(*) as t FROM pets WHERE status = 'adopted' AND is_deleted = 0")->fetch_assoc()['t'];
$total_fostered = $conn->query("SELECT COUNT(*) as t FROM pets WHERE status = 'fostered' AND is_deleted = 0")->fetch_assoc()['t'];
$total_available = $conn->query("SELECT COUNT(*) as t FROM pets WHERE status = 'available' AND is_deleted = 0")->fetch_assoc()['t'];
$total_users = $conn->query("SELECT COUNT(*) as t FROM users WHERE role = 'user'")->fetch_assoc()['t'];
$total_shelters = $conn->query("SELECT COUNT(*) as t FROM shelters")->fetch_assoc()['t'];

// ==========================
// SHELTER-WISE REPORT
// ==========================
$shelter_report = $conn->query("
    SELECT
        s.shelter_name,
        COUNT(CASE WHEN p.status = 'adopted' AND p.is_deleted = 0 THEN 1 END) AS adopted,
        COUNT(CASE WHEN p.status = 'fostered' AND p.is_deleted = 0 THEN 1 END) AS fostered,
        COUNT(CASE WHEN p.is_deleted = 0 THEN 1 END) AS total
    FROM shelters s
    LEFT JOIN pets p ON p.shelter_id = s.id
    GROUP BY s.id, s.shelter_name
    ORDER BY adopted DESC
");

// ==========================
// RECENT USERS
// ==========================
$recent_users = $conn->query("
    SELECT id, name, email, role, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<div class="p-6 md:p-10 bg-gray-50 min-h-screen">

    <!-- PAGE HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#2B4C7E]">Admin Dashboard</h1>
        <p class="text-gray-500 mt-1">Welcome back, <?= htmlspecialchars($user_name) ?>. Here is a full system overview.</p>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-[#2B4C7E]">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Total Pets</p>
            <p class="text-3xl font-bold text-[#2B4C7E] mt-1"><?= $total_pets ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-[#2D6A4F]">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Adopted</p>
            <p class="text-3xl font-bold text-[#2D6A4F] mt-1"><?= $total_adopted ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-[#6B8E23]">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Fostered</p>
            <p class="text-3xl font-bold text-[#6B8E23] mt-1"><?= $total_fostered ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-yellow-400">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Available</p>
            <p class="text-3xl font-bold text-yellow-500 mt-1"><?= $total_available ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-purple-400">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Users</p>
            <p class="text-3xl font-bold text-purple-600 mt-1"><?= $total_users ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-t-4 border-orange-400">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Shelters</p>
            <p class="text-3xl font-bold text-orange-500 mt-1"><?= $total_shelters ?></p>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="flex flex-wrap gap-4 mb-10">
        <a href="manage-users.php" class="bg-[#2B4C7E] text-white px-6 py-3 rounded-xl shadow hover:bg-blue-800 transition">
            Manage Users
        </a>
        <a href="manage-shelters.php" class="bg-[#2D6A4F] text-white px-6 py-3 rounded-xl shadow hover:bg-green-700 transition">
            Manage Shelters
        </a>
        <a href="manage-pets.php" class="bg-[#6B8E23] text-white px-6 py-3 rounded-xl shadow hover:opacity-90 transition">
            Manage All Pets
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-8">

        <!-- SHELTER-WISE REPORT -->
        <div class="bg-white rounded-2xl shadow p-6">

            <h2 class="text-xl font-bold text-[#2B4C7E] mb-5">Shelter-wise Report</h2>

            <?php if ($shelter_report && $shelter_report->num_rows > 0): ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 text-gray-500 font-semibold">Shelter</th>
                                <th class="text-center py-3 text-gray-500 font-semibold">Total Pets</th>
                                <th class="text-center py-3 text-gray-500 font-semibold">Adopted</th>
                                <th class="text-center py-3 text-gray-500 font-semibold">Fostered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $shelter_report->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 font-medium"><?= htmlspecialchars($row['shelter_name']) ?></td>
                                    <td class="py-3 text-center text-[#2B4C7E] font-bold"><?= $row['total'] ?></td>
                                    <td class="py-3 text-center">
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                                            <?= $row['adopted'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                                            <?= $row['fostered'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <p class="text-gray-400 text-sm">No shelters found.</p>
            <?php endif; ?>

        </div>

        <!-- RECENT USERS -->
        <div class="bg-white rounded-2xl shadow p-6">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-[#2B4C7E]">Recent Users</h2>
                <a href="manage-users.php" class="text-[#2D6A4F] text-sm font-semibold">View All</a>
            </div>

            <?php if ($recent_users && $recent_users->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php while ($u = $recent_users->fetch_assoc()): ?>
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <div>
                                <p class="font-semibold text-sm"><?= htmlspecialchars($u['name']) ?></p>
                                <p class="text-gray-400 text-xs"><?= htmlspecialchars($u['email']) ?></p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full
                                <?= $u['role'] === 'admin' ? 'bg-red-100 text-red-700' :
                                    ($u['role'] === 'shelter' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600') ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-400 text-sm">No users found.</p>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>