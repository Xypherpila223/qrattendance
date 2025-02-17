<?php
session_start();
include 'db_connect.php';

// Restrict access
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Get filter values
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_student = isset($_GET['student']) ? $_GET['student'] : '';

// Build query
$query = "SELECT a.id, s.name AS student_name, a.student_id, a.session_code, a.timestamp 
          FROM attendance a 
          JOIN students s ON a.student_id = s.student_id";

$conditions = [];

if ($filter_date) {
    $conditions[] = "DATE(a.timestamp) = '$filter_date'";
}
if ($filter_student) {
    $conditions[] = "a.student_id = '$filter_student'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #005a8e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #e0f7fa;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #b2ebf2;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f8e9;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        form {
            margin: 20px;
            text-align: center;
            background-color: #cce7ff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-right: 10px;
            font-weight: bold;
        }

        input {
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 8px 16px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            text-decoration: none;
            color: #0066cc;
            font-weight: bold;
            margin: 20px;
            display: block;
            text-align: center;
        }

        a:hover {
            color: #0044cc;
        }
    </style>
</head>
<body>
    <h2>Attendance Records</h2>
    
    <form method="GET">
        <label>Filter by Date:</label>
        <input type="date" name="date" value="<?= $filter_date ?>">
        
        <label>Filter by Student ID:</label>
        <input type="text" name="student" placeholder="Enter Student ID" value="<?= $filter_student ?>">

        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Session Code</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row['student_id'] ?></td>
                <td><?= $row['session_code'] ?></td>
                <td><?= $row['timestamp'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <a href="export_attendance.php">Download Attendance (CSV)</a>
</body>
</html>
