<?php
/*
* VetCare Doctor Dashboard
* Database Connection
*/

// --- Database Configuration ---
// Replace with your actual database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'vetcare');

// --- Establish Connection ---
/*
* Attempt to connect to the MySQL database.
* The @ symbol is used to suppress the default PHP warning on connection failure,
* allowing for custom error handling.
*/
$conn = @new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// --- Connection Error Handling ---
/*
* Check if the connection was successful.
* mysqli_connect_errno() returns an error code if the connection fails.
*/
if ($conn->connect_error) {
    // It's generally better to log errors than to display them to the end-user,
    // but for development purposes, we can die and show the error.
    die("<strong>Database Connection Failed:</strong> " . $conn->connect_error);
}

// --- Set Character Set ---
/*
* It's good practice to set the character set for the connection
* to prevent SQL injection issues related to character encoding.
*/
if (!$conn->set_charset("utf8mb4")) {
    // Handle charset error if needed
    // For simplicity, we'll proceed, but in a production app, this might be logged.
}

// The $conn variable can now be used in other scripts to interact with the database.
// Example: include 'includes/db_connect.php';
?>
