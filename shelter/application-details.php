<?php
session_start();

include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* Shelter ID */
$shelter = $conn->query("
    SELECT id
    FROM shelters
    WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    die("Shelter profile not found.");
}

$shelter_id = (int)$shelter['id'];

/* Get Application ID & Type */
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = ($_GET['type'] ?? 'adoption') === 'foster'
    ? 'foster'
    : 'adoption';

$table = ($type === 'foster')
    ? 'foster_applications'
    : 'adoption_applications';

/* Fetch Application Securely */
$app = $conn->query("
    SELECT
        a.*,

        p.name AS pet_name,
        p.type AS pet_type,
        p.age AS pet_age,
        p.image,
        p.shelter_id,

        CASE
            WHEN a.is_withdrawn = 1 THEN 'withdrawn'
            ELSE a.status
        END AS display_status

    FROM $table a

    JOIN pets p ON p.id = a.pet_id

    WHERE a.id = $id
    AND p.shelter_id = $shelter_id

    LIMIT 1
")->fetch_assoc();

if (!$app) {
    die("Application not found or unauthorized access!");
}

/* Update Status */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($app['is_withdrawn']) {
        die("This application has already been withdrawn.");
    }

    $status   = trim($_POST['status'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');

    if (!in_array($status, ['pending', 'approved', 'rejected'])) {
        die("Invalid status.");
    }

    if ($status === 'rejected' && empty($feedback)) {
        die("Feedback is required when rejecting.");
    }

    $stmt = $conn->prepare("
        UPDATE $table
        SET
            status = ?,
            feedback = ?,
            reviewed_by = ?,
            reviewed_at = NOW()
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssii",
        $status,
        $feedback,
        $user_id,
        $id
    );
$stmt->execute();

/* ===============================
   AUTO REJECT OTHER APPLICATIONS
   WHEN APPROVED
=============================== */

if ($status === 'approved') {

    $pet_id = (int)$app['pet_id'];

    // ===============================
    // AUTO UPDATE PET STATUS
    // ===============================
    $new_pet_status = ($type === 'foster') ? 'fostered' : 'adopted';

    $conn->query("
        UPDATE pets
        SET status = '$new_pet_status', updated_at = NOW()
        WHERE id = $pet_id
    ");

    // ===============================
    // AUTO REJECT OTHER APPLICATIONS
    // ===============================
    $auto_feedback = "Automatically rejected because another application for this pet was approved.";

    $conn->query("
        UPDATE $table
        SET
            status = 'rejected',
            feedback = '$auto_feedback',
            reviewed_by = $user_id,
            reviewed_at = NOW()
        WHERE pet_id = $pet_id
        AND id != $id
        AND status = 'pending'
    ");

} elseif ($status === 'pending') {

    // ===============================
    // RESET PET STATUS TO AVAILABLE
    // IF MOVED BACK TO PENDING
    // ===============================
    $pet_id = (int)$app['pet_id'];

    $conn->query("
        UPDATE pets
        SET status = 'available', updated_at = NOW()
        WHERE id = $pet_id
    ");
}

header("Location: pet-applications.php");
exit();
}

/* Status Color */
$statusClass = match ($app['display_status']) {
    'approved'  => 'bg-green-500',
    'rejected'  => 'bg-red-500',
    'withdrawn' => 'bg-gray-600',
    default     => 'bg-yellow-500'
};
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <h1 class="text-2xl font-bold mb-6 text-[#2D6A4F]">
        Application Details 📄
    </h1>

    <div class="bg-white  rounded-2xl shadow p-6 max-w-3xl mx-auto">

        <!-- Pet Info -->
        <div class="flex gap-4 items-center mb-6">

            <img
                src="../assets/images/<?= htmlspecialchars($app['image']) ?>"
                class="w-24 h-24 object-cover rounded-xl"
                alt="Pet"
            >

            <div>
                <h2 class="text-xl font-bold">
                    <?= htmlspecialchars($app['pet_name']) ?>
                </h2>

                <p class="text-sm text-gray-500">
                    <?= htmlspecialchars($app['pet_type']) ?>
                    •
                    <?= (int)$app['pet_age'] ?> years
                </p>
            </div>

        </div>

        <hr class="mb-5">

        <!-- Application Info -->
        <div class="space-y-3 text-sm">

            <p>
                <b>Application Type:</b>
                <?= ucfirst($type) ?>
            </p>

            <p>
                <b>Status:</b>

                <span class="px-3 py-1 rounded-full text-white text-xs <?= $statusClass ?>">
                    <?= ucfirst($app['display_status']) ?>
                </span>
            </p>

            <p>
                <b>Applied At:</b>
                <?= htmlspecialchars($app['created_at']) ?>
            </p>

            <hr>

            <h3 class="font-bold text-base">
                Applicant Information
            </h3>

            <p>
                <b>Full Name:</b>
                <?= htmlspecialchars($app['full_name'] ?? 'N/A') ?>
            </p>

            <p>
                <b>Email:</b>
                <?= htmlspecialchars($app['email'] ?? 'N/A') ?>
            </p>

            <p>
                <b>Phone:</b>
                <?= htmlspecialchars($app['phone'] ?? 'N/A') ?>
            </p>

            <p>
                <b>Address:</b>
                <?= nl2br(htmlspecialchars($app['address'] ?? 'N/A')) ?>
            </p>

            <?php if (!empty($app['living_situation'])): ?>
                <p>
                    <b>Living Situation:</b>
                    <?= htmlspecialchars($app['living_situation']) ?>
                </p>
            <?php endif; ?>

            <?php if (isset($app['previous_adoption'])): ?>
                <p>
                    <b>Previous Adoption:</b>
                    <?= $app['previous_adoption'] ? 'Yes' : 'No' ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($app['duration'])): ?>
                <p>
                    <b>Foster Duration:</b>
                    <?= htmlspecialchars($app['duration']) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($app['why_adopt'])): ?>
                <p>
                    <b>Why Adopt:</b><br>
                    <?= nl2br(htmlspecialchars($app['why_adopt'])) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($app['experience'])): ?>
                <p>
                    <b>Experience:</b><br>
                    <?= nl2br(htmlspecialchars($app['experience'])) ?>
                </p>
            <?php endif; ?>

            <hr>

            <p>
                <b>Shelter Feedback:</b><br>

                <?= !empty($app['feedback'])
                    ? nl2br(htmlspecialchars($app['feedback']))
                    : 'No feedback yet'; ?>
            </p>

        </div>

        <!-- Withdrawn -->
        <?php if ($app['is_withdrawn']): ?>

            <div class="mt-6 bg-gray-100 border rounded-lg p-4">
                <p class="font-semibold text-gray-700">
                    This application has been withdrawn by the applicant.
                </p>
            </div>

        <?php else: ?>

            <!-- Status Update Form -->
            <form method="POST" class="mt-6 space-y-4">

                <label class="block font-semibold">
                    Update Application Status
                </label>

                <select
                    name="status"
                    id="status"
                    class="w-full border p-3 rounded-lg"
                    required
                >
                    <option value="pending"
                        <?= $app['status'] === 'pending' ? 'selected' : '' ?>>
                        Pending
                    </option>

                    <option value="approved"
                        <?= $app['status'] === 'approved' ? 'selected' : '' ?>>
                        Approved
                    </option>

                    <option value="rejected"
                        <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>
                        Rejected
                    </option>
                </select>

                <textarea
                    name="feedback"
                    id="feedback"
                    class="w-full border p-3 rounded-lg"
                    rows="4"
                    placeholder="Required when rejecting..."
                ><?= htmlspecialchars($app['feedback'] ?? '') ?></textarea>

                <button
                    type="submit"
                    class="w-full bg-[#2D6A4F] text-white py-3 rounded-lg font-semibold"
                >
                    Save Changes
                </button>

            </form>

        <?php endif; ?>

        <a
            href="pet-applications.php"
            class="inline-block mt-6 text-gray-600 hover:text-black"
        >
            ← Back to Applications
        </a>

    </div>

</div>

<script>
document.querySelector("form")?.addEventListener("submit", function(e){

    const status = document.getElementById("status").value;
    const feedback = document.getElementById("feedback").value.trim();

    if (status === "rejected" && feedback === "") {
        e.preventDefault();
        alert("Feedback is required when rejecting an application.");
    }

});
</script>

<?php include '../includes/footer.php'; ?>
```