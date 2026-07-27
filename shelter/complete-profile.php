<?php
session_start();

include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SESSION['role'] != 'shelter') {
    header("Location: ../index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $shelter_name = trim($_POST['shelter_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("
        INSERT INTO shelters
        (user_id, shelter_name, address, phone)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isss",
        $user_id,
        $shelter_name,
        $address,
        $phone
    );

    if ($stmt->execute()) {

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Something went wrong!";
    }
}
?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg">

        <h1 class="text-3xl font-bold mb-6 text-[#2D6A4F]">
            Complete Shelter Profile
        </h1>

        <?php if($error): ?>
            <p class="text-red-500 mb-4">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <div>
                <label class="block mb-2 font-medium">
                    Shelter Name
                </label>

                <input type="text"
                       name="shelter_name"
                       required
                       class="w-full border px-4 py-3 rounded-xl">
            </div>

            <div>
                <label class="block mb-2 font-medium">
                    Address
                </label>

                <textarea name="address"
                          required
                          class="w-full border px-4 py-3 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block mb-2 font-medium">
                    Phone
                </label>

                <input type="text"
                       name="phone"
                       required
                       class="w-full border px-4 py-3 rounded-xl">
            </div>

            <button type="submit"
                    class="w-full bg-[#2D6A4F] text-white py-3 rounded-xl">

                Save Profile

            </button>

        </form>

    </div>

</div>