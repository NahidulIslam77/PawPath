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
$id = (int)$_GET['id'];

$pet = $conn->query("
    SELECT *
    FROM pets
    WHERE id = $id
    AND user_id = $user_id
    AND owner_type = 'user'
    AND category = 'foster'
    LIMIT 1
")->fetch_assoc();

if (!$pet) {
    die("Pet not found!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $activity_level = $_POST['activity_level'];
    $health_status = trim($_POST['health_status']);
    $foster_duration = trim($_POST['foster_duration']);
    $description = trim($_POST['description']);

    $image = $pet['image'];

    if (!empty($_FILES['image']['name'])) {

        $image = time() . '_' . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            '../uploads/' . $image
        );
    }

    $stmt = $conn->prepare("
        UPDATE pets
        SET
            name=?,
            type=?,
            age=?,
            gender=?,
            activity_level=?,
            health_status=?,
            foster_duration=?,
            description=?,
            image=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssssssssi",
        $name,
        $type,
        $age,
        $gender,
        $activity_level,
        $health_status,
        $foster_duration,
        $description,
        $image,
        $id
    );

    $stmt->execute();

    header("Location: my-foster-pets.php");
    exit();
}
?>

<div class="max-w-4xl mx-auto p-6">

    <div class="bg-white p-8 rounded-3xl shadow">

        <h1 class="text-3xl font-bold mb-6">
            Edit Foster Pet ✏️
        </h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <div>
                <label class="font-semibold">Current Image</label>

                <img src="../uploads/<?= htmlspecialchars($pet['image']) ?>"
                     class="w-48 rounded-xl mb-3">

                <input type="file"
                       name="image"
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Pet Name</label>
                <input type="text"
                       name="name"
                       value="<?= htmlspecialchars($pet['name']) ?>"
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Age</label>
                <input type="text"
                       name="age"
                       value="<?= htmlspecialchars($pet['age']) ?>"
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Foster Duration</label>
                <input type="text"
                       name="foster_duration"
                       value="<?= htmlspecialchars($pet['foster_duration']) ?>"
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Health Status</label>
                <input type="text"
                       name="health_status"
                       value="<?= htmlspecialchars($pet['health_status']) ?>"
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Description</label>
                <textarea
                    name="description"
                    rows="5"
                    class="w-full border p-3 rounded-xl"><?= htmlspecialchars($pet['description']) ?></textarea>
            </div>

            <button
                class="w-full bg-[#2D6A4F] text-white py-4 rounded-xl font-bold">
                Update Foster Pet
            </button>

        </form>

    </div>

</div>