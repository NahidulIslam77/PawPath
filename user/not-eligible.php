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

// get latest eligibility attempt
$attempt = $conn->query("
    SELECT * FROM eligibility_attempts
    WHERE user_id = $user_id
    ORDER BY id DESC
    LIMIT 1
")->fetch_assoc();

$score = $attempt['score'] ?? 0;
?>

<!-- PAGE -->
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="bg-white w-full max-w-md p-10 rounded-2xl shadow-xl border border-gray-100 text-center">

        <!-- ICON -->
        <div class="text-6xl mb-4">
            ❌
        </div>

        <!-- TITLE -->
        <h1 class="text-3xl font-bold text-red-500 mb-3">
            Not Eligible for Adoption
        </h1>

        <!-- MESSAGE -->
        <p class="text-gray-600 text-sm mb-4 leading-relaxed">
            You are not eligible to adopt pets right now.
            But you can still foster pets and help animals in need.
        </p>

        <!-- SCORE INFO -->
        <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-left mb-6">

            <p class="text-sm font-semibold text-gray-700 mb-2">
                Your Result:
            </p>

            <p class="text-sm text-gray-600">
                You scored <b><?= $score ?>/6</b> in eligibility quiz.
            </p>

            <p class="text-sm font-semibold mt-3 text-gray-700">
                Requirements:
            </p>

            <ul class="text-sm text-gray-600 space-y-1 mt-1">
                <li>✔ Score at least 4/6</li>
                <li>✔ Basic pet care knowledge</li>
                <li>✔ Time & responsibility readiness</li>
            </ul>

        </div>

        <!-- BUTTONS -->
        <div class="space-y-3">

            <a href="../quiz/eligibility.php"
                class="block w-full bg-[#2D6A4F] text-white py-3 rounded-xl font-semibold hover:bg-[#40916c] transition">

                Retake Quiz

            </a>

            <a href="../user/foster-pets.php"
                class="block w-full border border-[#2D6A4F] text-[#2D6A4F] py-3 rounded-xl font-semibold hover:bg-green-50 transition">

                Explore Foster Pets

            </a>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>