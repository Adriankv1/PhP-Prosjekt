<?php
include('../../config/server.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('location: ../views/auth/login.php');
    exit();
}

if (isset($_POST['delete_profile'])) {
    $username = $_SESSION['username'];
    
    // Start transaction
    mysqli_begin_transaction($db);
    
    try {
        // Get user ID
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
        
        // Call the DeleteUser stored procedure
        $query = "CALL DeleteUser(?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        // Additional cleanup (not handled by the stored procedure)
        
        // Delete user preferences
        $query = "DELETE FROM user_preferences WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        // Delete user stays record
        $query = "DELETE FROM user_stays WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        // Delete guest profile if exists
        $query = "DELETE FROM guest_profiles WHERE user_id=?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        // If everything succeeded, commit the transaction
        mysqli_commit($db);
        
        // Destroy session and redirect to home page
        session_destroy();
        header('location: index.php');
        exit();
        
    } catch (Exception $e) {
        // If there was an error, rollback the transaction
        mysqli_rollback($db);
        $_SESSION['error'] = "Could not delete account: " . $e->getMessage();
        header('location: index.php');
        exit();
    }
}
?>