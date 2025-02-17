<?php
session_start();
include 'db_connect.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "not_logged_in";
    exit();
}

$scanned_qr = $_POST['scanned_qr'] ?? '';
$student_id = $_SESSION['student_id'];

// Check if student already recorded attendance today
$stmt = $conn->prepare("
    SELECT * FROM attendance 
    WHERE student_id = ? 
    AND DATE(timestamp) = CURDATE()
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$attendanceResult = $stmt->get_result();

if ($attendanceResult->num_rows > 0) {
    echo "already_recorded";
    exit();
}

// Validate the Backup QR Code
include 'validate_backup_qr.php';

if ($result->num_rows > 0) {
    // Insert attendance record using backup QR
    $stmt = $conn->prepare("
        INSERT INTO attendance (student_id, session_code, timestamp, is_backup) 
        VALUES (?, ?, NOW(), 1)
    ");
    $stmt->bind_param("ss", $student_id, $scanned_qr);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}

$stmt->close();
$conn->close();
?>
