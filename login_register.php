<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1.
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP 1.0. 

// Database connection
$servername = "localhost";
$username = "root"; // Default username for phpMyAdmin
$password = ""; // Default password for phpMyAdmin
$dbname = "erequestdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the output buffer to handle PHP and HTML together
ob_start();

// Initialize a message variable
$message = "";

// Determine the action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // 'login' or 'register'

    if ($action === 'login') {
        $studentEmailOrNo = $_POST['studentEmailOrNo'];
        $studentPassword = $_POST['studentPassword'];

        // Check if email or student number matches with the password
        $stmt = $conn->prepare("SELECT studentPassword FROM login WHERE (studentNo = ? OR studentEmail = ?)");
        $stmt->bind_param("ss", $studentEmailOrNo, $studentEmailOrNo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($studentPassword, $row['studentPassword'])) {
                $message = "Login successful!";
            } else {
                $message = "Invalid credentials";
            }
        } else {
            $message = "No account found";
        }
        $stmt->close();
    } elseif ($action === 'register') {
        $studentNo = $_POST['studentNo'];
        $studentName = $_POST['studentName'];
        $studentEmail = $_POST['studentEmail'];
        $studentPassword = password_hash($_POST['studentPassword'], PASSWORD_BCRYPT); // Encrypt password
        $studentCourse = $_POST['studentCourse'];
        $studentYear = $_POST['studentYear'];

        // Insert the new record
        $stmt = $conn->prepare("INSERT INTO login (studentNo, studentName, studentEmail, studentPassword, studentCourse, studentYear) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $studentNo, $studentName, $studentEmail, $studentPassword, $studentCourse, $studentYear);

        if ($stmt->execute()) {
            $message = "Registered Successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>e-Request</title>
</head>
<body>
    <?php if (!empty($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <a href="javascript:history.back()">Go Back</a>
</body>
</html>
