<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// সেশন চেক (যদি রোল সেট করা না থাকে তবে শুধু user_id চেক করাই নিরাপদ)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// ইউআরএল থেকে নির্দিষ্ট pet_id নেওয়া হয়েছে (যদি থাকে)
$pet_id_filter = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;

// শেল্টার আইডি বের করা (প্রয়োজন হলে ব্যবহার করতে পারেন)
$shelter = $conn->query("SELECT id FROM shelters WHERE user_id = $user_id")->fetch_assoc();
$shelter_id = $shelter ? $shelter['id'] : 0;


$sql = "
    SELECT fa.*, p.name as pet_name, p.image
    FROM foster_applications fa
    JOIN pets p ON fa.pet_id = p.id
    WHERE p.user_id = $user_id
";

if ($pet_id_filter > 0) {
    $sql .= " AND fa.pet_id = $pet_id_filter";
}

$sql .= " ORDER BY fa.id DESC";
$applications = $conn->query($sql);
?>

<div class="px-6 py-10 bg-gray-50 min-h-screen">

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-[#2D6A4F]">
            Foster Applications 📩
        </h1>
        <?php if ($pet_id_filter > 0): ?>
            <a href="foster-applications.php" class="text-sm bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                View All Applications
            </a>
        <?php endif; ?>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        <?php if ($applications && $applications->num_rows > 0): ?>
            <?php while($a = $applications->fetch_assoc()): ?>

            <div class="bg-white p-5 rounded-2xl shadow hover:shadow-md transition">

                <div class="flex gap-4">

                    <img src="../assets/images/<?= htmlspecialchars($a['image']) ?>"
                         class="w-24 h-24 object-cover rounded-xl bg-gray-100">

                    <div class="flex flex-col justify-between">

                        <div>
                            <h2 class="text-xl font-bold text-gray-800">
                                <?= htmlspecialchars($a['pet_name']) ?>
                            </h2>

                            <p class="text-sm text-gray-600 mt-1">Status:
                                <span class="font-bold px-2 py-0.5 rounded text-xs
                                    <?= $a['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                                    <?= $a['status'] == 'approved' ? 'bg-green-100 text-green-700' : '' ?>
                                    <?= $a['status'] == 'rejected' ? 'bg-red-100 text-red-700' : '' ?>
                                ">
                                    <?= ucfirst(htmlspecialchars($a['status'])) ?>
                                </span>
                            </p>
                        </div>

                        <a href="foster-application-details.php?id=<?= $a['id'] ?>"
                           class="text-blue-600 font-semibold text-sm hover:underline mt-3 inline-block">
                           View Details 
                        </a>

                    </div>

                </div>

            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-2 text-center bg-white p-10 rounded-2xl shadow">
                <p class="text-gray-500 text-lg">No foster applications found.</p>
            </div>
        <?php endif; ?>

    </div>

</div>