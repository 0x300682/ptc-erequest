<?php
session_start();
include("../php/config.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNo = mysqli_real_escape_string($con, $_POST['studentNo']);
    $studentName = mysqli_real_escape_string($con, $_POST['studentName']);
    $studentEmail = mysqli_real_escape_string($con, $_POST['studentEmail']);
    $studentPassword = mysqli_real_escape_string($con, $_POST['studentPassword']);
    $studentCourse = mysqli_real_escape_string($con, $_POST['studentCourse']);
    $studentYear = mysqli_real_escape_string($con, $_POST['studentYear']);

    $checkQuery = "SELECT * FROM login WHERE studentEmail='$studentEmail' OR studentNo='$studentNo'";
    $checkResult = mysqli_query($con, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['error' => 'Email or Student Number already exists.']);
    } else {
        $insertQuery = "INSERT INTO login (studentNo, studentName, studentEmail, studentPassword, studentCourse, studentYear) 
                        VALUES ('$studentNo', '$studentName', '$studentEmail', '$studentPassword', '$studentCourse', '$studentYear')";
        if (mysqli_query($con, $insertQuery)) {
            echo json_encode(['success' => 'Registration successful! You can now log in.']);
        } else {
            echo json_encode(['error' => 'Registration failed.']);
        }
    }
}
?>
