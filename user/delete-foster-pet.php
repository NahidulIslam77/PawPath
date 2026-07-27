<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pet = $conn->query("
    SELECT id
    FROM pets
    WHERE id = $id
    AND user_id = $user_id
    AND owner_type = 'user'
    AND category = 'foster'
    LIMIT 1
")->fetch_assoc();

if (!$pet) {
    die("Pet not found or unauthorized access!");
}

$conn->query("
    UPDATE pets
    SET
        is_deleted = 1,
        deleted_at = NOW()
    WHERE id = $id
");

header("Location: my-foster-pets.php");
exit();