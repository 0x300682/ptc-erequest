<?php
// Define database credentials as constants only if they are not already defined
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'erequestdb');

// Establish the connection
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

