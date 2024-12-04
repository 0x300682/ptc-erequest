<?php
// api/login.php
session_start();
include("../php/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentEmailOrNo = mysqli_real_escape_string($con, $_POST['studentEmailOrNo']);
    $studentPassword = mysqli_real_escape_string($con, $_POST['studentPassword']);

    $query = "SELECT * FROM login WHERE (studentEmail='$studentEmailOrNo' OR studentNo='$studentEmailOrNo') AND studentPassword='$studentPassword'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $_SESSION['valid'] = $row['studentEmail'];
        // Set other session variables as needed
        echo json_encode(['redirect' => '/dashboard']); // Send a redirect response
    } else {
        echo json_encode(['error' => 'Wrong Email/Student No. or Password']);
    }
}
?>
