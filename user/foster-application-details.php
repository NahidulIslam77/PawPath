<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'] == 'approved' ? 'approved' : 'rejected';
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
    
   
    $check_owner = $conn->query("
        SELECT fa.pet_id, p.name as pet_name 
        FROM foster_applications fa
        JOIN pets p ON fa.pet_id = p.id
        WHERE fa.id = $id AND p.user_id = $user_id
    ")->fetch_assoc();

    if ($check_owner) {
        $pet_id = (int)$check_owner['pet_id'];
        $pet_name = $conn->real_escape_string($check_owner['pet_name']);
        $feedback = $conn->real_escape_string($feedback);

        if ($action == 'rejected' && empty($feedback)) {
            echo "<script>alert('Please provide a reason for rejection.'); window.history.back();</script>";
            exit();
        }

        
        $update_main = $conn->query("UPDATE foster_applications SET status = '$action', feedback = '$feedback' WHERE id = $id");

        if ($update_main) {
          
            if ($action == 'approved') {
                $auto_reject_msg = $conn->real_escape_string("Thank you for your interest. Another applicant has been approved for fostering $pet_name.");
                
                $conn->query("
                    UPDATE foster_applications 
                    SET status = 'rejected', feedback = '$auto_reject_msg' 
                    WHERE pet_id = $pet_id 
                    AND id != $id 
                    AND status = 'pending'
                ");
            }
            
            echo "<script>alert('Application status updated successfully!'); window.location.href='foster-application-details.php?id=$id';</script>";
            exit();
        }
    } else {
        die("<div class='text-center py-10 font-semibold text-red-600'>Unauthorized action!</div>");
    }
}


$app = $conn->query("
    SELECT fa.*, p.name as pet_name, p.user_id as pet_owner_id
    FROM foster_applications fa
    JOIN pets p ON fa.pet_id = p.id
    WHERE fa.id = $id AND p.user_id = $user_id
")->fetch_assoc();


if (!$app) {
    die("<div class='text-center py-10 font-semibold text-red-600'>Application not found or you are not authorized to view this!</div>");
}
?>

<div class="max-w-3xl mx-auto py-10 px-4">

    <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100">

        <div class="border-b pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Application for Foster: <span class="text-[#2D6A4F]"><?= htmlspecialchars($app['pet_name']) ?></span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Review the applicant's details below to make a decision.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 mb-6">
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Applicant's Name</p>
                <p class="font-medium text-base mt-0.5"><?= htmlspecialchars($app['full_name']) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Email Address</p>
                <p class="font-medium text-base mt-0.5"><?= htmlspecialchars($app['email']) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Phone Number</p>
                <p class="font-medium text-base mt-0.5"><?= htmlspecialchars($app['phone']) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Foster Duration</p>
                <p class="font-medium text-base mt-0.5 bg-green-50 text-green-700 inline-block px-2 py-0.5 rounded-md text-sm"><?= htmlspecialchars($app['duration']) ?></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Living Situation</p>
                <p class="font-medium text-base mt-0.5"><?= htmlspecialchars($app['living_situation']) ?></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Address</p>
                <p class="font-medium text-base mt-0.5"><?= htmlspecialchars($app['address']) ?></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Experience Description</p>
                <p class="font-medium text-base mt-0.5 bg-gray-50 p-3 rounded-xl border border-gray-100"><?= nl2br(htmlspecialchars($app['experience'])) ?></p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t flex items-center justify-between">
            <span class="font-semibold text-gray-700">Current Application Status:</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                <?= $app['status'] == 'approved' ? 'bg-green-100 text-green-800' : ($app['status'] == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                <?= $app['status'] ?>
            </span>
        </div>

        <!-- REJECTED FEEDBACK BOX -->
        <?php if ($app['status'] == 'rejected' && !empty($app['feedback'])): ?>
            <div class="mt-4 bg-red-50 border border-red-100 p-4 rounded-xl text-sm text-red-700">
                <b>Your Feedback/Reason:</b> <?= htmlspecialchars($app['feedback']) ?>
            </div>
        <?php endif; ?>

      
        <?php if ($app['status'] == 'pending'): ?>
           
            <form method="POST" action="" class="mt-6 pt-6 border-t">
                <h3 class="text-lg font-bold mb-3 text-gray-800">Take Action on this Application</h3>
                
                <label class="block text-sm font-medium text-gray-600 mb-1">Feedback (Required if rejecting):</label>
                <textarea name="feedback"
                          class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-[#2D6A4F] outline-none transition"
                          rows="3"
                          placeholder="Write a message or reason for rejection..."></textarea>

                <div class="flex gap-4 mt-4">
                    <button name="action" value="approved"
                            onclick="return confirm('Approving this will automatically REJECT all other pending applications for this pet. Proceed?')"
                            class="flex-1 bg-[#2D6A4F] hover:bg-[#1B4332] text-white font-medium py-3 rounded-xl transition shadow-sm">
                        Accept & Approve
                    </button>

                    <button name="action" value="rejected"
                            onclick="return confirm('Are you sure you want to REJECT this applicant?')"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-xl transition shadow-sm">
                        Reject Application
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>