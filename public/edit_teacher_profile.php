<?php
session_start();
include 'db_connect.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch current teacher details
$stmt = $conn->prepare("SELECT name, year_levels, courses FROM teachers WHERE id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($name, $year_levels, $courses);
$stmt->fetch();
$stmt->close();

// If form is submitted, update teacher details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_year_levels = implode(", ", $_POST['year_levels']); // Store as comma-separated string
    $new_courses = implode(", ", $_POST['courses']); // Store as comma-separated string

    $stmt = $conn->prepare("UPDATE teachers SET year_levels = ?, courses = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_year_levels, $new_courses, $teacher_id);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location='edit_teacher_profile.php';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .form-check {
            margin-bottom: 8px;
        }
        .btn-save {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-container">
        <h3 class="text-center">Edit Teacher Profile</h3>
        <hr>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label"><strong>Teacher Name:</strong></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($name) ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Year Levels Handling:</strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="year_levels[]" value="1st Year" <?= strpos($year_levels, "1st Year") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">1st Year</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="year_levels[]" value="2nd Year" <?= strpos($year_levels, "2nd Year") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">2nd Year</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="year_levels[]" value="3rd Year" <?= strpos($year_levels, "3rd Year") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">3rd Year</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="year_levels[]" value="4th Year" <?= strpos($year_levels, "4th Year") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">4th Year</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Courses Handling:</strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="courses[]" value="BSCS" <?= strpos($courses, "BSCS") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">BSCS</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="courses[]" value="IT" <?= strpos($courses, "IT") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">IT</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="courses[]" value="ECE" <?= strpos($courses, "ECE") !== false ? "checked" : "" ?>>
                    <label class="form-check-label">ECE</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
        </form>
        <button type="button" class="btn btn-secondary mt-3" onclick="history.back()">Go Back</button>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
