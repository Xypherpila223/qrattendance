<?php
session_start();
include 'phpqrcode/qrlib.php';
include 'db_connect.php'; // Database connection file

$qrFolder = "qrcodes/";

// Ensure the "qrcodes" folder exists
if (!file_exists($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

// Function to generate a new QR session
function generateQR($conn, $qrFolder) {
    $session_code = uniqid("session_");
    $_SESSION['current_qr'] = $session_code;

    $qrFilePath = $qrFolder . $session_code . ".png";

    QRcode::png($session_code, $qrFilePath, QR_ECLEVEL_L, 10);

    $stmt = $conn->prepare("INSERT INTO qr_sessions (session_code, created_at) VALUES (?, NOW())");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $stmt->close();

    return $qrFilePath;
}

// Function to generate a backup QR (Valid for 1 Hour)
function generateBackupQR($conn, $qrFolder) {
    $session_code = uniqid("backup_");
    $_SESSION['backup_qr'] = $session_code;

    $qrFilePath = $qrFolder . $session_code . ".png";

    QRcode::png($session_code, $qrFilePath, QR_ECLEVEL_L, 10);

    $stmt = $conn->prepare("INSERT INTO backup_sessions (session_code, created_at, expires_at) VALUES (?, NOW(), NOW() + INTERVAL 1 HOUR)");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $stmt->close();

    return $qrFilePath;
}

// Delete expired QR codes (older than 2 minutes)
$conn->query("DELETE FROM qr_sessions WHERE created_at < NOW() - INTERVAL 2 MINUTE");
// Delete expired backup QR codes (older than 1 hour)
$conn->query("DELETE FROM backup_sessions WHERE expires_at < NOW()");

// Generate a normal QR Code (on load)
$qrFilePath = generateQR($conn, $qrFolder);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            font-family: Arial, sans-serif;
            position: relative;
        }
        #qrContainer img {
            border: 5px solid #000;
            padding: 10px;
            border-radius: 10px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background: #0056b3;
        }
        select {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <script>
        // Auto-refresh every 2 minutes
        function refreshQR() {
            location.reload();
        }
        setInterval(refreshQR, 120000); // 120,000 ms = 2 minutes

        // Manual refresh
        function generateNewQR() {
            location.href = 'generate_qr.php';
        }

        // Generate backup QR
        function generateBackupQR() {
            location.href = 'generate_backup_qr.php';
        }

        // Dropdown navigation
        function navigateToPage() {
            var page = document.getElementById("navigation").value;
            if (page) {
                window.location.href = page;
            }
        }
    </script>
</head>
<body>
    <h2>Scan QR Code for Attendance</h2>
    <p>The QR code refreshes every 2 minutes automatically.</p>
    <div id="qrContainer">
        <img src="<?= $qrFilePath ?>" alt="QR Code">
    </div>
    <button onclick="generateNewQR()">Generate New QR</button>
    <button onclick="generateBackupQR()">Generate Backup QR (Valid for 1 Hour)</button> <!-- New Button -->

    <!-- Dropdown for navigation at the top right -->
    <select id="navigation" onchange="navigateToPage()">
        <option value="">Select a Page</option>
        <option value="dashboard.php">Dashboard</option>
        <option value="edit_teacher_profile.php">Teacher Profile</option>
        <option value="logout.php">Logout</option>
        <option value="generate_backup_qr.php">Generate Backup QR</option> <!-- New Option -->
    </select>
</body>
</html>
