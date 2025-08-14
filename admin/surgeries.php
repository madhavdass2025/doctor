<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all surgeries
$result = $conn->query("SELECT * FROM surgery ORDER BY surName ASC");
$surgeries = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Surgeries';
$active_page = 'surgeries';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Surgeries</h2>
    <a href="surgery_form.php" class="btn btn-success">Add New Surgery</a>
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
            <th>Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($surgeries) > 0): ?>
            <?php foreach ($surgeries as $surgery): ?>
                <tr>
                    <td><?= htmlspecialchars($surgery['surName']) ?></td>
                    <td><?= htmlspecialchars($surgery['surAmount']) ?></td>
                    <td class="action-links">
                        <a href="surgery_form.php?id=<?= $surgery['surgeryID'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_surgery.php?id=<?= $surgery['surgeryID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this surgery?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">No surgeries found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
