<?php
// This file assumes a session is active and an $active_page variable is set.
// We can also check for a user role to show different navigation links.
$user_role = $_SESSION['role'] ?? 'doctor'; // Default to 'doctor' role
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>VetCare</h2>
    </div>
    <ul class="sidebar-nav">
        <?php if ($user_role == 'doctor'): ?>
            <li><a href="doctor/index.php" class="<?= ($active_page == 'dashboard') ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="doctor/patients.php" class="<?= ($active_page == 'patients') ? 'active' : '' ?>">Patients</a></li>
            <!-- Add more doctor-specific links here -->
        <?php endif; ?>

        <?php if ($user_role == 'admin'): ?>
            <li><a href="admin/index.php" class="<?= ($active_page == 'admin_dashboard') ? 'active' : '' ?>">Admin Dashboard</a></li>
            <li><a href="admin/doctors.php" class="<?= ($active_page == 'doctors') ? 'active' : '' ?>">Manage Doctors</a></li>
            <li><a href="admin/medicines.php" class="<?= ($active_page == 'medicines') ? 'active' : '' ?>">Manage Medicines</a></li>
            <li><a href="admin/vaccinations.php" class="<?= ($active_page == 'vaccinations') ? 'active' : '' ?>">Manage Vaccinations</a></li>
            <li><a href="admin/lab_tests.php" class="<?= ($active_page == 'lab_tests') ? 'active' : '' ?>">Manage Lab Tests</a></li>
            <li><a href="admin/surgeries.php" class="<
?php ($active_page == 'surgeries') ? 'active' : '' ?>">Manage Surgeries</a></li>
            <li><a href="admin/scans.php" class="<?= ($active_page == 'scans') ? 'active' : '' ?>">Manage Scans</a></li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <p>Version 1.0</p>
    </div>
</aside>
