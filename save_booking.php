<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNo = $_SESSION['studentNo'];
    $bookingDate = $_POST['bookingDate'];

    if (empty($bookingDate)) {
        die("No date selected.");
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $maxBookings = 5;
    $query = "SELECT COUNT(*) as bookingCount FROM booking WHERE bookingDate = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $bookingDate);
    $stmt->execute();
    $stmt->bind_result($bookingCount);
    $stmt->fetch();
    $stmt->close();

    if ($bookingCount >= $maxBookings) {
        die("The selected date is already fully booked. Please choose another date.");
    }

    $conn->begin_transaction();

    try {
        // Insert the primary booking
        $sql = "INSERT INTO booking (studentNo, bookingDate) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $studentNo, $bookingDate);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting booking: " . $stmt->error);
        }

        // Fetch the last inserted booking ID
        $bookingID = $conn->insert_id;
        $stmt->close();

        // Handle request IDs if provided
        if (isset($_POST['readyRequestIDs'])) {
            $readyRequestIDs = json_decode($_POST['readyRequestIDs'], true);

            if (!empty($readyRequestIDs)) {
                // Insert the request IDs into the request_booking table
                $insertRequestSQL = "INSERT INTO request_booking (bookingID, requestID) VALUES (?, ?)";
                $insertStmt = $conn->prepare($insertRequestSQL);

                // Update requestStatus to "Booked" in the request table
                $updateRequestSQL = "UPDATE request SET requestStatus = 'Booked' WHERE requestID = ?";
                $updateStmt = $conn->prepare($updateRequestSQL);

                foreach ($readyRequestIDs as $requestID) {
                    // Insert into request_booking
                    $insertStmt->bind_param('ii', $bookingID, $requestID);
                    if (!$insertStmt->execute()) {
                        throw new Exception("Error inserting requestID into request_booking: " . $insertStmt->error);
                    }

                    // Update the request table
                    $updateStmt->bind_param('i', $requestID);
                    if (!$updateStmt->execute()) {
                        throw new Exception("Error updating request status: " . $updateStmt->error);
                    }
                }
                $insertStmt->close();
                $updateStmt->close();
            }
        }

        $conn->commit();
        $_SESSION['bookingSuccess'] = true;
        $_SESSION['currentStep'] = 4;
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }

    $conn->close();
}
?>





