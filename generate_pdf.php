<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// Include the required files
require_once 'php/config.php';
require_once 'vendor/autoload.php';

session_start();

// Get the studentNo from the session
$studentNo = $_SESSION['studentNo'];

// Fetch the requested documents for the student
$requestQuery = "SELECT requestDocument FROM request WHERE studentNo = '$studentNo'";
$requestResult = mysqli_query($con, $requestQuery);

// Collect documents
$documents = [];
while ($row = mysqli_fetch_assoc($requestResult)) {
    $documents[] = $row['requestDocument'];
}

// Get the booking date from the POST request
$bookingDate = $_POST['bookingDate'];

// Load the required libraries
use Spipu\Html2Pdf\Html2Pdf;

try {
    // Create an instance of Html2Pdf
    $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [10, 10, 10, 10]);

    // Set the HTML content
    $html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        .styled-table, tr, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Receipt for Document Request</h1>
    <p>Student Number: ' . htmlspecialchars($studentNo) . '</p>
    <p>Booking Date: ' . htmlspecialchars ($bookingDate) . '</p>
    <h2>Requested Documents</h2>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Document</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($documents as $document) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($document) . '</td>
            </tr>';
    }

    $html .= '
        </tbody>
    </table>
</body>
</html>
';

    // Set the HTML content to the PDF
    $html2pdf->writeHTML($html);

    // Output the PDF
    $html2pdf->output('receipt.pdf');
} catch (Html2PdfException $e) {
    $html2pdf->clean();
    // Handle the exception
}
?>