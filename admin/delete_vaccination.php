<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['id'])) {
    $vaccination_id = (int)$_GET['id'];

    // Check if the vaccination is in use in consultation_injections
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM consultation_injections WHERE VaccinationID = ?");
    $check_stmt->bind_param("i", $vaccination_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($result['count'] > 0) {
        $_SESSION['message'] = "Error: Cannot delete vaccination because it has been used in past consultations.";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM vaccination WHERE VId = ?");
        $stmt->bind_param("i", $vaccination_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Vaccination deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No vaccination found with that ID.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Error deleting vaccination: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $conn->close();

} else {
    $_SESSION['message'] = "No vaccination ID provided for deletion.";
    $_SESSION['message_type'] = "error";
}

header("Location: vaccinations.php");
exit();
?>
