<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$surgery = [
    'surgeryID' => '',
    'surName' => '',
    'surAmount' => ''
];
$page_title = 'Add New Surgery';
$form_action = 'save_surgery.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $surgery_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM surgery WHERE surgeryID = ?");
    $stmt->bind_param("i", $surgery_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $surgery = $result->fetch_assoc();
        $page_title = 'Edit Surgery';
    }
    $stmt->close();
}

$active_page = 'surgeries';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <input type="hidden" name="surgeryID" value="<?= htmlspecialchars($surgery['surgeryID']) ?>">

        <div class="form-group">
            <label for="surName">Surgery Name</label>
            <input type="text" id="surName" name="surName" class="form-control" value="<?= htmlspecialchars($surgery['surName']) ?>" required>
        </div>

        <div class="form-group">
            <label for="surAmount">Amount</label>
            <input type="text" id="surAmount" name="surAmount" class="form-control" value="<?= htmlspecialchars($surgery['surAmount']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Surgery</button>
        <a href="surgeries.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
