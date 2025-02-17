<?php
session_start();
include 'db_connect.php'; // Include the database connection

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "not_logged_in"; // Return error if student is not logged in
    exit();
}

// Get the scanned QR code
$scanned_qr = $_POST['scanned_qr'] ?? '';
$student_id = $_SESSION['student_id'];

// Check if the student has already recorded attendance for today
$stmt = $conn->prepare("
    SELECT * FROM attendance 
    WHERE student_id = ? 
    AND DATE(timestamp) = CURDATE()
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$attendanceResult = $stmt->get_result();

if ($attendanceResult->num_rows > 0) {
    echo "Your Attendance Already Recorded!"; // Student has already recorded attendance today
    exit();
}

// Validate the backup QR code (check if it exists and is not expired)
$stmt = $conn->prepare("
    SELECT * FROM backup_sessions 
    WHERE session_code = ? 
    AND expires_at > NOW()
");
$stmt->bind_param("s", $scanned_qr);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Insert attendance record using backup QR
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, session_code, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $student_id, $scanned_qr);
    
    if ($stmt->execute()) {
        echo "success"; // Attendance successfully recorded
    } else {
        echo "error"; // Error during insertion
    }
} else {
    echo "invalid"; // If the QR code is invalid or expired
}

$stmt->close();
$conn->close();
?>
