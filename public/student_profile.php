<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$message = "";

// Fetch student details
$query = "SELECT name, email, year_level, course, contact FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Student not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $year_level = trim($_POST['year_level']);
    $course = trim($_POST['course']);
    $contact = trim($_POST['contact']);

    $update_query = "UPDATE students SET name = ?, email = ?, year_level = ?, course = ?, contact = ? WHERE student_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssss", $name, $email, $year_level, $course, $contact, $student_id);

    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
        // Refresh the profile info
        $student = ['name' => $name, 'email' => $email, 'year_level' => $year_level, 'course' => $course, 'contact' => $contact];
    } else {
        $message = "Error updating profile.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            width: 400px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            font-size: 14px;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2>Student Profile</h2>
        
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

            <label for="year_level">Year Level:</label>
<select id="year_level" name="year_level" required>
    <option value="1st Year" <?= $student['year_level'] == '1st Year' ? 'selected' : '' ?>>1st Year</option>
    <option value="2nd Year" <?= $student['year_level'] == '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
    <option value="3rd Year" <?= $student['year_level'] == '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
    <option value="4th Year" <?= $student['year_level'] == '4th Year' ? 'selected' : '' ?>>4th Year</option>
</select>

<div class="mb-3">
    <label for="course" class="form-label"><strong>Course:</strong></label>
    <select id="course" class="form-select" name="course" required>
        <option value="BSCS" <?= $student['course'] == 'BSCS' ? 'selected' : '' ?>>BSCS</option>
        <option value="IT" <?= $student['course'] == 'IT' ? 'selected' : '' ?>>IT</option>
        <option value="ECE" <?= $student['course'] == 'ECE' ? 'selected' : '' ?>>ECE</option>
    </select>
</div>

                

            <label for="contact">Contact Number:</label>
            <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($student['contact']) ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>

</body>
</html>
