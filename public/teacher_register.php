<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: teacher_register.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header("Location: teacher_register.php");
        exit();
    } 

    $stmt->close();

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new teacher
    $stmt = $conn->prepare("INSERT INTO teachers (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: teacher_login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again!";
        header("Location: teacher_register.php");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .container { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .message { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register as a Teacher</h2>

        <?php 
        if (isset($_SESSION['error'])) {
            echo "<p class='message'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']); // Clear session message
        }
        if (isset($_SESSION['success'])) {
            echo "<p class='success'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']); // Clear session message
        }
        ?>

        <form method="POST">
            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Register</button>
        </form>

        <p>Already registered? <a href="teacher_login.php">Login here</a></p>
    </div>
</body>
</html>
