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

// ==========================
// DELETE SHELTER
// ==========================
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    // Get user_id of shelter before deleting
    $s = $conn->query("SELECT user_id FROM shelters WHERE id = $del_id")->fetch_assoc();
    if ($s) {
        $conn->query("DELETE FROM shelters WHERE id = $del_id");
        // Downgrade shelter user back to 'user' role
        $uid = (int) $s['user_id'];
        $conn->query("UPDATE users SET role = 'user' WHERE id = $uid");
    }
    header("Location: manage-shelters.php?msg=deleted");
    exit();
}

// ==========================
// FETCH SHELTERS
// ==========================
$search = isset($_GET['q']) ? $conn->real_escape_string(trim($_GET['q'])) : '';
$where = $search ? "WHERE s.shelter_name LIKE '%$search%' OR u.name LIKE '%$search%'" : '';

$shelters = $conn->query("
    SELECT
        s.*,
        u.name AS owner_name,
        u.email AS owner_email,
        COUNT(DISTINCT p.id) AS total_pets,
        COUNT(DISTINCT CASE WHEN p.status = 'adopted' AND p.is_deleted = 0 THEN p.id END) AS adopted,
        COUNT(DISTINCT CASE WHEN p.status = 'fostered' AND p.is_deleted = 0 THEN p.id END) AS fostered
    FROM shelters s
    JOIN users u ON u.id = s.user_id
    LEFT JOIN pets p ON p.shelter_id = s.id
    $where
    GROUP BY s.id
    ORDER BY s.created_at DESC
");
?>

<div class="p-6 md:p-10 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-[#2B4C7E]">Manage Shelters</h1>
            <p class="text-gray-500 mt-1 text-sm">View all registered shelters and their activity</p>
        </div>
        <a href="dashboard.php" class="text-[#2D6A4F] font-semibold text-sm">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-5 bg-green-100 text-green-700 px-4 py-3 rounded-xl text-sm">
            Shelter removed successfully.
        </div>
    <?php endif; ?>

    <!-- SEARCH -->
    <form method="GET" class="flex gap-3 mb-6">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
            placeholder="Search by shelter or owner name..."
            class="border rounded-xl px-4 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-[#2B4C7E]">
        <button type="submit" class="bg-[#2B4C7E] text-white px-5 py-2 rounded-xl text-sm hover:bg-blue-800 transition">
            Search
        </button>
        <a href="manage-shelters.php" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm hover:bg-gray-300 transition">
            Reset
        </a>
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-4">Shelter Name</th>
                    <th class="text-left px-5 py-4">Owner</th>
                    <th class="text-left px-5 py-4">Phone</th>
                    <th class="text-left px-5 py-4">Address</th>
                    <th class="text-center px-5 py-4">Total Pets</th>
                    <th class="text-center px-5 py-4">Adopted</th>
                    <th class="text-center px-5 py-4">Fostered</th>
                    <th class="text-left px-5 py-4">Registered</th>
                    <th class="text-left px-5 py-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($shelters && $shelters->num_rows > 0): ?>
                    <?php while ($s = $shelters->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-5 py-4 font-semibold text-[#2B4C7E]"><?= htmlspecialchars($s['shelter_name']) ?></td>
                            <td class="px-5 py-4">
                                <p class="font-medium"><?= htmlspecialchars($s['owner_name']) ?></p>
                                <p class="text-gray-400 text-xs"><?= htmlspecialchars($s['owner_email']) ?></p>
                            </td>
                            <td class="px-5 py-4 text-gray-500"><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
                            <td class="px-5 py-4 text-gray-500 max-w-xs truncate"><?= htmlspecialchars($s['address'] ?? '-') ?></td>
                            <td class="px-5 py-4 text-center font-bold text-[#2B4C7E]"><?= $s['total_pets'] ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                                    <?= $s['adopted'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                                    <?= $s['fostered'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-400"><?= date("d M Y", strtotime($s['created_at'])) ?></td>
                            <td class="px-5 py-4">
                                <a href="manage-shelters.php?delete=<?= $s['id'] ?>"
                                    onclick="return confirm('Remove this shelter? The owner will be downgraded to a regular user.')"
                                    class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs font-semibold hover:bg-red-200 transition">
                                    Remove
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-gray-400">No shelters found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../includes/footer.php'; ?>