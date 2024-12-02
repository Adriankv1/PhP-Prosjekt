<?php
// Start the session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../../config/server.php');

// Initialize variables
$username = "";
$email    = "";
$errors = array(); 

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username   = mysqli_real_escape_string($db, $_POST['brukernavn']);
    $email      = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = $_POST['password_1'];
    $password_2 = $_POST['password_2'];

    // Form validation: ensure that the form is correctly filled
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email))    { array_push($errors, "Email is required"); }
    if (empty($password_1)) { array_push($errors, "Password is required"); }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // Password requirements: at least one uppercase letter, one lowercase letter, and one special character
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W]).{8,}$/', $password_1)) {
        array_push($errors, "Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one special character");
    }

    // Check if user exists using a prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Register user if there are no errors in the form
    if (count($errors) == 0) {
        // Encrypt the password before saving in the database using password_hash
        $password_hashed = password_hash($password_1, PASSWORD_DEFAULT);

        // Insert user into the database using a prepared statement
        $stmt = mysqli_prepare($db, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hashed);
        mysqli_stmt_execute($stmt);

        // Set session variables upon successful registration
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id']  = mysqli_insert_id($db); // Get the newly inserted user ID
        $_SESSION['success']  = "You are now logged in";

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        header('location: ../../../../php-prosjekt/index.php');
        exit();
    }
}

// CREATE DEFAULT USERS (ADMIN AND USER)
if (isset($_POST['create_default_users'])) {
    // Check if admin user already exists
    $stmtCheckAdmin = mysqli_prepare($db, "SELECT * FROM users WHERE username = ? LIMIT 1");
    $adminUsername = "admin";
    mysqli_stmt_bind_param($stmtCheckAdmin, "s", $adminUsername);
    mysqli_stmt_execute($stmtCheckAdmin);
    $resultAdmin = mysqli_stmt_get_result($stmtCheckAdmin);
    
    if (mysqli_num_rows($resultAdmin) == 0) {
        // Hash password for admin
        $adminPassword = password_hash('123', PASSWORD_DEFAULT);
        // Insert admin user
        $stmtAdmin = mysqli_prepare($db, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $adminEmail = "admin@admin.com";
        mysqli_stmt_bind_param($stmtAdmin, "sss", $adminUsername, $adminEmail, $adminPassword);
        if (mysqli_stmt_execute($stmtAdmin)) {
            $_SESSION['message'] = "Admin user created successfully.";
        } else {
            $_SESSION['message'] = "Failed to create admin user: " . mysqli_error($db);
        }
    } else {
        $_SESSION['message'] = "Admin user already exists.";
    }

    // Check if regular user already exists
    $stmtCheckUser = mysqli_prepare($db, "SELECT * FROM users WHERE username = ? LIMIT 1");
    $userUsername = "user";
    mysqli_stmt_bind_param($stmtCheckUser, "s", $userUsername);
    mysqli_stmt_execute($stmtCheckUser);
    $resultUser = mysqli_stmt_get_result($stmtCheckUser);

    if (mysqli_num_rows($resultUser) == 0) {
        // Hash password for user
        $userPassword = password_hash('123', PASSWORD_DEFAULT);
        // Insert regular user
        $stmtUser = mysqli_prepare($db, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'guest')");
        $userEmail = "user@user.com";
        mysqli_stmt_bind_param($stmtUser, "sss", $userUsername, $userEmail, $userPassword);
        if (mysqli_stmt_execute($stmtUser)) {
            $_SESSION['message'] .= " User account created successfully.";
        } else {
            $_SESSION['message'] .= " Failed to create user account: " . mysqli_error($db);
        }
    } else {
        $_SESSION['message'] .= " User account already exists.";
    }

    // Redirect to the same page to show the message
    header('location: registrer.php');
    exit();
}
