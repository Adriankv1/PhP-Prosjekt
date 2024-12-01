<?php
session_start(); // Start the session at the very top

include('../config/server.php');

// Enable error reporting for debugging (you can remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('location: ../../php-prosjekt/index.php');
    exit();
}

if (isset($_POST['delete_profile'])) {
    // Store username from the session for later use
    $username = $_SESSION['username'];

    // Start transaction for deleting user data
    mysqli_begin_transaction($db);

    try {
        // Get user ID from the database using the stored username
        $query = "SELECT id FROM users WHERE username=? AND is_deleted=0";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {
            throw new Exception("User not found or already deleted");
        }

        $user_id = $user['id'];

        // Delete user directly from users table
        $query = "DELETE FROM users WHERE id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to delete user");
        }

        // Additional cleanup for other associated records

        // Delete user preferences
        $query = "DELETE FROM user_preferences WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to delete user preferences");
        }

        // Delete user stays record
        $query = "DELETE FROM user_stays WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to delete user stays");
        }

        // Delete guest profile if exists
        $query = "DELETE FROM guest_profiles WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to delete guest profile");
        }

        // If everything succeeded, commit the transaction
        mysqli_commit($db);

        // Destroy the session after successful deletion to log the user out
        $_SESSION = []; // Clear all session variables

        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy(); // Completely destroy the session

        // Redirect to home page after successful deletion
        header('location: ../../php-prosjekt/index.php');
        exit();

    } catch (Exception $e) {
        // If there was an error, rollback the transaction
        mysqli_rollback($db);

        // Save the error message in the session
        $_SESSION['error'] = "Could not delete account: " . $e->getMessage();

        // Redirect to the home page or a specific error page
        header('../../php-prosjekt/index.php');
        exit();
    }
}
?>
