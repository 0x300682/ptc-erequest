<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1.
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP 1.0. 
// Start session to access student data
session_start();

// Include database configuration
include("php/config.php");

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNo = $_POST['studentNo'];
    $selectedDocuments = $_POST['documents'] ?? [];
    $otherDocument = $_POST['otherDocument'] ?? '';

    // Validate input
    if (empty($selectedDocuments) && empty($otherDocument)) {
        die("No document selected.");
    }

    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); // Use your constants in `config.php`

    // Check for connection errors
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Insert each selected document
    foreach ($selectedDocuments as $document) {
        $sql = "INSERT INTO request (studentNo, requestDocument, requestStatus) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $requestStatus = 'Pending';
        $stmt->bind_param('sss', $studentNo, $document, $requestStatus);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // Insert "Other" document if provided
    if (!empty($otherDocument)) {
        $sql = "INSERT INTO request (studentNo, requestDocument, requestStatus) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $requestStatus = 'Pending';
        $stmt->bind_param('sss', $studentNo, $otherDocument, $requestStatus);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();

    // Redirect to next step or success page
    // After successful insertion into the database
    $_SESSION['currentStep'] = 2; // Move to Step 2
    session_write_close();
    header("Location: dashboard.php");
    exit();
}
?>
