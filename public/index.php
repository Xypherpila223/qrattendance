<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
            background: url('public\scc.webp') no-repeat center center fixed;
            background-size: cover;
        }

        .title {
            text-align: center;
            margin-top: 20px;
            font-size: 2.5em;
            font-weight: bold;
            color: #007bff;
        }

        .container {
            display: flex;
            justify-content: flex-end; /* Moves container to the right */
            width: 100%;
            height: calc(100vh - 60px);
            align-items: center;
            padding-right: 40px;
        }

        .right-side {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .container-box {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.85); /* Slight transparency */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px; /* Set fixed width */
            height: 800px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .welcome-text {
            font-size: 1.8em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }

        .container-box a {
            font-size: 18px;
            text-decoration: none;
            color: #007bff;
            margin: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #007bff;
            transition: background-color 0.3s;
        }

        .container-box a:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="title">
        Welcome to QR Attendance System
    </div>
    <div class="container">
        <div class="right-side">
            <div class="container-box">
                <div class="welcome-text">Welcome Clareans!</div>
                <p><a href="teacher_login.php">Teacher Login</a></p>
                <p><a href="student_login.php">Student Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
