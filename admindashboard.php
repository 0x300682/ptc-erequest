<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start the session and include database configuration
session_start();
include("php/config.php");



// Fetch all pending document requests
$requestQuery = "SELECT * FROM request WHERE requestStatus = 'Pending'";
$requestResult = mysqli_query($con, $requestQuery);

// Fetch all bookings with student details and the requestID from request_booking table
$bookingQuery = "
    SELECT 
        booking.bookingID, 
        booking.bookingDate, 
        login.studentName, 
        login.studentCourse, 
        login.studentYear, 
        request_booking.requestID  -- Fetching requestID from request_booking table
    FROM booking
    INNER JOIN login ON booking.studentNo = login.studentNo
    INNER JOIN request_booking ON booking.bookingID = request_booking.bookingID";
$bookingResult = mysqli_query($con, $bookingQuery);

// Handle the "Ready to Pickup" button action



require 'phpmailer/vendor/autoload.php'; // Include the Composer autoload file
include("php/config.php"); // Include your database connection

// Handle the "Mark as Ready" button action
if (isset($_POST['markReady'])) {
    $requestID = mysqli_real_escape_string($con, $_POST['requestID']); // Sanitize input

    // Update the request status to 'Ready to Pickup'
    $updateStatusQuery = "UPDATE request SET requestStatus = 'Ready to Pickup' WHERE requestID = '$requestID'";
    if (mysqli_query($con, $updateStatusQuery)) {
        // Fetch the student's email address from the database
        $studentQuery = "
            SELECT login.studentEmail 
            FROM request
            INNER JOIN login ON request.studentNo = login.studentNo
            WHERE request.requestID = '$requestID'";
        $studentResult = mysqli_query($con, $studentQuery);

        if (mysqli_num_rows($studentResult) > 0) {
            $studentRow = mysqli_fetch_assoc($studentResult);
            $studentEmail = $studentRow['studentEmail'];

            // Send email notification
            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'dhariuscarolino@gmail.com'; // Your Gmail address
                $mail->Password   = 'zukm amtr xarv wddp';    // Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Email settings
                $mail->setFrom('dhariuscarolino@gmail.com', 'Admin'); // Sender info
                $mail->addAddress($studentEmail);               // Recipient email

                $mail->isHTML(true);
                $mail->Subject = 'Your Document is Ready for Pickup';
                $mail->Body    = '
                    <h3>Dear Student,</h3>
                    <p>Your requested document is now ready for pickup.</p>
                    <p>Please visit the office to collect it at your convenience.</p>
                    <p>Thank you!</p>';

                $mail->AltBody = 'Your requested document is now ready for pickup. Please visit the office to collect it.';

                $mail->send();
                echo "Email notification sent successfully to $studentEmail.";
            } catch (Exception $e) {
                echo "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Student email not found.";
        }
    } else {
        echo "Failed to update request status.";
    }

    // Redirect to prevent form resubmission
    header("Location: admindashboard.php");
    exit();
}


// Handle the "Mark as Done" button action
if (isset($_POST['markDone'])) {
    // Sanitize input to prevent SQL injection
    $requestID = mysqli_real_escape_string($con, $_POST['requestID']);
    
    // Update the request status to 'Done' for the specific request ID
    $updateRequestStatusQuery = "UPDATE request SET requestStatus = 'Done' WHERE requestID = '$requestID'";
    mysqli_query($con, $updateRequestStatusQuery);
    
    // Remove the corresponding booking entry from the request_booking table
    $deleteBookingQuery = "DELETE FROM request_booking WHERE requestID = '$requestID'";
    mysqli_query($con, $deleteBookingQuery);
    
    // Redirect to prevent form resubmission
    header("Location: admindashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminCSS.css"> <!-- Adjust the path -->
</head>
<body>
<header>
        <img src="ptclogo.png">
        <span>Pateros Technological College</span>
    </header>
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>

        <!-- Document Requests Section -->
        <div class="container">
            <h2>Document Requests</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Student Number</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($requestResult) > 0) {
                    while ($row = mysqli_fetch_assoc($requestResult)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['requestID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['studentNo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['requestDocument']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['requestStatus']) . "</td>";
                        echo "<td>
                            <form method='POST' action='admindashboard.php'>
                                <input type='hidden' name='requestID' value='" . htmlspecialchars($row['requestID']) . "'>
                                <button type='submit' name='markReady' class='ready-btn'>Mark as Ready</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No pending requests.</td></tr>";
                }
                ?>
            </tbody>
            </table>
        </div>

        <!-- Student Bookings Section -->
        <div class="container">
            <h2>Student Bookings</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Booking Date</th>
                        <th>Request ID</th>
                        <th>Action</th> <!-- New column for the action button -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($bookingResult) > 0) {
                        while ($row = mysqli_fetch_assoc($bookingResult)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['bookingID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['studentName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['studentCourse']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['studentYear']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['bookingDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['requestID']) . "</td>";  // Displaying requestID
                            echo "<td>
                                <form method='POST' action='admindashboard.php'>
                                    <input type='hidden' name='requestID' value='" . htmlspecialchars($row['requestID']) . "'>
                                    <button type='submit' name='markDone' class='done-btn'>Mark as Done</button>
                                </form>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No bookings found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>



