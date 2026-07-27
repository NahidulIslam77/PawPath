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
// FORCE DELETE PET
// ==========================
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    $conn->query("UPDATE pets SET is_deleted = 1, deleted_at = NOW() WHERE id = $del_id");
    header("Location: manage-pets.php?msg=deleted");
    exit();
}

// ==========================
// OVERRIDE STATUS
// ==========================
if (isset($_POST['override_status'])) {
    $pet_id = (int) $_POST['pet_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $allowed = ['available', 'adopted', 'fostered'];
    if (in_array($new_status, $allowed)) {
        $conn->query("UPDATE pets SET status = '$new_status' WHERE id = $pet_id");
    }
    header("Location: manage-pets.php?msg=updated");
    exit();
}

// ==========================
// FILTERS
// ==========================
$search = isset($_GET['q']) ? $conn->real_escape_string(trim($_GET['q'])) : '';
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$filter_cat = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$where = "WHERE p.is_deleted = 0";
if ($search) $where .= " AND p.name LIKE '%$search%'";
if ($filter_status) $where .= " AND p.status = '$filter_status'";
if ($filter_cat) $where .= " AND p.category = '$filter_cat'";

$pets = $conn->query("
    SELECT
        p.*,
        s.shelter_name,
        u.name AS user_name
    FROM pets p
    LEFT JOIN shelters s ON s.id = p.shelter_id
    LEFT JOIN users u ON u.id = p.user_id
    $where
    ORDER BY p.created_at DESC
");
?>

<div class="p-6 md:p-10 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-[#2B4C7E]">Manage All Pets</h1>
            <p class="text-gray-500 mt-1 text-sm">View, filter, override status, and remove pets system-wide</p>
        </div>
        <a href="dashboard.php" class="text-[#2D6A4F] font-semibold text-sm">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-5 bg-green-100 text-green-700 px-4 py-3 rounded-xl text-sm">
            <?= $_GET['msg'] === 'deleted' ? 'Pet removed successfully.' : 'Pet status updated.' ?>
        </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
            placeholder="Search by pet name..."
            class="border rounded-xl px-4 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#2B4C7E]">

        <select name="status" class="border rounded-xl px-4 py-2 text-sm focus:outline-none">
            <option value="">All Statuses</option>
            <option value="available" <?= $filter_status === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="adopted" <?= $filter_status === 'adopted' ? 'selected' : '' ?>>Adopted</option>
            <option value="fostered" <?= $filter_status === 'fostered' ? 'selected' : '' ?>>Fostered</option>
        </select>

        <select name="category" class="border rounded-xl px-4 py-2 text-sm focus:outline-none">
            <option value="">All Categories</option>
            <option value="adoption" <?= $filter_cat === 'adoption' ? 'selected' : '' ?>>Adoption</option>
            <option value="foster" <?= $filter_cat === 'foster' ? 'selected' : '' ?>>Foster</option>
        </select>

        <button type="submit" class="bg-[#2B4C7E] text-white px-5 py-2 rounded-xl text-sm hover:bg-blue-800 transition">
            Filter
        </button>
        <a href="manage-pets.php" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm hover:bg-gray-300 transition">
            Reset
        </a>
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-4">Pet</th>
                    <th class="text-left px-5 py-4">Type</th>
                    <th class="text-left px-5 py-4">Category</th>
                    <th class="text-left px-5 py-4">Owner</th>
                    <th class="text-left px-5 py-4">Status</th>
                    <th class="text-left px-5 py-4">Added</th>
                    <th class="text-left px-5 py-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pets && $pets->num_rows > 0): ?>
                    <?php while ($p = $pets->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($p['image'])): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>"
                                            class="w-10 h-10 rounded-full object-cover"
                                            onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <span class="font-semibold"><?= htmlspecialchars($p['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-500"><?= htmlspecialchars($p['type']) ?></td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $p['category'] === 'adoption' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                    <?= ucfirst($p['category']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500">
                                <?= $p['owner_type'] === 'shelter'
                                    ? htmlspecialchars($p['shelter_name'] ?? 'N/A')
                                    : htmlspecialchars($p['user_name'] ?? 'N/A') ?>
                                <span class="text-xs text-gray-400">(<?= ucfirst($p['owner_type']) ?>)</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $p['status'] === 'adopted' ? 'bg-green-100 text-green-700' :
                                        ($p['status'] === 'fostered' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-600') ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-400"><?= date("d M Y", strtotime($p['created_at'])) ?></td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-2">

                                    <!-- OVERRIDE STATUS -->
                                    <form method="POST" class="flex gap-1 items-center">
                                        <input type="hidden" name="pet_id" value="<?= $p['id'] ?>">
                                        <select name="new_status" class="border rounded-lg px-2 py-1 text-xs">
                                            <option value="available" <?= $p['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                            <option value="adopted" <?= $p['status'] === 'adopted' ? 'selected' : '' ?>>Adopted</option>
                                            <option value="fostered" <?= $p['status'] === 'fostered' ? 'selected' : '' ?>>Fostered</option>
                                        </select>
                                        <button type="submit" name="override_status"
                                            class="bg-[#6B8E23] text-white px-2 py-1 rounded-lg text-xs hover:opacity-90 transition">
                                            Override
                                        </button>
                                    </form>

                                    <!-- REMOVE -->
                                    <a href="manage-pets.php?delete=<?= $p['id'] ?>"
                                        onclick="return confirm('Remove this pet?')"
                                        class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs font-semibold hover:bg-red-200 transition">
                                        Remove
                                    </a>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-gray-400">No pets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../includes/footer.php'; ?>