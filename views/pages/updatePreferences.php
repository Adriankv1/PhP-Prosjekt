<?php
include('../../config/server.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header('location: login.php');
  exit();
}

if (isset($_POST['preferences'])) {
  // Get the user ID from the session
  $userId = $_SESSION['user_id'];

  // Update user preferences
  foreach ($_POST['preferences'] as $key => $value) {
    $key = mysqli_real_escape_string($db, $key);
    $value = mysqli_real_escape_string($db, $value);
    $query = "UPDATE user_preferences SET preference_value='$value' WHERE user_id=$userId AND preference_key='$key'";
    mysqli_query($db, $query);
  }

  // Update preferred room type
  if (isset($_POST['preferred_room_type'])) {
    $preferred_room_type = mysqli_real_escape_string($db, $_POST['preferred_room_type']);
    $query = "UPDATE user_preferences SET preference_value='$preferred_room_type' WHERE user_id=$userId AND preference_key='preferred_room_type'";
    mysqli_query($db, $query);
  }

  // Redirect back to the profile page
  header('location: ../pages/Profile.php');
  exit();
}
?>