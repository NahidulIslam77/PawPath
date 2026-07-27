<?php
include '../includes/header.php';
include '../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {

        // check email exists
        $check = $conn->prepare("SELECT id, role FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, role)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param("ssss", $name, $email, $hashed, $role);

            if ($stmt->execute()) {

                // ✅ সেশন তৈরি করা বন্ধ করা হয়েছে (সরাসরি লগইন হবে না)
                // ✅ লগইন পেজে রিডাইরেক্ট এবং ইউআরএল-এ একটি সাকসেস মেসেজ পাঠানো হয়েছে
                header("Location: login.php?success=Registration successful! Please login.");
                exit();

            } else {
                $error = "Something went wrong!";
            }
        }
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
        <a href="login.php" class="hover:text-[#2D6A4F]">Login</a>
    </div>

</nav>

<div class="flex flex-col md:flex-row min-h-screen">

    <div class="hidden md:block md:w-1/2 relative overflow-hidden">

        <img src="../assets/images/adopt.jpg" class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30"></div>

        <div class="absolute bottom-10 left-10 text-white">
            <h2 class="text-3xl font-bold">Join PawPath</h2>
            <p class="text-sm">Adopt, foster and give pets a better life.</p>
        </div>

    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center p-6">

        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">

            <h1 class="text-3xl font-bold mb-2">Create Account</h1>
            <p class="text-gray-500 mb-6 text-sm">
                Join PawPath and start your journey
            </p>

            <?php if ($error): ?>
                <p class="text-red-500 mb-3"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <input type="text" name="name" placeholder="Name"
                    class="w-full px-4 py-3 border rounded-xl" required>

                <input type="email" name="email" placeholder="Email"
                    class="w-full px-4 py-3 border rounded-xl" required>

                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-3 border rounded-xl" required>

                <input type="password" name="confirm_password" placeholder="Confirm Password"
                    class="w-full px-4 py-3 border rounded-xl" required>

                <button type="submit"
                    class="w-full bg-[#2D6A4F] text-white py-4 rounded-xl font-semibold hover:bg-[#40916c]">

                    Sign Up

                </button>

            </form>

            <p class="text-sm text-center mt-6">
                Already have an account?
                <a href="login.php" class="text-[#2D6A4F] font-semibold">Login</a>
            </p>

        </div>

    </div>

</div>