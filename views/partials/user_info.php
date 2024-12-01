<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

// Check if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo '<div class="user-info">
            Logged in as: <strong>' . htmlspecialchars($_SESSION['username']) . '</strong>
          </div>';
}
?>

