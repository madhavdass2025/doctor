<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$lab_test = [
    'Lid' => '',
    'name' => '',
    'amount' => ''
];
$page_title = 'Add New Lab Test';
$form_action = 'save_lab_test.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $lab_test_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM laboratory WHERE Lid = ?");
    $stmt->bind_param("i", $lab_test_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $lab_test = $result->fetch_assoc();
        $page_title = 'Edit Lab Test';
    }
    $stmt->close();
}

$active_page = 'lab_tests';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <input type="hidden" name="Lid" value="<?= htmlspecialchars($lab_test['Lid']) ?>">

        <div class="form-group">
            <label for="name">Test Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($lab_test['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" class="form-control" value="<?= htmlspecialchars($lab_test['amount']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Lab Test</button>
        <a href="lab_tests.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
