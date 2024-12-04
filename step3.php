<?php
include("php/config.php");

// Ensure student is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: dashboard.php");
    exit;
}

// Fetch booking data
$studentNo = $_SESSION['studentNo'];

// Query to get booking counts for all dates
$query = "SELECT bookingDate, COUNT(*) as count FROM booking GROUP BY bookingDate";
$result = mysqli_query($con, $query);

$bookedDates = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Ensure the date format is consistent with the JS format YYYY-MM-DD
    $bookedDates[$row['bookingDate']] = (int)$row['count'];
}

// Query to fetch request IDs with status "Ready to Pickup"
$requestQuery = "SELECT requestID FROM request WHERE studentNo = '$studentNo' AND requestStatus = 'Ready to Pickup'";
$requestResult = mysqli_query($con, $requestQuery);

$readyRequestIDs = [];
while ($row = mysqli_fetch_assoc($requestResult)) {
    $readyRequestIDs[] = $row['requestID'];
}

// Encode the request IDs for JavaScript or subsequent PHP use
$readyRequestIDsJSON = json_encode($readyRequestIDs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 3 - Select Pickup Date</title>
    <link rel="stylesheet" href="dashCSS.css">
</head>
<body>
    <header></header>
    <div id="calendar-container">
        <div class="month-controls">
            <button id="prevMonth"><i class='bx bx-left-arrow-alt'></i></button>
            <h2 id="monthYear"></h2>
            <button id="nextMonth"><i class='bx bx-right-arrow-alt'></i></button>
        </div>
        <div id="calendar" class="calendar-grid"></div>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <p id="modalMessage"></p>
            <form id="confirmForm" method="POST" action="save_booking.php">
                <input type="hidden" name="bookingDate" id="selectedDate">
                <input type="hidden" name="readyRequestIDs" value='<?= $readyRequestIDsJSON; ?>'>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        const bookedDates = <?= json_encode($bookedDates); ?>;
    </script>
    <script src="JSDash.js"></script>
</body>
</html>



