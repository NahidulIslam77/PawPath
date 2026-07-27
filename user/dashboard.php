<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// ==========================
// LOGIN CHECK
// ==========================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id   = (int)$_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// ==========================
// ELIGIBILITY CHECK
// ==========================
$attempt = $conn->query("
    SELECT is_eligible
    FROM eligibility_attempts
    WHERE user_id = $user_id
    ORDER BY id DESC
    LIMIT 1
")->fetch_assoc();

if (!$attempt) {
    header("Location: ../quiz/eligibility.php");
    exit();
}

$is_eligible = (int)$attempt['is_eligible'];

// ==========================
// SEARCH + FILTER INPUTS
// ==========================
$search = trim($_GET['search'] ?? '');
$type   = trim($_GET['type']   ?? '');
$level  = trim($_GET['level']  ?? '');


$sql = "SELECT * FROM pets WHERE is_deleted = 0 AND status = 'available'";

if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (name LIKE '%$s%' OR type LIKE '%$s%')";
}
if ($type !== '') {
    $t = $conn->real_escape_string($type);
    $sql .= " AND type = '$t'";
}
if ($level !== '') {
    $l = $conn->real_escape_string($level);
    $sql .= " AND activity_level = '$l'";
}

$sql .= " ORDER BY created_at DESC";
$pets = $conn->query($sql);

$show_personalized = ($search === '' && $type === '' && $level === '');

