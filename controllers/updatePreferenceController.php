<?php
include('../../config/server.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $preferred_room_type = mysqli_real_escape_string($db, $_POST['preferred_room_type']);

  // Update or insert preference
  $query = "INSERT INTO user_preferences (user_id, preference_key, preference_value) 
            VALUES ($user_id, 'preferred_room_type', '$preferred_room_type')
            ON DUPLICATE KEY UPDATE preference_value='$preferred_room_type'";
  if (mysqli_query($db, $query)) {
    header('Location: ../views/profile.php');
    exit();
  } else {
    echo "Error updating preferences: " . mysqli_error($db);
  }
}
?>
