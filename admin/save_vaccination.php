<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $vid = (int)$_POST['VId'];
    $name = trim($_POST['name']);
    $amount = trim($_POST['amount']);
    $type = trim($_POST['type']);
    $duration = trim($_POST['duration']);
    $submittedby = $_SESSION['did'];

    if (empty($name) || empty($amount)) {
        $_SESSION['message'] = "Vaccination Name and Amount are required.";
        $_SESSION['message_type'] = "error";
        header("Location: vaccination_form.php" . ($vid ? "?id=$vid" : ""));
        exit();
    }

    if ($vid > 0) {
        // --- UPDATE existing record ---
        $sql = "UPDATE vaccination SET name=?, amount=?, type=?, duration=?, submittedBy=? WHERE VId=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $amount, $type, $duration, $submittedby, $vid);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Vaccination updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating vaccination: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        $sql = "INSERT INTO vaccination (name, amount, type, duration, submittedBy) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $amount, $type, $duration, $submittedby);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Vaccination added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding vaccination: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: vaccinations.php");
    exit();

} else {
    header("Location: vaccinations.php");
    exit();
}
?>
