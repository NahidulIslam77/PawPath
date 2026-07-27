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
// DELETE USER
// ==========================
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    // Prevent self-deletion
    if ($del_id !== (int) $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $del_id");
    }
    header("Location: manage-users.php?msg=deleted");
    exit();
}

// ==========================
// CHANGE ROLE
// ==========================
if (isset($_POST['change_role'])) {
    $target_id = (int) $_POST['user_id'];
    $new_role = $conn->real_escape_string($_POST['new_role']);
    $allowed = ['user', 'shelter', 'admin'];
    if (in_array($new_role, $allowed) && $target_id !== (int) $_SESSION['user_id']) {
        $conn->query("UPDATE users SET role = '$new_role' WHERE id = $target_id");
    }
    header("Location: manage-users.php?msg=updated");
    exit();
}

// ==========================
// FETCH USERS
// ==========================
$search = isset($_GET['q']) ? $conn->real_escape_string(trim($_GET['q'])) : '';
$role_filter = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
if ($role_filter) $where .= " AND role = '$role_filter'";

$users = $conn->query("SELECT * FROM users $where ORDER BY created_at DESC");
?>

<div class="p-6 md:p-10 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-[#2B4C7E]">Manage Users</h1>
            <p class="text-gray-500 mt-1 text-sm">View, filter, change roles, and remove users</p>
        </div>
        <a href="dashboard.php" class="text-[#2D6A4F] font-semibold text-sm">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-5 bg-green-100 text-green-700 px-4 py-3 rounded-xl text-sm">
            <?= $_GET['msg'] === 'deleted' ? 'User removed successfully.' : 'User role updated successfully.' ?>
        </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
            placeholder="Search by name or email..."
            class="border rounded-xl px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-[#2B4C7E]">

        <select name="role" class="border rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B4C7E]">
            <option value="">All Roles</option>
            <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>User</option>
            <option value="shelter" <?= $role_filter === 'shelter' ? 'selected' : '' ?>>Shelter</option>
            <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <button type="submit" class="bg-[#2B4C7E] text-white px-5 py-2 rounded-xl text-sm hover:bg-blue-800 transition">
            Filter
        </button>
        <a href="manage-users.php" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm hover:bg-gray-300 transition">
            Reset
        </a>
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-4">Name</th>
                    <th class="text-left px-5 py-4">Email</th>
                    <th class="text-left px-5 py-4">Role</th>
                    <th class="text-left px-5 py-4">Eligible</th>
                    <th class="text-left px-5 py-4">Joined</th>
                    <th class="text-left px-5 py-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium"><?= htmlspecialchars($u['name']) ?></td>
                            <td class="px-5 py-4 text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $u['role'] === 'admin' ? 'bg-red-100 text-red-700' :
                                        ($u['role'] === 'shelter' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs <?= $u['is_eligible'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                    <?= $u['is_eligible'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-400"><?= date("d M Y", strtotime($u['created_at'])) ?></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">

                                    <?php if ($u['id'] !== (int) $_SESSION['user_id']): ?>

                                        <!-- CHANGE ROLE -->
                                        <form method="POST" class="flex gap-1 items-center">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <select name="new_role" class="border rounded-lg px-2 py-1 text-xs">
                                                <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="shelter" <?= $u['role'] === 'shelter' ? 'selected' : '' ?>>Shelter</option>
                                                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <button type="submit" name="change_role"
                                                class="bg-[#2D6A4F] text-white px-2 py-1 rounded-lg text-xs hover:bg-green-700 transition">
                                                Set
                                            </button>
                                        </form>

                                        <!-- DELETE -->
                                        <a href="manage-users.php?delete=<?= $u['id'] ?>"
                                            onclick="return confirm('Remove this user?')"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs font-semibold hover:bg-red-200 transition">
                                            Remove
                                        </a>

                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">You</span>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../includes/footer.php'; ?>