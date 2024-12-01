<?php
include('../../config/server.php');

// initializing variables
$identifier = "";
$errors = array(); 

// LOGIN USER
if (isset($_POST['login_user'])) {
  // receive all input values from the form
  $identifier = mysqli_real_escape_string($db, $_POST['identifier']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  // form validation: ensure that the form is correctly filled
  if (empty($identifier)) {
    array_push($errors, "Username or Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  // Check if the user is locked out
  $lockout_time = strtotime('-1 hour');
  $query = "SELECT COUNT(*) AS attempts FROM login_attempts WHERE identifier='$identifier' AND attempt_time > FROM_UNIXTIME($lockout_time)";
  $result = mysqli_query($db, $query);
  $attempts = mysqli_fetch_assoc($result)['attempts'];

  if ($attempts >= 3) {
    array_push($errors, "Too many failed login attempts. Please try again after one hour.");
  }

  // If there are no errors, proceed to check the user in the database
  if (count($errors) == 0) {
    $query = "SELECT * FROM users WHERE username='$identifier' OR email='$identifier' LIMIT 1";
    $results = mysqli_query($db, $query);

    if (mysqli_num_rows($results) == 1) {
      $user = mysqli_fetch_assoc($results);
      if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $identifier;
        $_SESSION['user_id'] = $user['id']; // Set the user_id in the session
        $_SESSION['success'] = "You are now logged in";

        // Clear login attempts on successful login
        $query = "DELETE FROM login_attempts WHERE identifier='$identifier'";
        mysqli_query($db, $query);

        header('location: index.php');
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
    $query = "INSERT INTO login_attempts (identifier) VALUES ('$identifier')";
    mysqli_query($db, $query);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  <link rel="stylesheet" type="text/css" href="../../public/css/styleLogin.css">
</head>
<body>