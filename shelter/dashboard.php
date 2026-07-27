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

// ==========================
// ROLE CHECK
// ==========================
if ($_SESSION['role'] != 'shelter') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// ==========================
// GET SHELTER ID
// ==========================
$shelter = $conn->query("
    SELECT *
    FROM shelters
    WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    header("Location: complete-profile.php");
    exit();
}

$shelter_id = $shelter['id'];


// ==========================
// TOTAL ADOPTION PETS
// ==========================
$total_adoption = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND category = 'adoption'
    AND is_deleted = 0
")->fetch_assoc()['total'];


// ==========================
// TOTAL FOSTER PETS
// ==========================
$total_foster = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND category = 'foster'
    AND is_deleted = 0
")->fetch_assoc()['total'];


// ==========================
// AVAILABLE PETS
// ==========================
$available = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'available'
    AND is_deleted = 0
")->fetch_assoc()['total'];


// ==========================
// ADOPTED PETS
// ==========================
$adopted = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'adopted'
    AND is_deleted = 0
")->fetch_assoc()['total'];


// ==========================
// FOSTERED PETS
// ==========================
$fostered = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'fostered'
    AND is_deleted = 0
")->fetch_assoc()['total'];


// ==========================
// TOTAL APPLICATIONS
// ==========================
$total_applications = $conn->query("
    SELECT COUNT(*) as total
    FROM adoption_applications aa
    JOIN pets p ON aa.pet_id = p.id
    WHERE p.shelter_id = $shelter_id
")->fetch_assoc()['total'];


// ==========================
// RECENT APPLICATIONS
// ==========================
$recent = $conn->query("
    SELECT
        aa.id,
        aa.status,
        aa.created_at,
        u.name AS user_name,
        p.name AS pet_name
    FROM adoption_applications aa

    JOIN users u
    ON aa.user_id = u.id

    JOIN pets p
    ON aa.pet_id = p.id

    WHERE p.shelter_id = $shelter_id

    ORDER BY aa.created_at DESC
    LIMIT 5
");
?>

<div class="bg-gray-50 min-h-screen px-6 md:px-12 py-10">

    <!-- HEADER -->
    <div class="mb-10">

        <h1 class="text-4xl font-bold text-[#2D6A4F]">
            Shelter Dashboard 🏠
        </h1>

        <p class="text-gray-500 mt-2">
            Welcome back, <?= $user_name ?>
        </p>

    </div>


    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-5 mb-12">

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Adoption Pets
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $total_adoption ?>
            </p>
        </div>

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Foster Pets
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $total_foster ?>
            </p>
        </div>

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Available
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $available ?>
            </p>
        </div>

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Adopted
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $adopted ?>
            </p>
        </div>

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Fostered
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $fostered ?>
            </p>
        </div>

        <!-- CARD -->
        <div class="bg-white p-5 rounded-2xl shadow">
            <h3 class="text-gray-500 text-sm">
                Applications
            </h3>

            <p class="text-3xl font-bold text-[#2D6A4F] mt-2">
                <?= $total_applications ?>
            </p>
        </div>

    </div>


    <!-- QUICK ACTIONS -->
    <div class="flex flex-wrap gap-4 mb-12">

        <a href="add-pet.php"
           class="bg-[#2D6A4F] text-white px-6 py-3 rounded-xl shadow hover:bg-[#40916c] transition">

            + Add Pet

        </a>

        <a href="manage-pets.php"
           class="bg-white border px-6 py-3 rounded-xl shadow hover:bg-gray-100 transition">

            Manage Pets

        </a>

        <a href="pet-applications.php"
           class="bg-white border px-6 py-3 rounded-xl shadow hover:bg-gray-100 transition">

            Applications

        </a>

    </div>


    <!-- RECENT APPLICATIONS -->
    <div class="bg-white rounded-2xl shadow p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-2xl font-bold">
                Recent Applications 📩
            </h2>

            <a href="pet-applications.php"
               class="text-[#2D6A4F] font-semibold">
                View All
            </a>

        </div>

        <?php if ($recent->num_rows > 0): ?>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Applicant
                            </th>

                            <th class="text-left py-3">
                                Pet
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Date
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $recent->fetch_assoc()): ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="py-4">
                                    <?= $row['user_name'] ?>
                                </td>

                                <td class="py-4">
                                    <?= $row['pet_name'] ?>
                                </td>

                                <td class="py-4">

                                    <span class="
                                        px-3 py-1 rounded-full text-sm

                                        <?=
                                        $row['status'] == 'approved'
                                            ? 'bg-green-100 text-green-700'
                                            : ($row['status'] == 'rejected'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-yellow-100 text-yellow-700')
                                        ?>
                                    ">

                                        <?= ucfirst($row['status']) ?>

                                    </span>

                                </td>

                                <td class="py-4">
                                    <?= date("d M Y", strtotime($row['created_at'])) ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-gray-500">
                No applications found.
            </p>

        <?php endif; ?>

    </div>

</div>

<?php include '../includes/footer.php'; ?>