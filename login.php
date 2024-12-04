<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1.
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP 1.0.  
session_start();
include("php/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>e-Request</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='loginCSS.css'>
</head>
<body>
    <header>
        <img src="ptclogo.png">
        <span>Pateros Technological College</span>
    </header>
    <div class="container">

        <!-- Login Section -->
        <div class="form-box login">
            <?php 
              if (isset($_POST['action']) && $_POST['action'] == 'login') {
                $studentEmailOrNo = mysqli_real_escape_string($con, $_POST['studentEmailOrNo']);
                $studentPassword = mysqli_real_escape_string($con, $_POST['studentPassword']);

                // Query to check if login matches email or student number
                $query = "SELECT * FROM login WHERE (studentEmail='$studentEmailOrNo' OR studentNo='$studentEmailOrNo') AND studentPassword='$studentPassword'";
                $result = mysqli_query($con, $query) or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if (is_array($row) && !empty($row)) {
                    // Set session variables
                    $_SESSION['valid'] = $row['studentEmail'];
                    $_SESSION['studentName'] = $row['studentName'];
                    $_SESSION['studentCourse'] = $row['studentCourse'];
                    $_SESSION['studentYear'] = $row['studentYear'];
                    $_SESSION['studentNo'] = $row['studentNo'];
                    
                    // Redirect to home page
                    header("Location: dashboard.php");
                    exit;
                } else {
                    // Display alert if login fails
                    echo "<script>alert('Wrong Email/Student No. or Password');</script>";
                }
              }
            ?>
            <form id="loginForm" action="" method="POST">
                <h1>Login</h1>
                <input type="hidden" name="action" value="login">
                <div class="input-box">
                    <input type="text" name="studentEmailOrNo" placeholder="Email/Student No." autocomplete="off" required>
                    <i class='bx bx-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="studentPassword" placeholder="Password" autocomplete="off" required>
                    <i class='bx bx-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <!-- Registration Section -->
        <div class="form-box register">
            <?php 
              if (isset($_POST['action']) && $_POST['action'] == 'register') {
                $studentNo = mysqli_real_escape_string($con, $_POST['studentNo']);
                $studentName = mysqli_real_escape_string($con, $_POST['studentName']);
                $studentEmail = mysqli_real_escape_string($con, $_POST['studentEmail']);
                $studentPassword = mysqli_real_escape_string($con, $_POST['studentPassword']);
                $studentCourse = mysqli_real_escape_string($con, $_POST['studentCourse']);
                $studentYear = mysqli_real_escape_string($con, $_POST['studentYear']);

                // Check if email or student number already exists
                $checkQuery = "SELECT * FROM login WHERE studentEmail='$studentEmail' OR studentNo='$studentNo'";
                $checkResult = mysqli_query($con, $checkQuery);
                if (mysqli_num_rows($checkResult) > 0) {
                    echo "<script>alert('Email or Student Number already exists.');</script>";
                } else {
                    // Insert new user into the database
                    $insertQuery = "INSERT INTO login (studentNo, studentName, studentEmail, studentPassword, studentCourse, studentYear) 
                                    VALUES ('$studentNo', '$studentName', '$studentEmail', '$studentPassword', '$studentCourse', '$studentYear')";
                    if (mysqli_query($con, $insertQuery)) {
                        echo "<script>alert('Registration successful! You can now log in.');</script>";
                    } else {
                        echo "<script>alert('Registration failed. Please try again.');</script>";
                    }
                }
              }
            ?>

            <form action="" method="POST" <?= isset($_GET['register']) ? "style='display:none;'" : "" ?>>
                <h1>Register</h1>
                <input type="hidden" name="action" value="register">
                <div class="input-box">
                    <input type="text" name="studentNo" placeholder="Student No." autocomplete="off" required>
                    <i class='bx bx-user'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="studentName" placeholder="Name" autocomplete="off" required>
                    <i class='bx bx-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="studentEmail" placeholder="Email" autocomplete="off" required>
                    <i class='bx bx-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="studentPassword" placeholder="Password" autocomplete="off" required>
                    <i class='bx bx-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="studentCourse" placeholder="Course" autocomplete="off" required>
                    <i class='bx bxs-graduation'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="studentYear" placeholder="Year Level" autocomplete="off" required>
                    <i class='bx bx-up-arrow-alt'></i>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Welcome PTCians!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div> 
    <script src="JSLogin.js"></script>
</body>
</html> 
