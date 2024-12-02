<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <ul>
        <li><a href="/php-prosjekt/index.php">Home</a></li>
        <li><a href="/php-prosjekt/views/pages/rombooking.php">Book rom her</a></li>
        <li><a href="/php-prosjekt/views/pages/Profile.php">Profil</a></li>

        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // Start right-aligned items
            ?>
            <li class="right"><a href="/php-prosjekt/controllers/logoutcontroller.php">Logg ut</a></li>
            <li><?php include 'user_info.php'; ?></li>
            <?php
        } else {
            // Start right-aligned items
            ?>
            <li class="right"><a href="/php-prosjekt/views/pages/registrer.php">Registrering</a></li>
            <li><a href="/php-prosjekt/views/pages/login.php">Log in</a></li>
            <?php
        }
        ?>
    </ul>
</nav>