$personalized_pets = null;
if ($show_personalized) {
    // Pick 6 random available pets as "recommended"
    // (can be enhanced later with a user prefs table)
    $personalized_pets = $conn->query("
        SELECT * FROM pets
        WHERE is_deleted = 0
        AND status = 'available'
        ORDER BY RAND()
        LIMIT 6
    ");
}
?>

<div class="px-6 md:px-12 py-10 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex flex-wrap items-center justify-between mb-8 gap-3">
        <div>
            <h1 class="text-3xl font-bold text-[#2D6A4F]">
                Welcome, <?= htmlspecialchars($user_name) ?>
            </h1>
            <p class="text-gray-500 mt-1 text-sm">
                Find your perfect companion below
            </p>
        </div>

        <!-- ELIGIBILITY BADGE -->
        <?php if ($is_eligible): ?>
            <span class="bg-green-100 text-green-700 border border-green-200 px-4 py-2 rounded-full text-sm font-semibold">
                Eligible to Adopt &amp; Foster
            </span>
        <?php else: ?>
            <div class="flex items-center gap-3">
                <span class="bg-red-100 text-red-700 border border-red-200 px-4 py-2 rounded-full text-sm font-semibold">
                    Not Eligible to Adopt
                </span>
                <a href="../quiz/eligibility.php"
                   class="bg-[#2D6A4F] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-[#40916c] transition">
                    Retake Quiz
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- SEARCH + FILTER BAR -->
    <form method="GET" class="mb-10 grid grid-cols-1 md:grid-cols-4 gap-3">

        <input type="text"
               name="search"
               value="<?= htmlspecialchars($search) ?>"
               placeholder="Search by name or type..."
               class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]">

        <select name="type" class="w-full px-4 py-3 border rounded-xl focus:outline-none">
            <option value="">All Types</option>
            <option value="Dog"    <?= $type==='Dog'    ? 'selected' : '' ?>>Dog</option>
            <option value="Cat"    <?= $type==='Cat'    ? 'selected' : '' ?>>Cat</option>
            <option value="Bird"   <?= $type==='Bird'   ? 'selected' : '' ?>>Bird</option>
            <option value="Rabbit" <?= $type==='Rabbit' ? 'selected' : '' ?>>Rabbit</option>
            <option value="Other"  <?= $type==='Other'  ? 'selected' : '' ?>>Other</option>
        </select>

        <select name="level" class="w-full px-4 py-3 border rounded-xl focus:outline-none">
            <option value="">All Activity Levels</option>
            <option value="Low"    <?= $level==='Low'    ? 'selected' : '' ?>>Low</option>
            <option value="Medium" <?= $level==='Medium' ? 'selected' : '' ?>>Medium</option>
            <option value="High"   <?= $level==='High'   ? 'selected' : '' ?>>High</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-[#2D6A4F] text-white rounded-xl font-semibold hover:bg-[#40916c] transition">
                Search
            </button>
            <?php if ($search || $type || $level): ?>
                <a href="dashboard.php"
                   class="flex-1 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center justify-center">
                    Reset
                </a>
            <?php endif; ?>
        </div>

    </form>

    <?php if ($show_personalized && $personalized_pets && $personalized_pets->num_rows > 0): ?>

        <!-- PERSONALIZED SECTION -->
        <div class="mb-12">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Recommended for You</h2>
                    <p class="text-gray-500 text-sm mt-1">Pets available for adoption and fostering</p>
                </div>
                <a href="adopt-pets.php" class="text-[#2D6A4F] font-semibold text-sm">View All Adoption Pets &rarr;</a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <?php while ($p = $personalized_pets->fetch_assoc()): ?>
                    <div class="bg-white rounded-2xl shadow overflow-hidden hover:shadow-lg transition group">

                        <div class="relative h-44 overflow-hidden">
                            <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='../assets/images/default.png'">
                            <span class="absolute top-3 left-3 bg-white/90 text-xs font-bold px-3 py-1 rounded-full
                                <?= $p['category'] === 'adoption' ? 'text-purple-700' : 'text-blue-700' ?>">
                                <?= ucfirst($p['category']) ?>
                            </span>
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="text-gray-500 text-sm mt-1">
                                <?= htmlspecialchars($p['type']) ?> &bull;
                                <?= htmlspecialchars($p['activity_level']) ?> activity &bull;
                                <?= htmlspecialchars($p['gender']) ?>
                            </p>

                            <a href="<?= $p['category'] === 'adoption' ? 'adopt-details.php' : 'foster-details.php' ?>?id=<?= $p['id'] ?>"
                               class="mt-4 inline-block bg-[#2D6A4F] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#40916c] transition">
                                View Details
                            </a>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>

        </div>

    <?php endif; ?>

    <!-- SEARCH RESULTS / ALL PETS -->
    <?php if (!$show_personalized || ($search || $type || $level)): ?>

        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                <?= ($search || $type || $level) ? 'Search Results' : 'All Available Pets' ?>
            </h2>
            <?php if ($pets): ?>
                <span class="text-gray-400 text-sm"><?= $pets->num_rows ?> pet(s) found</span>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <?php if ($pets && $pets->num_rows > 0): ?>
                <?php while ($p = $pets->fetch_assoc()): ?>
                    <div class="bg-white rounded-2xl shadow overflow-hidden hover:shadow-lg transition group">

                        <div class="relative h-44 overflow-hidden">
                            <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='../assets/images/default.png'">
                            <span class="absolute top-3 left-3 bg-white/90 text-xs font-bold px-3 py-1 rounded-full
                                <?= $p['category'] === 'adoption' ? 'text-purple-700' : 'text-blue-700' ?>">
                                <?= ucfirst($p['category']) ?>
                            </span>
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="text-gray-500 text-sm mt-1">
                                <?= htmlspecialchars($p['type']) ?> &bull;
                                <?= htmlspecialchars($p['activity_level']) ?> activity
                            </p>

                            <a href="<?= $p['category'] === 'adoption' ? 'adopt-details.php' : 'foster-details.php' ?>?id=<?= $p['id'] ?>"
                               class="mt-4 inline-block bg-[#2D6A4F] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#40916c] transition">
                                View Details
                            </a>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16">
                    <p class="text-gray-400 text-lg">No pets found matching your search.</p>
                    <a href="dashboard.php" class="mt-4 inline-block text-[#2D6A4F] font-semibold">Reset Search</a>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>