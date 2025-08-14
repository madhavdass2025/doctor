<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $sid = (int)$_POST['surgeryID'];
    $name = trim($_POST['surName']);
    $amount = trim($_POST['surAmount']);
    $submittedby = $_SESSION['did'];

    if (empty($name) || empty($amount)) {
        $_SESSION['message'] = "Surgery Name and Amount are required.";
        $_SESSION['message_type'] = "error";
        header("Location: surgery_form.php" . ($sid ? "?id=$sid" : ""));
        exit();
    }

    if ($sid > 0) {
        // --- UPDATE existing record ---
        $sql = "UPDATE surgery SET surName=?, surAmount=?, submitBy=? WHERE surgeryID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $amount, $submittedby, $sid);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Surgery updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating surgery: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        $sql = "INSERT INTO surgery (surName, surAmount, submitBy) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $amount, $submittedby);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Surgery added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding surgery: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: surgeries.php");
    exit();

} else {
    header("Location: surgeries.php");
    exit();
}
?>
