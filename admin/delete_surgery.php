<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['id'])) {
    $surgery_id = (int)$_GET['id'];

    // Check if the surgery is in use in consultations
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM consultation_surgeries WHERE SurgeryID = ?");
    $check_stmt->bind_param("i", $surgery_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($result['count'] > 0) {
        $_SESSION['message'] = "Error: Cannot delete surgery because it has been used in past consultations.";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM surgery WHERE surgeryID = ?");
        $stmt->bind_param("i", $surgery_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Surgery deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No surgery found with that ID.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Error deleting surgery: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $conn->close();

} else {
    $_SESSION['message'] = "No surgery ID provided for deletion.";
    $_SESSION['message_type'] = "error";
}

header("Location: surgeries.php");
exit();
?>
