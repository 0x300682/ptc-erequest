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
    <link rel='stylesheet' type='text/css' media='screen' href='adminLCSS.css'>
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
if(isset($_POST['action']) && $_POST['action'] == 'login') {
    $studentEmailOrNo = mysqli_real_escape_string($con, $_POST['studentEmailOrNo']);
    $studentPassword = mysqli_real_escape_string($con, $_POST['studentPassword']);

    // Query to check if login matches email or student number
    $query = "SELECT * FROM login WHERE (studentEmail='$studentEmailOrNo' OR studentNo='$studentEmailOrNo') AND studentPassword='$studentPassword'";
    $result = mysqli_query($con, $query) or die("Select Error");
    $row = mysqli_fetch_assoc($result);

    if(is_array($row) && !empty($row)) {
        // Set session variables
        $_SESSION['valid'] = $row['studentEmail'];
        $_SESSION['studentName'] = $row['studentName'];
        $_SESSION['studentCourse'] = $row['studentCourse'];
        $_SESSION['studentYear'] = $row['studentYear'];
        $_SESSION['studentNo'] = $row['studentNo'];
        
        // Redirect to home page
        header("Location: admindashboard.php");
        exit;
    } else {
        // Display error as an alert and redirect after 2 seconds
        echo "<script>alert('Wrong Email/Student No. or Password');</script>";
        echo "<script>
            setTimeout(function() {
                window.location.href='adminLogin.php';
            }, 2000); // Redirects after 2 seconds
        </script>";
    }
}
?>
            <form id="loginForm" action="" method="POST">
                <h1>Admin Login</h1>
                <input type="hidden" name="action" value="login">
                <div class="input-box">
                    <input type="text" name="studentEmailOrNo" placeholder="Username." autocomplete="off" required>
                    <i class='bx bx-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="studentPassword" placeholder="Password" autocomplete="off" required>
                    <i class='bx bx-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

    <script src="JSLogin.js"></script>
</body>
</html>