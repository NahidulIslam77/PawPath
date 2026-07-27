<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid request!");
}

/*
-----------------------------------
CHECK BOTH TABLES
(adoption first, then foster)
-----------------------------------
*/
$app = $conn->query("
    SELECT id, user_id, 'adoption' AS type
    FROM adoption_applications
    WHERE id = $id AND user_id = $user_id

    UNION

    SELECT id, user_id, 'foster' AS type
    FROM foster_applications
    WHERE id = $id AND user_id = $user_id

    LIMIT 1
")->fetch_assoc();

if (!$app) {
    die("Application not found!");
}

$type = $app['type'];

/*
-----------------------------------
UPDATE BASED ON TYPE
-----------------------------------
*/
if ($type == 'adoption') {

    $conn->query("
        UPDATE adoption_applications
        SET is_withdrawn = 1,
            status = 'withdrawn',
            updated_at = NOW()
        WHERE id = $id
    ");

} else {

    $conn->query("
        UPDATE foster_applications
        SET is_withdrawn = 1,
            status = 'withdrawn',
            updated_at = NOW()
        WHERE id = $id
    ");
}

header("Location: my-applications.php");
exit();
?>