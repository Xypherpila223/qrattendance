<?php
session_start();
include 'db_connect.php'; // Include the database connection

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Get total students
$total_students = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Ensure full height and remove default margins */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('scc.webp') no-repeat center center fixed;
            background-size: cover; /* Ensures it covers the whole screen */
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh; /* Ensures full screen coverage */
        }

        /* Dashboard container styling */
        .dashboard-container {
            max-width: 800px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            color: #0044cc; /* Blue color for the heading */
            margin-top: 10px;
        }

        p {
            font-size: 18px;
            margin: 10px;
        }

        strong {
            color: #0044cc; /* Blue color for important text */
        }

        /* Styling the attendance number in green */
        #todayAttendance {
            font-size: 20px;
            font-weight: bold;
            color: #28a745; /* Green color for present students */
        }

        /* Button style */
        .btn {
            background-color: #0044cc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin: 10px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0033aa;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Attendance Dashboard</h2>
        <p><strong style="color: green;">Total Students:</strong> <span><strong style="color: green;"><?= $total_students ?></strong></span></p>
        <p><strong style="color: green;">Students Present Today:</strong> <span id="todayAttendance" style="color: green;"><?= $total_students ?></span></p>

        <br>
        <a href="view_attendance.php" class="btn">View Attendance Records</a>
        <a href="export_attendance.php" class="btn">Download Attendance (CSV)</a>
    </div>

    <script>
        // Function to update today's attendance count in real time
        function updateAttendanceCount() {
            $.ajax({
                url: 'get_today_attendance.php', // PHP file to fetch attendance
                method: 'GET',
                success: function(response) {
                    $('#todayAttendance').text(response); // Update the displayed count
                }
            });
        }

        // Call the function to update attendance every 10 seconds
        setInterval(updateAttendanceCount, 10000);
    </script>
</body>
</html>
