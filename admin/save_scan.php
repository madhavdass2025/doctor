<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $sid = (int)$_POST['sID'];
    $name = trim($_POST['scanName']);
    $amount = trim($_POST['amount']);
    $submittedby = $_SESSION['did'];

    if (empty($name) || empty($amount)) {
        $_SESSION['message'] = "Scan Name and Amount are required.";
        $_SESSION['message_type'] = "error";
        header("Location: scan_form.php" . ($sid ? "?id=$sid" : ""));
        exit();
    }

    if ($sid > 0) {
        // --- UPDATE existing record ---
        $sql = "UPDATE scan SET scanName=?, amount=?, submittedby=? WHERE sID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $amount, $submittedby, $sid);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Scan updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating scan: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        $sql = "INSERT INTO scan (scanName, amount, submittedby) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $amount, $submittedby);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Scan added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding scan: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: scans.php");
    exit();

} else {
    header("Location: scans.php");
    exit();
}
?>
