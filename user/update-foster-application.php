<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}

$id = (int)$_POST['id'];
$action = $_POST['action'];
$feedback = trim($_POST['feedback']);

if ($action == 'rejected' && empty($feedback)) {
    die("Feedback required for rejection!");
}

if ($action == 'approved') {

    $conn->query("
        UPDATE foster_applications
        SET status='approved',
            reviewed_at=NOW(),
            reviewed_by=".(int)$_SESSION['user_id']."
        WHERE id=$id
    ");

} else {

    $conn->query("
        UPDATE foster_applications
        SET status='rejected',
            feedback='".$conn->real_escape_string($feedback)."',
            reviewed_at=NOW(),
            reviewed_by=".(int)$_SESSION['user_id']."
        WHERE id=$id
    ");
}

header("Location: foster-applications.php");
exit();