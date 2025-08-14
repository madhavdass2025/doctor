<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $lid = (int)$_POST['Lid'];
    $name = trim($_POST['name']);
    $amount = trim($_POST['amount']);
    $submittedby = $_SESSION['did'];

    if (empty($name) || empty($amount)) {
        $_SESSION['message'] = "Test Name and Amount are required.";
        $_SESSION['message_type'] = "error";
        header("Location: lab_test_form.php" . ($lid ? "?id=$lid" : ""));
        exit();
    }

    if ($lid > 0) {
        // --- UPDATE existing record ---
        $sql = "UPDATE laboratory SET name=?, amount=?, submittedBy=? WHERE Lid=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $amount, $submittedby, $lid);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lab Test updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating lab test: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        $sql = "INSERT INTO laboratory (name, amount, submittedBy) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $amount, $submittedby);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lab Test added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding lab test: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: lab_tests.php");
    exit();

} else {
    header("Location: lab_tests.php");
    exit();
}
?>
