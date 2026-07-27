<?php
session_start();

include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* Shelter ID */
$shelter = $conn->query("
    SELECT id
    FROM shelters
    WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    die("Shelter profile not found.");
}

$shelter_id = (int)$shelter['id'];

/* Combined Applications */
$applications = $conn->query("

    SELECT
        a.id,
        a.user_id,
        a.pet_id,

        a.status,
        a.is_withdrawn,
        a.created_at,

        'Adoption' AS application_type,

        u.name AS applicant_name,

        p.name AS pet_name,
        p.type,
        p.age

    FROM adoption_applications a

    JOIN pets p ON p.id = a.pet_id
    JOIN users u ON u.id = a.user_id

    WHERE p.shelter_id = $shelter_id


    UNION ALL


    SELECT
        f.id,
        f.user_id,
        f.pet_id,

        f.status,
        f.is_withdrawn,
        f.created_at,

        'Foster' AS application_type,

        u.name AS applicant_name,

        p.name AS pet_name,
        p.type,
        p.age

    FROM foster_applications f

    JOIN pets p ON p.id = f.pet_id
    JOIN users u ON u.id = f.user_id

    WHERE p.shelter_id = $shelter_id

    ORDER BY created_at DESC
");

$total = $applications->num_rows;
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold">
                Applications Dashboard 📩
            </h1>

            <p class="text-gray-600 mt-1">
                Total Applications: <?= $total ?>
            </p>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-4 text-left">Pet</th>
                    <th class="p-4 text-left">Applicant</th>
                    <th class="p-4 text-left">Type</th>
                    <th class="p-4 text-left">Pet Info</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Date</th>
                    <th class="p-4 text-center">Action</th>
                </tr>

            </thead>

            <tbody>

            <?php if ($applications->num_rows > 0): ?>

                <?php while ($app = $applications->fetch_assoc()): ?>

                    <?php

                   $displayStatus = ($app['is_withdrawn'] == 1)
    ? 'withdrawn'
    : $app['status'];

$statusClass = match($displayStatus) {
    'approved' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
    'withdrawn' => 'bg-gray-200 text-gray-700',
    default => 'bg-yellow-100 text-yellow-700'
};

                    $typeParam = strtolower($app['application_type']);

                    ?>

                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-4 font-medium">
                            <?= htmlspecialchars($app['pet_name']) ?>
                        </td>

                        <td class="p-4">
                            <?= htmlspecialchars($app['applicant_name']) ?>
                        </td>

                        <td class="p-4">
                            <?= $app['application_type'] ?>
                        </td>

                        <td class="p-4">
                            <?= htmlspecialchars($app['type']) ?>
                            •
                            <?= $app['age'] ?> yrs
                        </td>

                       <td class="p-4">
    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
        <?= ucfirst($displayStatus) ?>
    </span>
</td>

                        <td class="p-4">
                            <?= date('d M Y', strtotime($app['created_at'])) ?>
                        </td>

                        <td class="p-4 text-center">

                            <a
                                href="application-details.php?id=<?= $app['id'] ?>&type=<?= $typeParam ?>"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm"
                            >
                                Details
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No applications found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include '../includes/footer.php'; ?>