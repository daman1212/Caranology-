<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'dtw348');            // your CS username
define('DB_PASS', 'your_db_password'); // your MySQL password (same one you use for titan)
define('DB_NAME', 'dtw348');           // same as your username on UofR

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('DB Error: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
