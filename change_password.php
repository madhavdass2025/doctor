<?php
session_start();
// In a real app, you'd have a login check here
if (!isset($_SESSION['did'])) {
    // For demonstration, let's set a dummy session
    $_SESSION['did'] = 1;
    $_SESSION['doctor_name'] = 'Dr. Smith';
    $_SESSION['role'] = 'doctor';
}

$page_title = 'Change Password';
$active_page = 'change_password'; // This won't match any sidebar item, which is fine
include 'includes/header.php';
?>

<h3>Change Your Password</h3>
<p>Please enter your old password and your new password.</p>

<form action="update_password.php" method="POST" style="max-width: 500px;">
    <div style="margin-bottom: 15px;">
        <label for="old_password" style="display: block; margin-bottom: 5px;">Old Password</label>
        <input type="password" id="old_password" name="old_password" required style="width: 100%; padding: 8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="new_password" style="display: block; margin-bottom: 5px;">New Password</label>
        <input type="password" id="new_password" name="new_password" required style="width: 100%; padding: 8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="confirm_password" style="display: block; margin-bottom: 5px;">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 8px;">
    </div>
    <button type="submit" class="btn btn-primary">Update Password</button>
</form>

<?php
include 'includes/footer.php';
?>
