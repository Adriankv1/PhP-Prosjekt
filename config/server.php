<?php
// Start the session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'registration');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
