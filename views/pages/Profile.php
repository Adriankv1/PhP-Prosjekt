<?php
include('../../config/server.php');
include './../partials/navbar.php'; 
include '../partials/user_info.php';


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
$history_query = "
    SELECT bh.id, bh.booking_date, bh.details, r.room_number, r.room_type 
    FROM booking_history bh 
    JOIN rooms r ON bh.room_id = r.id 
    WHERE bh.user_id=" . $user['id'] . " ORDER BY bh.booking_date DESC";
$history_result = mysqli_query($db, $history_query);
$history = mysqli_fetch_all($history_result, MYSQLI_ASSOC);

// Fetch available room types
$room_types_query = "SELECT DISTINCT room_type FROM rooms";
$room_types_result = mysqli_query($db, $room_types_query);
$room_types = mysqli_fetch_all($room_types_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Profile</title>
  <link rel="stylesheet" type="text/css" href="./../../public/css/styleGlobal.css">
  <link rel="stylesheet" type="text/css" href="../../public/css/styleProfile.css">
</head>
<body>
  <h1><?php echo htmlspecialchars($user['username']); ?>'s profile</h1>
  
  <!-- <h2>Update Profile</h2> -->
  <form method="post" action="../../controllers/updateProfileController.php">
    <div class="input-group">
      <label>Username</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
    </div>
    <div class="input-group">
      <label>Email</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
    </div>
    <div class="input-group">
      <button type="submit" class="btn" name="update_user">Update Profile</button>
    </div>
  </form>
  
  <h3>Your Preffered Room Type:</h3>
  <form method="post" action="../../controllers/updatePreferencesController.php">
    <div class="input-group">
      <label>Preferred Room Type</label>
      <select name="preferred_room_type">
        <?php foreach ($room_types as $room_type): ?>
          <option value="<?php echo htmlspecialchars($room_type['room_type']); ?>" 
            <?php echo (isset($preferences[0]['preference_value']) && $preferences[0]['preference_value'] == $room_type['room_type']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars(ucfirst($room_type['room_type'])); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit">Update Preferences</button>
  </form>
  
  <h3>Your Booking History:</h3>
  <table>
    <tr>
      <th>Room Number</th>
      <th>Room Type</th>
      <th>Booking Date</th>
      <th>Details</th>
    </tr>
    <?php foreach ($history as $booking): ?>
      <tr>
        <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
        <td><?php echo htmlspecialchars(ucfirst($booking['room_type'])); ?></td>
        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
        <td><?php echo htmlspecialchars($booking['details']); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if (isset($user['loyalty_level'])): ?>
    <div class="profile">
      <h3>Your Loyalty Level:</h3>
      <p>Loyalty Level: <?php echo htmlspecialchars($user['loyalty_level']); ?></p>
    </div>
  <?php else: ?>
    <p>Error: Loyalty level not found.</p>
  <?php endif; ?>
</body>
</html>
