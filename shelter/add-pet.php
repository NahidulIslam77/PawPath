<?php
session_start();

include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// ==========================
// LOGIN CHECK
// ==========================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ==========================
// ROLE CHECK
// ==========================
if ($_SESSION['role'] != 'shelter') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================
// GET SHELTER
// ==========================
$shelter = $conn->query("
    SELECT *
    FROM shelters
    WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    header("Location: complete-profile.php");
    exit();
}

$shelter_id = $shelter['id'];

$error = "";
$success = "";

// ==========================
// ADD PET
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $category = $_POST['category'];
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $activity_level = $_POST['activity_level'];
    $health_status = trim($_POST['health_status']);
    $foster_duration = trim($_POST['foster_duration']);
    $description = trim($_POST['description']);

    // ==========================
    // IMAGE UPLOAD
    // ==========================
    $image = "";

    if (!empty($_FILES['image']['name'])) {

        $image = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/" . $image
        );
    }

    // ==========================
    // INSERT
    // ==========================
    $stmt = $conn->prepare("
        INSERT INTO pets
        (
            shelter_id,
            owner_type,
            category,
            name,
            type,
            age,
            gender,
            activity_level,
            health_status,
            foster_duration,
            description,
            image
        )
        VALUES
        (?, 'shelter', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssissssss",
        $shelter_id,
        $category,
        $name,
        $type,
        $age,
        $gender,
        $activity_level,
        $health_status,
        $foster_duration,
        $description,
        $image
    );

    if ($stmt->execute()) {

        $success = "Pet added successfully! 🐾";

    } else {

        $error = "Something went wrong!";
    }
}
?>

<div class="bg-gray-50 min-h-screen px-6 md:px-12 py-10">

    <!-- PAGE TITLE -->
    <div class="mb-8">

        <h1 class="text-4xl font-bold text-[#2D6A4F]">
            Add New Pet 🐶
        </h1>

        <p class="text-gray-500 mt-2">
            Add pets for adoption or foster.
        </p>

    </div>


    <!-- ALERT -->
    <?php if ($error): ?>

        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl mb-6">
            <?= $error ?>
        </div>

    <?php endif; ?>

    <?php if ($success): ?>

        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-6">
            <?= $success ?>
        </div>

    <?php endif; ?>


    <!-- FORM -->
    <div class="bg-white p-8 rounded-2xl shadow-xl">

        <form method="POST"
              enctype="multipart/form-data"
              class="grid md:grid-cols-2 gap-6">

            <!-- CATEGORY -->
          <!-- CATEGORY -->
<div>

    <label class="block mb-2 font-semibold">
        Category
    </label>

    <select name="category"
            id="category"
            required
            class="w-full border px-4 py-3 rounded-xl">

        <option value="">
            Select Category
        </option>

        <option value="adoption">
            Adoption
        </option>

        <option value="foster">
            Foster
        </option>

    </select>

</div>


<!-- FOSTER DURATION -->
<div id="fosterDurationBox">

    <label class="block mb-2 font-semibold">
        Foster Duration
    </label>

    <input type="text"
           name="foster_duration"
           placeholder="2 Weeks / 1 Month"
           class="w-full border px-4 py-3 rounded-xl">

</div>
<script>

const category = document.getElementById('category');
const fosterBox = document.getElementById('fosterDurationBox');

function toggleFosterField() {

    if (category.value === 'adoption') {

        fosterBox.style.display = 'none';

    } else {

        fosterBox.style.display = 'block';
    }
}

// RUN ON CHANGE
category.addEventListener('change', toggleFosterField);

// RUN ON PAGE LOAD
toggleFosterField();

</script>


            <!-- NAME -->
            <div>

                <label class="block mb-2 font-semibold">
                    Pet Name
                </label>

                <input type="text"
                       name="name"
                       required
                       class="w-full border px-4 py-3 rounded-xl">

            </div>


            <!-- TYPE -->
            <div>

                <label class="block mb-2 font-semibold">
                    Pet Type
                </label>

                <select name="type"
                        required
                        class="w-full border px-4 py-3 rounded-xl">

                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Bird">Bird</option>
                    <option value="Rabbit">Rabbit</option>
                    <option value="Other">Other</option>

                </select>

            </div>


            <!-- AGE -->
            <div>

                <label class="block mb-2 font-semibold">
                    Age
                </label>

                <input type="text"
       name="age"
       placeholder="2 months / 1 year"
       class="w-full border px-4 py-3 rounded-xl">
            </div>


            <!-- GENDER -->
            <div>

                <label class="block mb-2 font-semibold">
                    Gender
                </label>

                <select name="gender"
                        required
                        class="w-full border px-4 py-3 rounded-xl">

                    <option value="Male">Male</option>
                    <option value="Female">Female</option>

                </select>

            </div>


            <!-- ACTIVITY -->
            <div>

                <label class="block mb-2 font-semibold">
                    Activity Level
                </label>

                <select name="activity_level"
                        required
                        class="w-full border px-4 py-3 rounded-xl">

                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>

                </select>

            </div>


            <!-- HEALTH -->
            <div>

                <label class="block mb-2 font-semibold">
                    Health Status
                </label>

                <input type="text"
                       name="health_status"
                       placeholder="Healthy / Vaccinated"
                       class="w-full border px-4 py-3 rounded-xl">

            </div>


            
           
            <!-- IMAGE -->
            <div class="md:col-span-2">

                <label class="block mb-2 font-semibold">
                    Pet Image
                </label>

                <input type="file"
                       name="image"
                       required
                       class="w-full border px-4 py-3 rounded-xl">

            </div>


            <!-- DESCRIPTION -->
            <div class="md:col-span-2">

                <label class="block mb-2 font-semibold">
                    Description
                </label>

                <textarea name="description"
                          rows="5"
                          required
                          class="w-full border px-4 py-3 rounded-xl"></textarea>

            </div>


            <!-- BUTTON -->
            <div class="md:col-span-2">

                <button type="submit"
                        class="bg-[#2D6A4F] hover:bg-[#40916c] text-white px-8 py-3 rounded-xl font-semibold transition">

                    Add Pet

                </button>

            </div>

        </form>

    </div>

</div>

<?php include '../includes/footer.php'; ?>