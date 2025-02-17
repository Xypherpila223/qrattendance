<?php
session_start();
include 'phpqrcode/qrlib.php';
include 'db_connect.php'; // Database connection file

$qrFolder = "qrcodes/";

// Ensure the "qrcodes" folder exists
if (!file_exists($qrFolder)) {
    mkdir($qrFolder, 0777, true);
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

// Delete expired backup QR codes (older than 1 hour)
$conn->query("DELETE FROM backup_sessions WHERE expires_at < NOW()");

// Generate a backup QR Code (on load)
$qrFilePath = generateBackupQR($conn, $qrFolder);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup QR Code</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            font-family: Arial, sans-serif;
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
    </style>
    <script>
        // Auto-refresh every 1 hour (3600000 ms)
        function refreshQR() {
            location.reload();
        }
        setInterval(refreshQR, 3600000);
    </script>
</head>
<body>
    <h2>Backup QR Code</h2>
    <p>This QR code is valid for 1 hour.</p>
    <div id="qrContainer">
        <img src="<?= $qrFilePath ?>" alt="Backup QR Code">
    </div>
    <button onclick="location.reload()">Generate New Backup QR</button>
</body>
</html>
