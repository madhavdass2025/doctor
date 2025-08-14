<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$doctor = [
    'DoctorID' => '',
    'Name' => '',
    'Email' => '',
    'IsActive' => 1
];
$page_title = 'Add New Doctor';
$form_action = 'save_doctor.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $doctor_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT DoctorID, Name, Email, IsActive FROM doctors WHERE DoctorID = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        $page_title = 'Edit Doctor';
    }
    $stmt->close();
}

$active_page = 'doctors';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <input type="hidden" name="DoctorID" value="<?= htmlspecialchars($doctor['DoctorID']) ?>">

        <div class="form-group">
            <label for="Name">Full Name</label>
            <input type="text" id="Name" name="Name" class="form-control" value="<?= htmlspecialchars($doctor['Name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="Email">Email Address</label>
            <input type="email" id="Email" name="Email" class="form-control" value="<?= htmlspecialchars($doctor['Email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="Password">Password</label>
            <input type="password" id="Password" name="Password" class="form-control" placeholder="<?= $doctor['DoctorID'] ? 'Leave blank to keep current password' : 'Enter new password' ?>">
            <?php if ($doctor['DoctorID']): ?>
                <small>Leave this field blank to keep the password unchanged.</small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="IsActive">Status</label>
            <select id="IsActive" name="IsActive" class="form-control">
                <option value="1" <?= $doctor['IsActive'] == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= $doctor['IsActive'] == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Doctor</button>
        <a href="doctors.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
