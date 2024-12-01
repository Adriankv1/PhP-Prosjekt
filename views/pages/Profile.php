<?php
include('../../config/server.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header('location: login.php');
  exit();
}

// Fetch user details
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username='$username' OR email='$username'";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);

// Fetch user preferences
$preferences_query = "SELECT * FROM user_preferences WHERE user_id=" . $user['id'];
$preferences_result = mysqli_query($db, $preferences_query);
$preferences = mysqli_fetch_all($preferences_result, MYSQLI_ASSOC);

// Fetch booking history
$history_query = "SELECT bh.*, r.room_name FROM booking_history bh 
 JOIN rooms r ON bh.room_id = r.id 
 WHERE bh.user_id=" . $user['id'];
$history_result = mysqli_query($db, $history_query);
$history = mysqli_fetch_all($history_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Profile</title>
  <link rel="stylesheet" type="text/css" href="../public/css/styleProfile.css">
</head>
<body>
  <h1>Welcome, <?php echo $user['username']; ?></h1>
  
  <h2>Your Preferences</h2>
  <form method="post" action="update_preferences.php">
    <?php foreach ($preferences as $preference): ?>
      <label><?php echo $preference['preference_key']; ?></label>
      <input type="text" name="preferences[<?php echo $preference['preference_key']; ?>]" value="<?php echo $preference['preference_value']; ?>">
    <?php endforeach; ?>
    <button type="submit">Update Preferences</button>
  </form>
  
  <h2>Your Booking History</h2>
  <ul>
    <?php foreach ($history as $booking): ?>
      <li><?php echo $booking['booking_date']; ?> - Room: <?php echo $booking['room_name']; ?> - Details: <?php echo $booking['details']; ?></li>
    <?php endforeach; ?>
  </ul>
</body>
</html>

<?php
$userId = $_SESSION['user_id'];
$query = "SELECT loyalty_level FROM users WHERE id = $userId";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$loyaltyLevel = $row['loyalty_level'];
?>

<div class="profile">
    <h2>Your Profile</h2>
    <p>Loyalty Level: <?php echo $loyaltyLevel; ?></p>
</div>