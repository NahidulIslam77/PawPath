<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// =========================
// AUTH CHECK
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// =========================
// SHELTER ID
// =========================
$shelter = $conn->query("
    SELECT id FROM shelters WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    die("Shelter not found!");
}

$shelter_id = (int)$shelter['id'];

// =========================
// PET ID
// =========================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =========================
// FETCH PET (SECURITY)
// =========================
$pet = $conn->query("
    SELECT * FROM pets 
    WHERE id = $id 
    AND shelter_id = $shelter_id
    AND owner_type = 'shelter'
    LIMIT 1
")->fetch_assoc();

if (!$pet) {
    die("Pet not found or unauthorized!");
}

$category = $pet['category'];

// =========================
// UPDATE PET
// =========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $activity_level = $_POST['activity_level'];
    $health_status = $_POST['health_status'];
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    // foster only field
    $foster_duration = null;

    if ($category == 'foster') {
        $foster_duration = trim($_POST['foster_duration']);
    }

    // =========================
    // STATUS AUTO FIX (IMPORTANT)
    // =========================
    if ($category == 'adoption') {

        // adoption can only be available/adopted
        if ($status == 'fostered') {
            $status = 'adopted';
        }

    } else if ($category == 'foster') {

        // foster can only be available/fostered
        if ($status == 'adopted') {
            $status = 'fostered';
        }
    }

    // =========================
    // IMAGE
    // =========================
    $image = $pet['image'];

    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image);
    }

    // =========================
    // UPDATE QUERY
    // =========================
    $conn->query("
        UPDATE pets SET
            name = '$name',
            type = '$type',
            age = '$age',
            gender = '$gender',
            activity_level = '$activity_level',
            health_status = '$health_status',
            status = '$status',
            description = '$description',
            foster_duration = " . ($foster_duration ? "'$foster_duration'" : "NULL") . ",
            image = '$image',
            updated_at = NOW()
        WHERE id = $id
        AND shelter_id = $shelter_id
    ");

    header("Location: manage-pets.php");
    exit();
}
?>

<!-- =========================
     UI
========================= -->
<div class="min-h-screen bg-gray-50 p-6">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow">

        <h1 class="text-2xl font-bold text-[#2D6A4F] mb-6">
            ✏️ Edit Pet
        </h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- NAME -->
            <div>
                <label class="font-semibold text-sm">Pet Name</label>
                <input type="text" name="name"
                    value="<?= htmlspecialchars($pet['name']) ?>"
                    class="w-full border p-3 rounded-xl" required>
            </div>

            <!-- TYPE -->
            <div>
                <label class="font-semibold text-sm">Type</label>
                <select name="type" class="w-full border p-3 rounded-xl">
                    <option <?= $pet['type']=='Dog'?'selected':'' ?>>Dog</option>
                    <option <?= $pet['type']=='Cat'?'selected':'' ?>>Cat</option>
                    <option <?= $pet['type']=='Bird'?'selected':'' ?>>Bird</option>
                    <option <?= $pet['type']=='Rabbit'?'selected':'' ?>>Rabbit</option>
                    <option <?= $pet['type']=='Other'?'selected':'' ?>>Other</option>
                </select>
            </div>

            <!-- AGE -->
            <div>
                <label class="font-semibold text-sm">Age (month/year)</label>
                <input type="text" name="age"
                    value="<?= htmlspecialchars($pet['age']) ?>"
                    class="w-full border p-3 rounded-xl">
            </div>

            <!-- GENDER -->
            <div>
                <label class="font-semibold text-sm">Gender</label>
                <select name="gender" class="w-full border p-3 rounded-xl">
                    <option value="Male" <?= $pet['gender']=='Male'?'selected':'' ?>>Male</option>
                    <option value="Female" <?= $pet['gender']=='Female'?'selected':'' ?>>Female</option>
                </select>
            </div>

            <!-- ACTIVITY -->
            <div>
                <label class="font-semibold text-sm">Activity Level</label>
                <select name="activity_level" class="w-full border p-3 rounded-xl">
                    <option value="Low" <?= $pet['activity_level']=='Low'?'selected':'' ?>>Low</option>
                    <option value="Medium" <?= $pet['activity_level']=='Medium'?'selected':'' ?>>Medium</option>
                    <option value="High" <?= $pet['activity_level']=='High'?'selected':'' ?>>High</option>
                </select>
            </div>

            <!-- HEALTH -->
            <div>
                <label class="font-semibold text-sm">Health Status</label>
                <input type="text" name="health_status"
                    value="<?= htmlspecialchars($pet['health_status']) ?>"
                    class="w-full border p-3 rounded-xl">
            </div>

            <!-- CATEGORY INFO -->
            <div class="bg-gray-100 p-3 rounded-xl text-sm">
                Category: <b><?= $category ?></b>
            </div>

            <!-- STATUS -->
            <div>
                <label class="font-semibold text-sm">Status</label>
                <select name="status" class="w-full border p-3 rounded-xl">

                    <?php if ($category == 'adoption'): ?>
                        <option value="available" <?= $pet['status']=='available'?'selected':'' ?>>Available</option>
                        <option value="adopted" <?= $pet['status']=='adopted'?'selected':'' ?>>Adopted</option>
                    <?php else: ?>
                        <option value="available" <?= $pet['status']=='available'?'selected':'' ?>>Available</option>
                        <option value="fostered" <?= $pet['status']=='fostered'?'selected':'' ?>>Fostered</option>
                    <?php endif; ?>

                </select>
            </div>

            <!-- FOSTER ONLY -->
            <?php if ($category == 'foster'): ?>
            <div>
                <label class="font-semibold text-sm">Foster Duration</label>
                <input type="text" name="foster_duration"
                    value="<?= htmlspecialchars($pet['foster_duration']) ?>"
                    class="w-full border p-3 rounded-xl"
                    placeholder="e.g. 2 months">
            </div>
            <?php endif; ?>

            <!-- DESCRIPTION -->
            <div>
                <label class="font-semibold text-sm">Description</label>
                <textarea name="description"
                    class="w-full border p-3 rounded-xl"
                    rows="4"><?= htmlspecialchars($pet['description']) ?></textarea>
            </div>

            <!-- IMAGE -->
            <div>
                <label class="font-semibold text-sm">Image</label>

                <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                    class="h-40 w-full object-cover rounded-xl mb-3">

                <input type="file" name="image" class="w-full border p-3 rounded-xl">
            </div>

            <!-- SUBMIT -->
            <button class="w-full bg-[#2D6A4F] text-white py-3 rounded-xl font-semibold">
                Update Pet
            </button>

        </form>

    </div>

</div>