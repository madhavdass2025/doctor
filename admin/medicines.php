<?php
session_start();
// This could be a more robust check, e.g., checking for an admin role
if (!isset($_SESSION['did'])) {
    header("Location: /login.php"); // Redirect to a login page if not logged in
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all medicines from the database
$result = $conn->query("SELECT * FROM medicines ORDER BY name ASC");
$medicines = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Medicines';
$active_page = 'medicines'; // For highlighting the active link in the sidebar
include 'includes/header.php';
// The sidebar is included within the header
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Medicines</h2>
    <a href="medicine_form.php" class="btn btn-success">Add New Medicine</a>
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
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($medicines) > 0): ?>
            <?php foreach ($medicines as $medicine): ?>
                <tr>
                    <td><?= htmlspecialchars($medicine['name']) ?></td>
                    <td><?= htmlspecialchars($medicine['type']) ?></td>
                    <td><?= htmlspecialchars($medicine['UnitPrice']) ?></td>
                    <td><?= htmlspecialchars($medicine['status']) ?></td>
                    <td class="action-links">
                        <a href="medicine_form.php?id=<?= $medicine['Mid'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_medicine.php?id=<?= $medicine['Mid'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this medicine?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">No medicines found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
