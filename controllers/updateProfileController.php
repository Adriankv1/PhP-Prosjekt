<?php
include('../../config/server.php');

// Check if the request is a POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Fetch the user ID from session
  if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
  }

  $user_id = $_SESSION['user_id'];

  // Sanitize input data
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);

  // Validate input (add more checks as needed)
  if (empty($username) || empty($email)) {
    $_SESSION['error'] = 'Username and email are required!';
    header('Location: ../views/profile.php');
    exit();
  }

  // Update user information in the database
  $query = "UPDATE users 
            SET username = '$username', email = '$email'
            WHERE id = $user_id";

  if (mysqli_query($db, $query)) {
    $_SESSION['success'] = 'Profile updated successfully!';
    header('Location: ../views/profile.php');
    exit();
  } else {
    $_SESSION['error'] = 'Error updating profile: ' . mysqli_error($db);
    header('Location: ../views/profile.php');
    exit();
  }
} else {
  // Redirect if accessed without POST
  header('Location: ../views/profile.php');
  exit();
}
?>
