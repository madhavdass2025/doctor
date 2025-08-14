<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$vaccination = [
    'VId' => '',
    'name' => '',
    'amount' => '',
    'type' => '',
    'duration' => ''
];
$page_title = 'Add New Vaccination';
$form_action = 'save_vaccination.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $vaccination_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM vaccination WHERE VId = ?");
    $stmt->bind_param("i", $vaccination_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $vaccination = $result->fetch_assoc();
        $page_title = 'Edit Vaccination';
    }
    $stmt->close();
}

$active_page = 'vaccinations';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <input type="hidden" name="VId" value="<?= htmlspecialchars($vaccination['VId']) ?>">

        <div class="form-group">
            <label for="name">Vaccination Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($vaccination['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" class="form-control" value="<?= htmlspecialchars($vaccination['amount']) ?>" required>
        </div>

        <div class="form-group">
            <label for="type">Type (e.g., IM, SC)</label>
            <input type="text" id="type" name="type" class="form-control" value="<?= htmlspecialchars($vaccination['type']) ?>">
        </div>

        <div class="form-group">
            <label for="duration">Duration (e.g., 1 Year)</label>
            <input type="text" id="duration" name="duration" class="form-control" value="<?= htmlspecialchars($vaccination['duration']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save Vaccination</button>
        <a href="vaccinations.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
