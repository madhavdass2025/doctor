<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$scan = [
    'sID' => '',
    'scanName' => '',
    'amount' => ''
];
$page_title = 'Add New Scan';
$form_action = 'save_scan.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $scan_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM scan WHERE sID = ?");
    $stmt->bind_param("i", $scan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $scan = $result->fetch_assoc();
        $page_title = 'Edit Scan';
    }
    $stmt->close();
}

$active_page = 'scans';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <input type="hidden" name="sID" value="<?= htmlspecialchars($scan['sID']) ?>">

        <div class="form-group">
            <label for="scanName">Scan Name</label>
            <input type="text" id="scanName" name="scanName" class="form-control" value="<?= htmlspecialchars($scan['scanName']) ?>" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" class="form-control" value="<?= htmlspecialchars($scan['amount']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Scan</button>
        <a href="scans.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
