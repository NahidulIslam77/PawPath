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


// =========================
// GET PET
// =========================
if (!isset($_GET['id'])) {
    die("Pet ID missing!");
}

$pet_id = (int)$_GET['id'];

$pet = $conn->query("
    SELECT * FROM pets
    WHERE id = $pet_id
    AND category = 'adoption'
    AND is_deleted = 0
")->fetch_assoc();

if (!$pet) {
    die("Pet not found!");
}


// =========================
// ELIGIBILITY CHECK
// =========================
$eligible = $conn->query("
    SELECT is_eligible
    FROM eligibility_attempts
    WHERE user_id = $user_id
    ORDER BY id DESC
    LIMIT 1
")->fetch_assoc();

if (!$eligible || $eligible['is_eligible'] == 0) {
    header("Location: ../user/not-eligible.php");
    exit();
}


// =========================
$already = $conn->query("
    SELECT id, is_withdrawn
    FROM adoption_applications
    WHERE user_id = $user_id
    AND pet_id = $pet_id
    LIMIT 1
")->fetch_assoc();

if ($already) {

    // NOT withdrawn → block
    if ($already['is_withdrawn'] == 0) {
        die("You already applied for this pet.");
    }

    // withdrawn → allow re-apply (do nothing)
}


// =========================
// SUBMIT APPLICATION
// =========================
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $address = trim($_POST['address']);
    $living_situation = $_POST['living_situation'];
    $previous_adoption = isset($_POST['previous_adoption']) ? 1 : 0;
    $why_adopt = trim($_POST['why_adopt']);
    $preferred_date = $_POST['preferred_date'];

    $stmt = $conn->prepare("
        INSERT INTO adoption_applications (
            user_id,
            pet_id,
            full_name,
            email,
            phone,
            address,
            living_situation,
            previous_adoption,
            why_adopt,
            preferred_date
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iisssssiss",
        $user_id,
        $pet_id,
        $full_name,
        $email,
        $phone,
        $address,
        $living_situation,
        $previous_adoption,
        $why_adopt,
        $preferred_date
    );

    $stmt->execute();

    $success = "Application submitted successfully!";
}
?>

<main class="max-w-full mx-auto px-6 md:px-16 py-24 bg-gray-50 min-h-screen">

    <div class="flex flex-col lg:flex-row gap-16 items-start">

        <!-- LEFT -->
        <div class="w-full lg:w-1/3">

            <div class="sticky top-28">

                <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
                    Adopt
                    <span class="text-[#6B8E23]">
                        <?= htmlspecialchars($pet['name']) ?>
                    </span>
                </h1>

                <p class="text-slate-500 mt-6 text-lg">
                    Complete the adoption application form carefully.
                    This helps shelters find the perfect home for pets 🐾
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


        <!-- RIGHT -->
        <div class="w-full lg:w-2/3 bg-[#D9D9D9] p-8 md:p-12 rounded-[3rem] shadow-xl border border-slate-300">

            <h2 class="text-3xl font-bold text-[#1a5a82] mb-10">
                Pet Adoption Application Form
            </h2>

            <?php if ($success): ?>

                <div class="bg-green-100 text-green-700 px-6 py-4 rounded-2xl mb-8">
                    <?= $success ?>
                </div>

            <?php endif; ?>

            <form method="POST" class="space-y-8">

                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="space-y-2">

                        <label class="block text-sm font-bold text-slate-700 ml-2">
                            Full Name
                        </label>

                        <input type="text"
                               name="full_name"
                               required
                               placeholder="John Doe"
                               class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                    </div>

                    <div class="space-y-2">

                        <label class="block text-sm font-bold text-slate-700 ml-2">
                            Email Address
                        </label>

                        <input type="email"
                               name="email"
                               required
                               placeholder="john@example.com"
                               class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                    </div>

                    <div class="space-y-2">

                        <label class="block text-sm font-bold text-slate-700 ml-2">
                            Phone Number
                        </label>

                        <input type="text"
                               name="phone"
                               required
                               placeholder="+8801XXXXXXXXX"
                               class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                    </div>

                </div>


                <!-- ADDRESS -->
                <div class="space-y-2">

                    <label class="block text-sm font-bold text-slate-700 ml-2">
                        Home Address
                    </label>

                    <input type="text"
                           name="address"
                           required
                           placeholder="Street, City"
                           class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                </div>


                <!-- LIVING -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="space-y-2">

                        <label class="block text-sm font-bold text-slate-700 ml-2">
                            Living Situation
                        </label>

                        <select name="living_situation"
                                required
                                class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                            <option value="">Select Option</option>
                            <option value="Apartment">Apartment</option>
                            <option value="House">House</option>
                            <option value="Farm">Farm</option>
                            <option value="Other">Other</option>

                        </select>

                    </div>

                    <div class="space-y-2">

                        <label class="block text-sm font-bold text-slate-700 ml-2">
                            Preferred Adoption Date
                        </label>

                        <input type="date"
                               name="preferred_date"
                               required
                               class="w-full px-6 py-4 bg-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200">

                    </div>

                </div>


                <!-- PREVIOUS -->
                <div class="bg-white/50 p-6 rounded-2xl">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input type="checkbox"
                               name="previous_adoption"
                               class="w-5 h-5 accent-[#1a5a82]">

                        <span class="text-slate-700 font-medium">
                            I have adopted/fostered pets before
                        </span>

                    </label>

                </div>


                <!-- WHY -->
                <div class="space-y-2">

                    <label class="block text-sm font-bold text-slate-700 ml-2">
                        Why do you want to adopt?
                    </label>

                    <textarea rows="5"
                              name="why_adopt"
                              required
                              placeholder="Tell us why you'd be a great pet owner..."
                              class="w-full px-6 py-5 bg-white rounded-[2rem] outline-none resize-none focus:ring-4 focus:ring-blue-200"></textarea>

                </div>


                <!-- SUBMIT -->
                <button type="submit"
                        class="w-full md:w-auto bg-[#1a5a82] text-white text-lg font-bold px-16 py-4 rounded-2xl hover:bg-[#0d4566] transition shadow-lg">

                    Submit Application

                </button>

            </form>

        </div>

    </div>

</main>

<?php include '../includes/footer.php'; ?>