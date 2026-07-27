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
$pet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pet = $conn->query("
    SELECT *
    FROM pets
    WHERE id = $pet_id
    AND category='foster'
    AND is_deleted=0
")->fetch_assoc();

if (!$pet) {
    die("Pet not found!");
}

$check = $conn->query("
    SELECT id
    FROM foster_applications
    WHERE user_id=$user_id
    AND pet_id=$pet_id
")->num_rows;

if ($check > 0) {
    die("Already Applied!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $address = $_POST['address'];
    $duration = $_POST['duration'];
    $living_situation = $_POST['living_situation'];
    $experience = $_POST['experience'];

    $stmt = $conn->prepare("
        INSERT INTO foster_applications
        (
            user_id,
            pet_id,
            full_name,
            email,
            phone,
            address,
            duration,
            living_situation,
            experience
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iisssssss",
        $user_id,
        $pet_id,
        $full_name,
        $email,
        $phone,
        $address,
        $duration,
        $living_situation,
        $experience
    );

    $stmt->execute();

    header("Location: my-applications.php");
    exit();
}
?>
<main class="max-w-full mx-auto px-6 md:px-16 py-24 bg-gray-50 min-h-screen">
<div class="flex flex-col lg:flex-row gap-16 items-start">
    
   <div class="w-full lg:w-1/3">

            <div class="sticky top-28">

                <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
                    Foster
                    <span class="text-[#6B8E23]">
                        <?= htmlspecialchars($pet['name']) ?>
                    </span>
                </h1>

                <p class="text-slate-500 mt-6 text-lg">
                    Complete the Foster application form carefully.
                    This helps shelters/users find the temporary home for pets 🐾
                </p>

                <!-- PET CARD -->
                <div class="mt-10 bg-white rounded-[2rem] overflow-hidden shadow-xl border">

                    <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                         class="w-full h-72 object-cover">

                    <div class="p-6">

                        <div class="flex justify-between items-center">

                            <h2 class="text-3xl font-bold text-gray-800">
                                <?= htmlspecialchars($pet['name']) ?>
                            </h2>

                            <span class="bg-[#d9e3c2] text-[#355E3B] px-4 py-2 rounded-xl text-sm font-bold">
                                <?= ucfirst($pet['status']) ?>
                            </span>

                        </div>

                        <div class="mt-5 space-y-2 text-gray-600">

                            <p>🐾 Type: <?= $pet['type'] ?></p>

                            <p>📅 Age: <?= $pet['age'] ?></p>

                            <p>⚥ Gender: <?= $pet['gender'] ?></p>

                        </div>

                    </div>

                </div>

            </div>

    </div>

    <div class="w-full lg:w-2/3 bg-[#D9D9D9] p-8 md:p-12 rounded-[3rem] shadow-xl border border-slate-300">

        <h1 class="text-3xl font-bold mb-8 text-[#1a5a82]">
            Foster Application Form
        </h1>

        <form method="POST" class="space-y-6">

            <div>

                <label class="font-semibold">
                    Pet Name
                </label>

                <input type="text"
                       value="<?= htmlspecialchars($pet['name']) ?>"
                       readonly
                       class="w-full p-4 rounded-xl bg-gray-100">

            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <div>

                    <label>Full Name</label>

                    <input type="text"
                           name="full_name"
                           required
                           class="w-full p-4 rounded-xl">

                </div>

                <div>

                    <label>Email</label>

                    <input type="email"
                           name="email"
                           required
                           class="w-full p-4 rounded-xl">

                </div>

            </div>

            <div>

                <label>Phone Number</label>

                <input type="text"
                       name="phone"
                       required
                       class="w-full p-4 rounded-xl">

            </div>

            <div>

                <label>Address</label>

                <textarea name="address"
                          rows="3"
                          required
                          class="w-full p-4 rounded-xl"></textarea>

            </div>

            <div>

                <label>How Long Do You Want To Foster?</label>

                <input type="text"
                       name="duration"
                       required
                       class="w-full p-4 rounded-xl">

            </div>

            <div>

                <label>Living Situation</label>

                <select name="living_situation"
                        required
                        class="w-full p-4 rounded-xl">

                    <option value="">Select</option>
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                    <option value="Farm">Farm</option>
                    <option value="Other">Other</option>

                </select>

            </div>

            <div>

                <label>Previous Foster Experience</label>

                <textarea name="experience"
                          rows="5"
                          required
                          class="w-full p-4 rounded-xl"></textarea>

            </div>

            <button
                class="w-full bg-[#1a5a82] text-white py-4 rounded-xl text-lg font-bold">

                Submit Foster Application

            </button>

        </form>

    </div>

</div>
</main>