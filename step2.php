<?php
// Connect to the database
include("php/config.php");

// Retrieve the student ID from the session
$studentID = $_SESSION['studentNo'];

// Query the database for the documents requested by this student
$query = "SELECT requestDocument AS document_name, requestStatus AS status FROM request WHERE studentNo = '$studentID'";
$result = mysqli_query($con, $query); // Use $con for the connection
?>
<div class="step-content">
    <p>Your request has been successfully submitted and is now under processing.</p> 
    <p>Below is the list of your requested documents:</p>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Document Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['document_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No requests found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>



