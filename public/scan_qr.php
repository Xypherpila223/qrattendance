<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            position: relative;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* Dropdown styles */
        .dropdown-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        select {
            padding: 8px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .scanner-container, .options-container {
            position: relative;
            width: 300px;
            height: 300px;
            border: 5px solid black;
            border-radius: 10px;
            overflow: hidden;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 15px;
        }

        #reader {
            width: 100%;
            height: 100%;
        }

        .scan-line {
            position: absolute;
            width: 100%;
            height: 4px;
            background: red;
            animation: scanMove 2s infinite alternate;
        }

        @keyframes scanMove {
            from { top: 0; }
            to { top: 100%; }
        }

        .options-container {
            background: white;
            border: 2px solid black;
            padding: 4px;
            box-sizing: border-box;
            width: 300px;
            height: 135px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        select, input[type="file"], button {
            width: 95%;
            padding: 5px;
            font-size: 14px;
            margin: 2px 0;
            border-radius: 5px;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Dropdown Navigation -->
    <div class="dropdown-container">
        <select id="navigation" onchange="navigateToPage()">
            <option value="">Menu</option>
            <option value="student_profile.php">Student Profile</option>
            <option value="logout.php">Logout</option>
        </select>
    </div>

    <h2>Scan QR Code</h2>

    <div class="scanner-container">
        <div class="scan-line"></div>
        <div id="reader"></div>
    </div>
    
    <p id="result"></p>

    <div class="options-container">
        <select id="cameraSelect"></select>
        <button onclick="startScanner()">Start Camera</button>
        <input type="file" id="qrFile" accept="image/*">
        <button onclick="scanFromImage()">Scan QR from Image</button>
    </div>

    <script>
        let html5QrcodeScanner;

        function onScanSuccess(qrCodeMessage) {
            document.getElementById('result').innerText = "QR Code: " + qrCodeMessage;

            $.ajax({
                url: "validate_qr.php",
                type: "POST",
                data: { scanned_qr: qrCodeMessage },
                success: function(response) {
                    response = response.trim();
                    switch(response) {
                        case "success":
                            alert("Attendance recorded successfully!");
                            window.location.href = "dashboard.php";
                            break;
                        case "duplicate":
                            alert("You have already scanned a QR code today.");
                            break;
                        case "backup_success":
                            alert("Backup QR accepted. Attendance recorded.");
                            window.location.href = "dashboard.php";
                            break;
                        case "backup_duplicate":
                            alert("Backup QR already scanned today.");
                            break;
                        case "not_logged_in":
                            alert("You must log in first.");
                            window.location.href = "student_login.php";
                            break;
                        case "invalid":
                            alert("Invalid or expired QR code.");
                            break;
                        default:
                            alert("Error: " + response);
                    }
                },
                error: function() {
                    alert("Failed to send data. Please try again.");
                }
            });
        }

        function onScanFailure(error) {
            console.warn(`QR scan failed: ${error}`);
        }

        function startScanner() {
            const selectedCameraId = document.getElementById("cameraSelect").value;
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start(
                selectedCameraId,
                { fps: 10, qrbox: 250 },
                onScanSuccess,
                onScanFailure
            );
        }

        function scanFromImage() {
            const qrFileInput = document.getElementById("qrFile");
            if (qrFileInput.files.length === 0) {
                alert("Please select an image file first.");
                return;
            }
            const qrFile = qrFileInput.files[0];
            const html5QrCode = new Html5Qrcode("reader");
            html5QrCode.scanFile(qrFile, true)
                .then(qrCodeMessage => onScanSuccess(qrCodeMessage))
                .catch(error => alert("QR scan failed from image: " + error));
        }

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras.length > 0) {
                const cameraSelect = document.getElementById("cameraSelect");
                cameras.forEach(camera => {
                    const option = document.createElement("option");
                    option.value = camera.id;
                    option.text = camera.label || `Camera ${camera.id}`;
                    cameraSelect.appendChild(option);
                });
            }
        }).catch(error => console.error("Camera fetch error:", error));

        function navigateToPage() {
            var page = document.getElementById("navigation").value;
            if (page) {
                window.location.href = page;
            }
        }
    </script>
</body>
</html>
