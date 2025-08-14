<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in (and could add role check here)
if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $medicine_id = (int)$_GET['id'];

    // Before deleting, you might want to check if the medicine is used in any consultations.
    // For this implementation, we will proceed with a direct delete.

    $stmt = $conn->prepare("DELETE FROM medicines WHERE Mid = ?");
    $stmt->bind_param("i", $medicine_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Medicine deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "No medicine found with that ID.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // This could happen due to foreign key constraints if the medicine is in use
        $_SESSION['message'] = "Error deleting medicine. It might be in use in a past consultation.";
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    $conn->close();

} else {
    $_SESSION['message'] = "No medicine ID provided for deletion.";
    $_SESSION['message_type'] = "error";
}

header("Location: medicines.php");
exit();
?>
