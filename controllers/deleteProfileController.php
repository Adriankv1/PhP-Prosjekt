<?php

// Include the server configuration file
include('../config/server.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the homepage if the user is not logged in
    header('location: ../../php-prosjekt/index.php');
    exit();
}

// Check if the delete profile form has been submitted
if (isset($_POST['delete_profile'])) {
    $username = $_SESSION['username'];
    
    // Log the start of the deletion process
    error_log("Starting deletion process for user: " . $username);
    
    // Begin a database transaction
    mysqli_begin_transaction($db);

    try {
        // Get user ID based on the username
        $query = "SELECT id FROM users WHERE username=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Check if the user exists
        if (!$user) {
            error_log("User not found: " . $username);
            throw new Exception("User not found");
        }

        $user_id = $user['id'];
        error_log("Found user ID: " . $user_id);

        // Call the stored procedure to delete the user
        $query = "CALL DeleteUser(?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        // Execute the stored procedure and check for errors
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute DeleteUser procedure: " . mysqli_error($db));
            throw new Exception("Failed to delete user account");
        }

        // Commit the transaction
        mysqli_commit($db);
        error_log("Successfully deleted user and archived data");

        // Clear the session data
        $_SESSION = array();
        
        // Destroy the session cookie if it exists
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();
        error_log("Session destroyed");

        // Redirect to the homepage
        header('location: ../../php-prosjekt/index.php');
        exit();

    } catch (Exception $e) {
        // Log the error and rollback the transaction
        error_log("Error during deletion: " . $e->getMessage());
        mysqli_rollback($db);
        $_SESSION['error'] = "Could not delete account: " . $e->getMessage();
        header('location: ../../php-prosjekt/index.php');
        exit();
    }
}
?>