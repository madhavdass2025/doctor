<?php
session_start();
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

require_once '../includes/db_connect.php';

$medicine = [
    'Mid' => '',
    'name' => '',
    'type' => '',
    'description' => '',
    'UnitPrice' => '',
    'taxExcluded_price' => '',
    'taxAmount' => '',
    'hsn' => '',
    'taxable' => 'yes',
    'Itax' => '',
    'cess' => '',
    'status' => 'available'
];
$form_action = 'save_medicine.php';
$page_title = 'Add New Medicine';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $medicine_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM medicines WHERE Mid = ?");
    $stmt->bind_param("i", $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $medicine = $result->fetch_assoc();
        $page_title = 'Edit Medicine';
    }
    $stmt->close();
}

$active_page = 'medicines';
include 'includes/header.php';
?>

<div class="form-container">
    <form action="<?= $form_action ?>" method="POST">
        <!-- Hidden field for the ID, crucial for updates -->
        <input type="hidden" name="Mid" value="<?= htmlspecialchars($medicine['Mid']) ?>">

        <div class="form-group">
            <label for="name">Medicine Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($medicine['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="type">Type (e.g., Tablet, Syrup)</label>
            <input type="text" id="type" name="type" class="form-control" value="<?= htmlspecialchars($medicine['type']) ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($medicine['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="UnitPrice">Unit Price (including tax)</label>
            <input type="text" id="UnitPrice" name="UnitPrice" class="form-control" value="<?= htmlspecialchars($medicine['UnitPrice']) ?>" required>
        </div>

        <div class="form-group">
            <label for="taxExcluded_price">Price (excluding tax)</label>
            <input type="text" id="taxExcluded_price" name="taxExcluded_price" class="form-control" value="<?= htmlspecialchars($medicine['taxExcluded_price']) ?>" required>
        </div>

        <div class="form-group">
            <label for="taxAmount">Tax Amount</label>
            <input type="text" id="taxAmount" name="taxAmount" class="form-control" value="<?= htmlspecialchars($medicine['taxAmount']) ?>" required>
        </div>

        <div class="form-group">
            <label for="hsn">HSN Code</label>
            <input type="text" id="hsn" name="hsn" class="form-control" value="<?= htmlspecialchars($medicine['hsn']) ?>" required>
        </div>

        <div class="form-group">
            <label for="Itax">IGST (%)</label>
            <input type="text" id="Itax" name="Itax" class="form-control" value="<?= htmlspecialchars($medicine['Itax']) ?>" required>
        </div>

        <div class="form-group">
            <label for="cess">CESS (%)</label>
            <input type="text" id="cess" name="cess" class="form-control" value="<?= htmlspecialchars($medicine['cess']) ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="available" <?= $medicine['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                <option value="unavailable" <?= $medicine['status'] == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Medicine</button>
        <a href="medicines.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>
