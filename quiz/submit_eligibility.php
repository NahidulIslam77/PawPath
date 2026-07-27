<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$score = 0;

// GET QUESTIONS
$questions = $conn->query("SELECT * FROM quiz_questions");

while ($q = $questions->fetch_assoc()) {

    $qid = $q['id'];
    $user_ans = $_POST["q$qid"] ?? 0;

    if ((int)$user_ans == (int)$q['correct_answer']) {
        $score++;
    }
}

$is_eligible = ($score >= 4) ? 1 : 0;

// SAVE ATTEMPT
$conn->query("
    INSERT INTO eligibility_attempts (user_id, score, is_eligible)
    VALUES ($user_id, $score, $is_eligible)
");

// UPDATE USER
$conn->query("
    UPDATE users SET is_eligible=$is_eligible WHERE id=$user_id
");

// REDIRECT
if ($is_eligible) {
    header("Location: ../user/dashboard.php");
    exit();
}else {
    header("Location: ../user/not-eligible.php");
}
exit();