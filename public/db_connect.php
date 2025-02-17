<?php
// Database connection settings
$host = 'localhost';       // Database host (use 'localhost' for local MySQL, or a remote DB host)
$username = 'root';        // MySQL username (adjust as per your setup)
$password = 'yourpassword'; // MySQL password (adjust as per your setup)
$database = 'qr_attendance'; // Database name

// Create a connection to the MySQL database
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    // Log the error and stop the script execution if connection fails
    error_log("❌ Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error); // Stop execution
}
?>
