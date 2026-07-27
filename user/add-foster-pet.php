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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $activity_level = $_POST['activity_level'];
    $health_status = trim($_POST['health_status']);
    $foster_duration = trim($_POST['foster_duration']);
    $description = trim($_POST['description']);

    $image = '';

    if (!empty($_FILES['image']['name'])) {

        $image = time() . '_' . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            '../assets/images/' . $image
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO pets
        (
            user_id,
            owner_type,
            category,
            name,
            type,
            age,
            gender,
            activity_level,
            health_status,
            foster_duration,
            description,
            image
        )
        VALUES
        (?, 'user', 'foster', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssssss",
        $user_id,
        $name,
        $type,
        $age,
        $gender,
        $activity_level,
        $health_status,
        $foster_duration,
        $description,
        $image
    );

    $stmt->execute();

    header("Location: my-foster-pets.php");
    exit();
}
?>

<div class="max-w-4xl mx-auto p-6">

    <div class="bg-white p-8 rounded-3xl shadow">

        <h1 class="text-3xl font-bold mb-6">
            Add Foster Pet 🏡
        </h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <div>
                <label class="font-semibold">Pet Image</label>
                <input type="file" name="image" required class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Pet Name</label>
                <input type="text" name="name" required class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Pet Type</label>
                <select name="type" required class="w-full border p-3 rounded-xl">
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Bird">Bird</option>
                    <option value="Rabbit">Rabbit</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Age</label>
                <input type="text"
                       name="age"
                       placeholder="e.g. 2 Months / 1 Year"
                       required
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Gender</label>
                <select name="gender" required class="w-full border p-3 rounded-xl">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Activity Level</label>
                <select name="activity_level" required class="w-full border p-3 rounded-xl">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Health Status</label>
                <input type="text"
                       name="health_status"
                       required
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Foster Duration</label>
                <input type="text"
                       name="foster_duration"
                       placeholder="e.g. 2 Months"
                       required
                       class="w-full border p-3 rounded-xl">
            </div>

            <div>
                <label class="font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          required
                          class="w-full border p-3 rounded-xl"></textarea>
            </div>

            <button
                class="w-full bg-[#2D6A4F] text-white py-4 rounded-xl font-bold">
                Add Foster Pet
            </button>

        </form>

    </div>

</div>