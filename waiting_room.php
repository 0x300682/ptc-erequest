<?php
session_start();
include("php/config.php");

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit;
}

// Get the student number from the session
$studentNo = $_SESSION['studentNo'];

// Fetch the latest booking date for the logged-in student
$bookingQuery = "SELECT bookingID, bookingDate FROM booking WHERE studentNo = '$studentNo' ORDER BY bookingDate DESC LIMIT 1";
$bookingResult = mysqli_query($con, $bookingQuery);
$bookingRow = mysqli_fetch_assoc($bookingResult);
$bookingID = $bookingRow['bookingID'];
$bookingDate = $bookingRow['bookingDate'];

// Fetch all the documents requested by the student on the same booking date
$requestQuery = "
    SELECT r.requestDocument, r.requestStatus
    FROM request AS r
    INNER JOIN request_booking AS rb ON r.requestID = rb.requestID
    INNER JOIN booking AS b ON rb.bookingID = b.bookingID
    WHERE b.studentNo = '$studentNo' AND b.bookingDate = '$bookingDate'";

$requestResult = mysqli_query($con, $requestQuery);

// Collect the documents for the receipt
$documents = [];
while ($row = mysqli_fetch_assoc($requestResult)) {
    $documents[] = $row['requestDocument'];
}

// If all requests are done, redirect to the dashboard
if (empty($documents)) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room</title>
    <link rel="stylesheet" href="dashCSS.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("generateReceiptBtn").addEventListener("click", function() {
                const { jsPDF } = window.jspdf;

                // Fetch the data for the receipt
                const bookingID = "<?php echo $bookingID; ?>";
                const bookingDate = "<?php echo $bookingDate; ?>";
                const documents = <?php echo json_encode($documents); ?>; // Document names

                // Initialize jsPDF with A6 size
                const doc = new jsPDF({
                    orientation: "portrait",
                    unit: "mm",
                    format: [148, 105] // A6 size
                });

                // Set the header background
                doc.setFillColor(34, 139, 34); // Forest Green
                doc.rect(0, 0, doc.internal.pageSize.width, 20, 'F'); // Reduced height for header

                // Header title
                doc.setTextColor(0, 0, 0); // Black text
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(14);
                doc.text("Booking Confirmation", doc.internal.pageSize.width / 2, 15, { align: "center" });

                // Booking details
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.text(`Booking ID: ${bookingID}`, 20, 35);
                doc.text(`Date: ${bookingDate}`, 20, 45);

                // Documents list
                doc.setFont('helvetica', 'bold');
                doc.text("Documents Requested:", 20, 55);

                doc.setFont('helvetica', 'normal');
                let yPosition = 65;
                documents.forEach((docItem) => {
                    doc.text(`- ${docItem}`, 20, yPosition);
                    yPosition += 8;
                });

                // Footer
                doc.setFillColor(0, 128, 0); // Dark green for footer
                doc.rect(0, doc.internal.pageSize.height - 20, doc.internal.pageSize.width, 20, 'F');
                doc.setTextColor(0, 0, 0);
                doc.setFontSize(8);
                doc.text("Thank you for using e-Request!", doc.internal.pageSize.width / 2, doc.internal.pageSize.height - 8, { align: "center" });

                // Download the PDF
                doc.save(`Booking_Ticket_${bookingID}.pdf`);
            });
        });
    </script>
 ```html
</head>
<body>
    <header>
        <img src="ptclogo.png" alt="PTC Logo">
        <span>Pateros Technological College</span>
    </header>
    <div class="container">
        <h1>Waiting Room</h1>
        <p>Your requested documents are being processed. Please wait...</p>
        <p>You will be redirected to the dashboard once your documents are ready.</p>

        <!-- Display the status of requested documents -->
        <h2>Requested Document Status</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($documents) > 0) {
                foreach ($documents as $document) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($document) . "</td>";
                    echo "<td>Ready to receive</td>"; // Assuming all documents are pending for this example
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No documents found.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <!-- Button to generate a receipt for the current requested document -->
        <h2>Generate Receipt</h2>
        <button id="generateReceiptBtn" class="btn">Generate Receipt</button>
    </div>
</body>
</html>