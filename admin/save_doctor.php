<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['did'])) {

    // Sanitize and retrieve POST data
    $doctor_id = (int)$_POST['DoctorID'];
    $name = trim($_POST['Name']);
    $email = trim($_POST['Email']);
    $password = trim($_POST['Password']);
    $is_active = (int)$_POST['IsActive'];

    // Basic validation
    if (empty($name) || empty($email)) {
        $_SESSION['message'] = "Name and Email are required.";
        $_SESSION['message_type'] = "error";
        header("Location: doctor_form.php" . ($doctor_id ? "?id=$doctor_id" : ""));
        exit();
    }

    if ($doctor_id > 0) {
        // --- UPDATE existing record ---
        if (!empty($password)) {
            // If a new password is provided, hash it and update it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE doctors SET Name = ?, Email = ?, Password = ?, IsActive = ? WHERE DoctorID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $name, $email, $hashed_password, $is_active, $doctor_id);
        } else {
            // If no new password, update everything else
            $sql = "UPDATE doctors SET Name = ?, Email = ?, IsActive = ? WHERE DoctorID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $name, $email, $is_active, $doctor_id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Doctor updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating doctor: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // --- INSERT new record ---
        if (empty($password)) {
            $_SESSION['message'] = "Password is required for a new doctor.";
            $_SESSION['message_type'] = "error";
            header("Location: doctor_form.php");
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO doctors (Name, Email, Password, IsActive) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $hashed_password, $is_active);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Doctor added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding doctor: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: doctors.php");
    exit();

} else {
    header("Location: doctors.php");
    exit();
}
?>
