<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all doctors
$result = $conn->query("SELECT DoctorID, Name, Email, IsActive FROM doctors ORDER BY Name ASC");
$doctors = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Doctors';
$active_page = 'doctors';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Doctors</h2>
    <a href="doctor_form.php" class="btn btn-success">Add New Doctor</a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] == 'success' ? 'success' : 'danger' ?>">
        <?= $_SESSION['message'] ?>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    </div>
<?php endif; ?>

<table class="content-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($doctors) > 0): ?>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?= htmlspecialchars($doctor['Name']) ?></td>
                    <td><?= htmlspecialchars($doctor['Email']) ?></td>
                    <td><?= $doctor['IsActive'] ? 'Active' : 'Inactive' ?></td>
                    <td class="action-links">
                        <a href="doctor_form.php?id=<?= $doctor['DoctorID'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_doctor.php?id=<?= $doctor['DoctorID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this doctor?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">No doctors found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
