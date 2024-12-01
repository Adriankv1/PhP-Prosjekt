<?php
// Only start the session if not started AND not after a specific action like deletion
if (session_status() == PHP_SESSION_NONE && !isset($_SESSION['deleting_account'])) {
    session_start(); // Start session if not already started
}

// Check if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo '<div class="user-info">
            Logged in as: <strong>' . htmlspecialchars($_SESSION['username']) . '</strong>
          </div>';
}
?>
