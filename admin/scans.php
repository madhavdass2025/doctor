<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all scans
$result = $conn->query("SELECT * FROM scan ORDER BY scanName ASC");
$scans = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Scans';
$active_page = 'scans';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Scans</h2>
    <a href="scan_form.php" class="btn btn-success">Add New Scan</a>
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
        <?php if (count($scans) > 0): ?>
            <?php foreach ($scans as $scan): ?>
                <tr>
                    <td><?= htmlspecialchars($scan['scanName']) ?></td>
                    <td><?= htmlspecialchars($scan['amount']) ?></td>
                    <td class="action-links">
                        <a href="scan_form.php?id=<?= $scan['sID'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_scan.php?id=<?= $scan['sID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this scan?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">No scans found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
