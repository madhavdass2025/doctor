<div class="sidebar">
    <h2>VetCare Admin</h2>
    <ul>
        <li><a href="index.php" class="<?= ($active_page == 'dashboard') ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="doctors.php" class="<?= ($active_page == 'doctors') ? 'active' : '' ?>">Manage Doctors</a></li>
        <li><a href="medicines.php" class="<?= ($active_page == 'medicines') ? 'active' : '' ?>">Manage Medicines</a></li>
        <li><a href="vaccinations.php" class="<?= ($active_page == 'vaccinations') ? 'active' : '' ?>">Manage Vaccinations</a></li>
        <li><a href="lab_tests.php" class="<?= ($active_page == 'lab_tests') ? 'active' : '' ?>">Manage Lab Tests</a></li>
        <li><a href="surgeries.php" class="<?= ($active_page == 'surgeries') ? 'active' : '' ?>">Manage Surgeries</a></li>
        <li><a href="scans.php" class="<?= ($active_page == 'scans') ? 'active' : '' ?>">Manage Scans</a></li>
        <li><a href="../doctor/index.php">Go to Doctor View</a></li>
        <!-- Add a logout link if authentication is implemented -->
        <!-- <li><a href="logout.php">Logout</a></li> -->
    </ul>
</div>
