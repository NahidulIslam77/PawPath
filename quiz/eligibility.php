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

$questions = $conn->query("
    SELECT * FROM quiz_questions 
    ORDER BY display_order ASC
");
?>

<div class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center p-3 bg-green-100 rounded-2xl text-[#2D6A4F] mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Eligibility Quiz</h1>
            <p class="mt-2 text-sm text-gray-600 max-w-md mx-auto">
                Please answer the following questions accurately to evaluate your eligibility for pet adoption or fostering.
            </p>
        </div>

        <form method="POST" action="submit_eligibility.php" class="space-y-6">

            <?php 
            $count = 1;
            while($q = $questions->fetch_assoc()): 
                $q_id = $q['id'];
            ?>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 transition-all duration-200 hover:shadow-md">
                    
                    <div class="flex items-start gap-3 mb-5">
                        <span class="flex items-center justify-center bg-gray-100 text-gray-700 font-bold text-sm w-7 h-7 rounded-lg shrink-0 mt-0.5">
                            <?= $count++ ?>
                        </span>
                        <p class="text-lg font-medium text-gray-800 pt-0.5">
                            <?= htmlspecialchars($q['question_text']) ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <label class="relative flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 group">
                            <input type="radio" name="q<?= $q_id ?>" value="1" required
                                   class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                            <span class="ml-3 font-medium text-gray-700 group-has-[:checked]:text-green-800">Yes</span>
                        </label>

                        <label class="relative flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50 group">
                            <input type="radio" name="q<?= $q_id ?>" value="0"
                                   class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                            <span class="ml-3 font-medium text-gray-700 group-has-[:checked]:text-red-800">No</span>
                        </label>

                    </div>

                </div>

            <?php endwhile; ?>

            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="w-full sm:w-auto bg-[#2D6A4F] hover:bg-[#1f513b] text-white font-semibold px-8 py-3.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Submit My Answers
                </button>
            </div>

        </form>

    </div>
</div>

<?php include '../includes/footer.php'; ?>