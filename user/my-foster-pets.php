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

$pets = $conn->query("
    SELECT *
    FROM pets
    WHERE user_id = $user_id
    AND owner_type = 'user'
    AND category = 'foster'
    AND is_deleted = 0
    ORDER BY id DESC
");
?>

<div class="min-h-screen bg-gray-50 px-6 md:px-12 py-10">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-[#2D6A4F]">
                My Foster Pets 🏡
            </h1>

            <p class="text-gray-500 mt-2">
                Manage your foster pets and applications
            </p>
        </div>

        <a href="add-foster-pet.php"
           class="mt-4 md:mt-0 bg-[#2D6A4F] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[#40916c]">

            + Add Foster Pet

        </a>

    </div>

    <!-- PET LIST -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

        <?php if ($pets->num_rows > 0): ?>

            <?php while($p = $pets->fetch_assoc()): ?>

                <?php
                $pet_id = $p['id'];

                $application_count = $conn->query("
                    SELECT COUNT(*) total
                    FROM foster_applications
                    WHERE pet_id = $pet_id
                ")->fetch_assoc()['total'];
                ?>

                <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-2xl transition">

                    <!-- IMAGE -->
                    <div class="h-60 overflow-hidden">

                        <img src="../assets/images/<?= htmlspecialchars($p['image']) ?>"
                             class="w-full h-full object-cover hover:scale-105 transition duration-500">

                    </div>

                    <!-- BODY -->
                    <div class="p-5">

                        <!-- NAME -->
                        <div class="flex justify-between items-start">

                            <h2 class="text-2xl font-bold text-gray-800">
                                <?= htmlspecialchars($p['name']) ?>
                            </h2>

                            <span class="
                                px-3 py-1 rounded-full text-xs font-semibold

                                <?= $p['status']=='available' ? 'bg-green-100 text-green-700' : '' ?>
                                <?= $p['status']=='fostered' ? 'bg-blue-100 text-blue-700' : '' ?>
                                <?= $p['status']=='adopted' ? 'bg-red-100 text-red-700' : '' ?>
                            ">

                                <?= ucfirst($p['status']) ?>

                            </span>

                        </div>

                        <!-- INFO -->
                        <div class="mt-4 space-y-2 text-sm text-gray-600">

                            <p>
                                🐾 Type:
                                <span class="font-medium">
                                    <?= $p['type'] ?>
                                </span>
                            </p>

                            <p>
                                🚻 Gender:
                                <span class="font-medium">
                                    <?= $p['gender'] ?>
                                </span>
                            </p>

                            <p>
                                📅 Age:
                                <span class="font-medium">
                                    <?= htmlspecialchars($p['age']) ?>
                                </span>
                            </p>

                            <p>
                                ⏳ Duration:
                                <span class="font-medium">
                                    <?= htmlspecialchars($p['foster_duration']) ?>
                                </span>
                            </p>

                            <p>
                                📩 Applications:
                                <span class="font-bold text-[#2D6A4F]">
                                    <?= $application_count ?>
                                </span>
                            </p>

                        </div>

                        <!-- BUTTONS -->
                        <div class="grid grid-cols-2 gap-2 mt-6">

                            <a href="edit-foster-pet.php?id=<?= $p['id'] ?>"
                               class="bg-blue-500 text-white text-center py-2 rounded-xl hover:bg-blue-600">

                                 Edit

                            </a>

                            <a href="delete-foster-pet.php?id=<?= $p['id'] ?>"
                               onclick="return confirm('Delete this foster pet?')"
                               class="bg-red-500 text-white text-center py-2 rounded-xl hover:bg-red-600">

                                 Delete

                            </a>

                        </div>

                        <div class="mt-3">

                            <a href="foster-applications.php?pet_id=<?= $p['id'] ?>"
                               class="block w-full bg-[#2D6A4F] text-white text-center py-3 rounded-xl hover:bg-[#40916c]">

                                View Applications (<?= $application_count ?>)

                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-span-3">

                <div class="bg-white rounded-2xl p-10 text-center shadow">

                    <h2 class="text-2xl font-bold text-gray-700 mb-3">
                        No Foster Pets Found
                    </h2>

                    <p class="text-gray-500 mb-6">
                        You haven't added any foster pets yet.
                    </p>

                    <a href="add-foster-pet.php"
                       class="bg-[#2D6A4F] text-white px-6 py-3 rounded-xl">

                        Add Your First Foster Pet

                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>