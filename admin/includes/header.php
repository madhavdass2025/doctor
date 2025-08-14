<?php
// A session should already be started by the page including this file
// if session-based logic is needed (e.g., admin login check)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>VetCare Admin</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1><?= isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard' ?></h1>
            <hr>
        </header>
        <main>
            <!-- Page-specific content starts here -->
