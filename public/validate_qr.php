<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['student_id'])) {
    echo "not_logged_in";
    exit();
}

$scanned_qr = $_POST['scanned_qr'] ?? '';
$student_id = $_SESSION['student_id'];

// Check if the student already scanned today
$stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND DATE(timestamp) = CURDATE()");
$stmt->bind_param("s", $student_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "duplicate";
    exit();
}

// 1️⃣ Check if the scanned QR is in the main `qr_sessions` table (valid for 2 minutes)
$qr_query = "SELECT * FROM qr_sessions WHERE session_code = ? AND created_at > NOW() - INTERVAL 2 MINUTE";
$stmt = $conn->prepare($qr_query);
$stmt->bind_param("s", $scanned_qr);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Valid QR Code - Mark Attendance
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, session_code, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $student_id, $scanned_qr);
    echo $stmt->execute() ? "success" : "error";
    exit();
}

// 2️⃣ If the main QR failed, check the backup QR (valid for 10 minutes)
$backup_query = "SELECT * FROM backup_sessions WHERE session_code = ? AND expires_at > NOW()";
$stmt = $conn->prepare($backup_query);
$stmt->bind_param("s", $scanned_qr);
$stmt->execute();
$backup_result = $stmt->get_result();

if ($backup_result->num_rows > 0) {
    // Valid Backup QR - Check if already scanned
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND session_code = ?");
    $stmt->bind_param("ss", $student_id, $scanned_qr);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "backup_duplicate"; // Already scanned backup QR today
        exit();
    }

    // Insert attendance with backup QR
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, session_code, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $student_id, $scanned_qr);
    echo $stmt->execute() ? "backup_success" : "error";
    exit();
}

// ❌ If no valid QR is found
echo "invalid";
?>
