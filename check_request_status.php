<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['valid'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the booking ID from the request
$bookingID = $_GET['bookingID'];

// Check the status of the requests for the given booking ID
$statusQuery = "
    SELECT COUNT(*) AS totalRequests, 
           SUM(CASE WHEN requestStatus = 'Done' THEN 1 ELSE 0 END) AS doneRequests
    FROM request AS r
    INNER JOIN request_booking AS rb ON r.requestID = rb.requestID
    WHERE rb.bookingID = '$bookingID'";

$statusResult = mysqli_query($con, $statusQuery);
$statusRow = mysqli_fetch_assoc($statusResult);

$response = [
    'allDone' => $statusRow['totalRequests'] == $statusRow['doneRequests']
];

echo json_encode($response);
?>