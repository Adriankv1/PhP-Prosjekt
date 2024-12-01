<?php
include('../../config/server.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header('location: login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_SESSION['username'];
  $query = "SELECT * FROM users WHERE username='$username' OR email='$username'";
  $result = mysqli_query($db, $query);
  $user = mysqli_fetch_assoc($result);
  $user_id = $user['id'];

  foreach ($_POST['preferences'] as $key => $value) {
    $key = mysqli_real_escape_string($db, $key);
    $value = mysqli_real_escape_string($db, $value);
    $update_query = "INSERT INTO user_preferences (user_id, preference_key, preference_value) VALUES ($user_id, '$key', '$value') ON DUPLICATE KEY UPDATE preference_value='$value'";
    mysqli_query($db, $update_query);
  }

  $_SESSION['success'] = "Preferences updated successfully";
  header('location: profile.php');
}
?>