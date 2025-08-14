<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['id'])) {
    $scan_id = (int)$_GET['id'];

    // Check if the scan is in use in consultations
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM consultation_scans WHERE ScanID = ?");
    $check_stmt->bind_param("i", $scan_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($result['count'] > 0) {
        $_SESSION['message'] = "Error: Cannot delete scan because it has been used in past consultations.";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM scan WHERE sID = ?");
        $stmt->bind_param("i", $scan_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Scan deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No scan found with that ID.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Error deleting scan: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $conn->close();

} else {
    $_SESSION['message'] = "No scan ID provided for deletion.";
    $_SESSION['message_type'] = "error";
}

header("Location: scans.php");
exit();
?>
