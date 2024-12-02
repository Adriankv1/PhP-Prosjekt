<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

// Check if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Display the current logged-in user's username
    echo '<a href="/php-prosjekt/views/pages/Profile.php" class="user-info">
            Logged in as: <strong>' . htmlspecialchars($_SESSION['username']) . '</strong>
          </a>';
} else {
    // Display a login link when no user is logged in
    echo '<a href="/php-prosjekt/views/pages/login.php" class="user-info">Log in</a>';
}
?>
