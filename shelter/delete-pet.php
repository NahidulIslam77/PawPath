<?php
session_start();
include '../includes/db.php';

// =========================
// AUTH CHECK (ONLY SHELTER)
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// =========================
// GET SHELTER ID
// =========================
$shelter = $conn->query("
    SELECT id FROM shelters WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    die("Shelter not found!");
}

$shelter_id = (int)$shelter['id'];

// =========================
// GET PET ID
// =========================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid pet ID!");
}

// =========================
// CHECK OWNERSHIP FIRST (SECURITY)
// =========================
$pet = $conn->query("
    SELECT id 
    FROM pets 
    WHERE id = $id 
    AND shelter_id = $shelter_id 
    AND owner_type = 'shelter'
    AND is_deleted = 0
")->fetch_assoc();

if (!$pet) {
    die("Pet not found or unauthorized access!");
}

// =========================
// SOFT DELETE
// =========================
$conn->query("
    UPDATE pets 
    SET 
        is_deleted = 1,
        deleted_at = NOW(),
        updated_at = NOW()
    WHERE id = $id 
    AND shelter_id = $shelter_id
    AND owner_type = 'shelter'
");

header("Location: manage-pets.php?deleted=1");
exit();
?>