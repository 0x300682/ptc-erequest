<?php
// Start the session and include database configuration
session_start();
include("php/config.php");

// Fetch all document requests for the logged-in student
$studentNo = $_SESSION['studentNo'];
$requestQuery = "SELECT requestStatus FROM request WHERE studentNo = '$studentNo'";
$requestResult = mysqli_query($con, $requestQuery);

// Initialize flags for the document statuses
$allDone = true;
$hasPending = false;
$hasReadyToPickup = false;
$hasBooked = false;

while ($row = mysqli_fetch_assoc($requestResult)) {
    if ($row['requestStatus'] !== 'Done') {
        $allDone = false;
    }
    if ($row['requestStatus'] === 'Pending') {
        $hasPending = true;
    }
    if ($row['requestStatus'] === 'Ready to Pickup') {
        $hasReadyToPickup = true;
    }
    if ($row['requestStatus'] === 'Booked') {
        $hasBooked = true;
    }
}

// Determine the current step
if ($allDone) {
    $_SESSION['currentStep'] = 1; // All requests are "Done"
} else {
    if ($hasPending) {
        $_SESSION['currentStep'] = 2; // There are pending requests
    } elseif ($hasReadyToPickup) {
        $_SESSION['currentStep'] = 3; // Some requests are "Ready to Pickup"
    } elseif ($hasBooked) {
        $_SESSION['currentStep'] = 4; // A document request is "Booked"
    } else {
        $_SESSION['currentStep'] = 1; // Default step if none of the above
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Request: Online Document Request, Scheduling and Retrieval System</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashCSS.css">
</head>
<body>
    <header>
        <img src="ptclogo.png">
        <span>Pateros Technological College</span>
    </header>
    <nav>
        <button id="toggleSidebar">Dashboard</button>
        <span class="nav-title">e-Request</span>
        <a href="php/logout.php"> <button id="toggleSidebar">Log Out</button> </a>
    </nav>

    <div class="container">
        <aside class="sidebar">
            <h3>Dashboard</h3>
            <p>Name: <?php echo $_SESSION['studentName']; ?></p>
            <p>Student ID: <?php echo $_SESSION['studentNo']; ?></p>
            <p>Course: <?php echo $_SESSION['studentCourse']; ?></p>
            <p>Year Level: <?php echo $_SESSION['studentYear']; ?></p>
        </aside>

        <main>
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="step <?php echo (int)$_SESSION['currentStep'] >= 1 ? 'active' : ''; ?>" data-step="1">1</div>
                    <div class="line"></div>
                    <div class="step <?php echo (int)$_SESSION['currentStep'] >= 2 ? 'active' : ''; ?>" data-step="2">2</div>
                    <div class="line"></div>
                    <div class="step <?php echo (int)$_SESSION['currentStep'] >= 3 ? 'active' : ''; ?>" data-step="3">3</div>
                    <div class="line"></div>
                    <div class="step <?php echo (int)$_SESSION['currentStep'] >= 4 ? 'active' : ''; ?>" data-step="4">4</div>
                </div>
                <div id="stepContent" class="step-content">
                    <?php
                    // Dynamically include the appropriate step content
                    if ((int)$_SESSION['currentStep'] == 1) {
                        echo '<h3>Choose a Document</h3>';
                        include('step1.php');
                    } elseif ((int)$_SESSION['currentStep'] == 2) {
                        echo '<h3>Request is Pending</h3>';
                        include('step2.php');
                    } elseif ((int)$_SESSION['currentStep'] == 3) {
                        echo '<h3>Pick a Date</h3>';
                        include('step3.php');
                    } elseif ((int)$_SESSION['currentStep'] == 4) {
                        echo '<h3>Booking Confirmed</h3>';
                        include('step4.php');
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script src="JSDash.js"></script>
</body>
</html>




