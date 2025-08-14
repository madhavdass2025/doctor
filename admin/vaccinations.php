<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch all vaccinations
$result = $conn->query("SELECT * FROM vaccination ORDER BY name ASC");
$vaccinations = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Vaccinations';
$active_page = 'vaccinations';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Vaccinations</h2>
    <a href="vaccination_form.php" class="btn btn-success">Add New Vaccination</a>
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
            <th>Type</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($vaccinations) > 0): ?>
            <?php foreach ($vaccinations as $vaccination): ?>
                <tr>
                    <td><?= htmlspecialchars($vaccination['name']) ?></td>
                    <td><?= htmlspecialchars($vaccination['amount']) ?></td>
                    <td><?= htmlspecialchars($vaccination['type']) ?></td>
                    <td><?= htmlspecialchars($vaccination['duration']) ?></td>
                    <td class="action-links">
                        <a href="vaccination_form.php?id=<?= $vaccination['VId'] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_vaccination.php?id=<?= $vaccination['VId'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vaccination?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">No vaccinations found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>
