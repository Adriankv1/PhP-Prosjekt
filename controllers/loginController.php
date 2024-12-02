<?php
// Start the session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../../config/server.php');

// Initialize variables
$identifier = "";
$errors = array(); 

// LOGIN USER
if (isset($_POST['login_user'])) {
    // Receive all input values from the form
    $identifier = mysqli_real_escape_string($db, $_POST['identifier']);
    $password = $_POST['password']; // No need to escape since it's not used in a query directly

    // Form validation: ensure that the form is correctly filled
    if (empty($identifier)) {
        array_push($errors, "Username or Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // Check if the user is locked out
    $lockout_time = strtotime('-1 hour');
    $stmt = mysqli_prepare($db, "SELECT COUNT(*) AS attempts FROM login_attempts WHERE identifier = ? AND attempt_time > FROM_UNIXTIME(?)");
    mysqli_stmt_bind_param($stmt, "si", $identifier, $lockout_time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $attempts_row = mysqli_fetch_assoc($result);
    $attempts = $attempts_row ? $attempts_row['attempts'] : 0;

    if ($attempts >= 3) {
        array_push($errors, "Too many failed login attempts. Please try again after one hour.");
    }

    // If there are no errors, proceed to check the user in the database
    if (count($errors) == 0) {
        // Use prepared statements to prevent SQL injection
        $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_deleted = 0 LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
        mysqli_stmt_execute($stmt);
        $results = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($results)) {
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Set session variables upon successful login
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['success'] = "You are now logged in";

                // Clear login attempts on successful login
                $stmt = mysqli_prepare($db, "DELETE FROM login_attempts WHERE identifier = ?");
                mysqli_stmt_bind_param($stmt, "s", $identifier);
                mysqli_stmt_execute($stmt);

                header('location: ../../index.php');
                exit();
            } else {
                array_push($errors, "Wrong username/password combination");
            }
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }

    // Log the failed login attempt
    if (count($errors) > 0) {
        $stmt = mysqli_prepare($db, "INSERT INTO login_attempts (identifier) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        mysqli_stmt_execute($stmt);
    }
}
?>
