<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all lab tests
$result = $conn->query("SELECT * FROM laboratory ORDER BY name ASC");
$lab_tests = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Lab Tests';
$active_page = 'lab_tests';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Lab Tests</h2>
    <a href="lab_test_form.php" class="btn btn-success">Add New Lab Test</a>
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
        <?php if (count($lab_tests) > 0): ?>
            <?php foreach ($lab_tests as $test): ?>
                <tr>
                    <td><?= htmlspecialchars($test['name']) ?></td>
                    <td><?= htmlspecialchars($test['amount']) ?></td>
                    <td class="action-links">
                        <a href="lab_test_form.php?id=<?= $test['Lid'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_lab_test.php?id=<?= $test['Lid'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this lab test?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">No lab tests found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
