<?php
// Include the database connection
include("php/config.php");

// Get the logged-in student's number
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
?>

<div class="step4-content">
    <p>You can now pick up the document/s you've requested at the campus on <strong><?php echo htmlspecialchars($bookingDate); ?></strong>.</p>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Document</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($requestResult) > 0) {
            mysqli_data_seek($requestResult, 0); // Reset the pointer for displaying documents
            while ($row = mysqli_fetch_assoc($requestResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['requestDocument']) . "</td>";
                echo "<td>" . htmlspecialchars($row['requestStatus']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No documents found for this booking.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Button to generate receipt -->
    <button id="generateReceiptBtn" class="btn">Generate Receipt</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById("generateReceiptBtn").addEventListener("click", function() {
    const { jsPDF } = window.jspdf;
    
    // Fetch the data for the receipt
    const bookingID = "<?php echo $bookingID; ?>";
    const bookingDate = "<?php echo $bookingDate; ?>";
    const documents = <?php echo json_encode($documents); ?>;

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
</script>

