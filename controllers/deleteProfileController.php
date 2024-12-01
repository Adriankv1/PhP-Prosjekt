<?php
include('../config/server.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['username'])) {
    header('location: ../../php-prosjekt/index.php');
    exit();
}

if (isset($_POST['delete_profile'])) {
    $username = $_SESSION['username'];
    
    error_log("Starting deletion process for user: " . $username);
    
    mysqli_begin_transaction($db);

    try {
        // Get user ID
        $query = "SELECT id FROM users WHERE username=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {
            error_log("User not found: " . $username);
            throw new Exception("User not found");
        }

        $user_id = $user['id'];
        error_log("Found user ID: " . $user_id);

        // Call the improved DeleteUser procedure
        $query = "CALL DeleteUser(?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute DeleteUser procedure: " . mysqli_error($db));
            throw new Exception("Failed to delete user account");
        }

        mysqli_commit($db);
        error_log("Successfully deleted user and archived data");

        // Clear session
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        error_log("Session destroyed");

        header('location: ../../php-prosjekt/index.php');
        exit();

    } catch (Exception $e) {
        error_log("Error during deletion: " . $e->getMessage());
        mysqli_rollback($db);
        $_SESSION['error'] = "Could not delete account: " . $e->getMessage();
        header('location: ../../php-prosjekt/index.php');
        exit();
    }
}
?>