<?php
// This file assumes a session has been started by the calling page.
// It also assumes a $page_title variable is set for the page title.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'VetCare'; ?> - VetCare</title>
    <link rel="stylesheet" href="css/app_style.css">
</head>
<body>
    <div class="app-wrapper">

        <?php include 'sidebar.php'; // Include the sidebar for navigation ?>

        <div class="main-content">
            <header class="main-header">
                <div class="user-profile">
                    <span>Welcome, <strong><?= htmlspecialchars($_SESSION['doctor_name'] ?? 'Doctor'); ?></strong></span>
                    <a href="change_password.php">Change Password</a>
                    <a href="logout.php">Logout</a>
                </div>
            </header>
            <main class="page-content">
                <div class="content-box">
                    <!-- Page-specific content starts here -->
                    <h1><?= isset($page_title) ? htmlspecialchars($page_title) : 'Page Title'; ?></h1>
                    <hr>
