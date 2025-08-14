<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the form was submitted and the user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $mid = (int)$_POST['Mid'];
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $unitPrice = trim($_POST['UnitPrice']);
    $taxExcluded_price = trim($_POST['taxExcluded_price']);
    $taxAmount = trim($_POST['taxAmount']);
    $hsn = trim($_POST['hsn']);
    $itax = trim($_POST['Itax']);
    $cess = trim($_POST['cess']);
    $status = trim($_POST['status']);
    $submittedby = $_SESSION['did']; // Assuming doctor/admin ID is in session

    // Validate required fields
    if (empty($name) || empty($unitPrice)) {
        $_SESSION['message'] = "Medicine Name and Unit Price are required.";
        $_SESSION['message_type'] = "error";
        header("Location: medicine_form.php" . ($mid ? "?id=$mid" : ""));
        exit();
    }

    if ($mid > 0) {
        // --- UPDATE existing record ---
        $sql = "UPDATE medicines SET name=?, type=?, description=?, UnitPrice=?, taxExcluded_price=?, taxAmount=?, hsn=?, Itax=?, cess=?, status=?, submittedby=? WHERE Mid=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", $name, $type, $description, $unitPrice, $taxExcluded_price, $taxAmount, $hsn, $itax, $cess, $status, $submittedby, $mid);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Medicine updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating medicine: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        $sql = "INSERT INTO medicines (name, type, description, UnitPrice, taxExcluded_price, taxAmount, hsn, Itax, cess, status, submittedby) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $name, $type, $description, $unitPrice, $taxExcluded_price, $taxAmount, $hsn, $itax, $cess, $status, $submittedby);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Medicine added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding medicine: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: medicines.php");
    exit();

} else {
    // If not a POST request or not logged in, redirect to the list page
    header("Location: medicines.php");
    exit();
}
?>
