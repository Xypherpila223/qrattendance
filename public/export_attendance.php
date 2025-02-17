<?php
include 'db_connect.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Student Name', 'Student ID', 'Session Code', 'Timestamp']);

$result = $conn->query("SELECT a.id, s.name AS student_name, a.student_id, a.session_code, a.timestamp 
                        FROM attendance a 
                        JOIN students s ON a.student_id = s.student_id 
                        ORDER BY a.timestamp DESC");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
