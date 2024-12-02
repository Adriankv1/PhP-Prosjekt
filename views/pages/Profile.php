<?php
include('../../config/server.php');
include './../partials/navbar.php'; 

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
    SELECT b.id, b.booking_date, b.check_in_date, b.check_out_date, 
           b.total_price, r.room_number, r.room_type,
           b.number_of_adults, b.number_of_children 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = " . $user['id'] . " 
    ORDER BY b.booking_date DESC";
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
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleProfile.css">
</head>
<body>
    <div class="profilestyle">
    <h1><?php echo htmlspecialchars($user['username']); ?>'s profile</h1>

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

    <h3>Your Preferred Room Type:</h3>
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
        <th>Rom Nummer</th>
        <th>Rom Type</th>
        <th>Innsjekking</th>
        <th>Utsjekking</th>
        <th>Antall Gjester</th>
        <th>Total Pris</th>
        <th>Kvittering</th>
    </tr>
    <?php foreach ($history as $booking): ?>
        <tr>
            <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($booking['room_type'])); ?></td>
            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($booking['check_in_date']))); ?></td>
            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($booking['check_out_date']))); ?></td>
            <td><?php echo htmlspecialchars($booking['number_of_adults'] + $booking['number_of_children']); ?></td>
            <td><?php echo htmlspecialchars($booking['total_price']); ?> NOK</td>
            <td>
                <a href="../uploads/ordrebekreftelse_<?php echo htmlspecialchars($booking['id']); ?>.pdf" 
                   class="pdf-btn" target="_blank">Se kvittering</a>
            </td>
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

    <div class="delete-profile">
        <h3>Delete Account</h3>
        <p class="warning">Warning: This action cannot be undone. All your data will be permanently deleted.</p>
        <form method="post" action="../../controllers/deleteProfileController.php" onsubmit="return confirmDelete()">
            <button type="submit" class="btn-delete" name="delete_profile">Delete My Account</button>
        </form>
    </div>

    <script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete your account? This action cannot be undone.");
    }
    </script>
    </div>
</body>
</html>
