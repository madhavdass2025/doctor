<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['did'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['id'])) {
    $doctor_id = (int)$_GET['id'];

    // It's good practice to prevent deleting the last admin or oneself
    if ($doctor_id == $_SESSION['did']) {
        $_SESSION['message'] = "Error: You cannot delete your own account.";
        $_SESSION['message_type'] = "error";
        header("Location: doctors.php");
        exit();
    }

    // Check if the doctor has any bookings. If so, prevent deletion.
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM consultation_booking WHERE DoctorID = ?");
    $check_stmt->bind_param("i", $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($result['count'] > 0) {
        $_SESSION['message'] = "Error: Cannot delete doctor because they are assigned to existing consultations. Please reassign consultations first.";
        $_SESSION['message_type'] = "error";
    } else {
        // No bookings, safe to delete
        $stmt = $conn->prepare("DELETE FROM doctors WHERE DoctorID = ?");
        $stmt->bind_param("i", $doctor_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Doctor deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No doctor found with that ID.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Error deleting doctor: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $conn->close();

} else {
    $_SESSION['message'] = "No doctor ID provided for deletion.";
    $_SESSION['message_type'] = "error";
}

header("Location: doctors.php");
exit();
?>
