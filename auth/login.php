<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            $user_id = (int)$user['id'];

            // ADMIN
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit();
            }

            // SHELTER
            if ($user['role'] == 'shelter') {

                $checkShelter = $conn->query("
                    SELECT id 
                    FROM shelters
                    WHERE user_id = $user_id
                ");

                if ($checkShelter->num_rows == 0) {
                    header("Location: ../shelter/complete-profile.php");
                } else {
                    header("Location: ../shelter/dashboard.php");
                }
                exit();
            }

            // ELIGIBILITY CHECK
            $attempt = $conn->query("
                SELECT is_eligible 
                FROM eligibility_attempts 
                WHERE user_id = $user_id 
                ORDER BY id DESC 
                LIMIT 1
            ");

            $attempt = $attempt ? $attempt->fetch_assoc() : null;

            if (!$attempt) {
                header("Location: ../quiz/eligibility.php");
                exit();
            }

            if ((int)$attempt['is_eligible'] === 0) {
                header("Location: ../user/not-eligible.php");
                exit();
            }

            header("Location: ../user/dashboard.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>

<nav class="w-full bg-[#D9D9D9] px-6 py-4 flex justify-between items-center shadow-sm">
    <a href="../index.php" class="text-2xl font-serif flex items-baseline">
        <span class="text-[#6B8E23] italic">Paw</span>
        <span class="text-[#2B4C7E] ml-1">Path</span>
    </a>
    <div class="flex gap-6 text-sm font-medium">
        <a href="../index.php" class="hover:text-[#2D6A4F]">Home</a>
    </div>
</nav>

<div class="flex items-center justify-center min-h-screen px-4">

    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">

        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-[#2D6A4F]">
                Welcome Back 
            </h2>
            <p class="text-gray-500 mt-2">
                Login to continue to Pet Adoption System
            </p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded-xl mb-4 text-center text-sm font-medium">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded-xl mb-4 text-center text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#2D6A4F] hover:bg-[#1f513b] text-white py-3 rounded-xl font-semibold transition"
            >
                Login
            </button>

        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Don't have an account?
            <a href="register.php"
               class="text-[#2D6A4F] font-semibold hover:underline">
                Create Account
            </a>
        </div>

    </div>
</div>