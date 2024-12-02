<?php
// Start the session to manage user authentication
session_start();

// Include necessary files for database connection, models, services, and controllers
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/bookingModel.php';
require_once __DIR__ . '/../../services/PDFService.php';
require_once __DIR__ . '/../../controllers/bookingController.php';

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query to get the user's preferred room type from the database
$preferences_query = "SELECT preference_value FROM user_preferences WHERE user_id = $user_id AND preference_key = 'preferred_room_type'";
$preferences_result = mysqli_query($db, $preferences_query);
$preferred_room_type = mysqli_fetch_assoc($preferences_result)['preference_value'] ?? '';

// Handle the booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    // Instantiate the BookingController with the database connection
    $bookingController = new BookingController($db);
    // Process the booking with the form data
    $result = $bookingController->processBooking($_POST);
    
    // If booking is successful, redirect to the confirmation page
    if ($result['success']) {
        header('Location: confirmation.php?booking_id=' . $result['booking_id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include '../partials/navbar.php'; ?>
    
    <div class="booking-container">
        <h2>Book a Room</h2>
        <!-- Booking form -->
        <form method="post" action="">
            <div class="input-group">
                <label>Preferred Room Type</label>
                <select name="room_type">
                    <option value="deluxe" <?php echo $preferred_room_type == 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                    <option value="family" <?php echo $preferred_room_type == 'family' ? 'selected' : ''; ?>>Family</option>
                    <option value="standard" <?php echo $preferred_room_type == 'standard' ? 'selected' : ''; ?>>Standard</option>
                    <option value="cheap" <?php echo $preferred_room_type == 'cheap' ? 'selected' : ''; ?>>Cheap</option>
                </select>
            </div>
            <!-- Add other booking fields here -->
            <button type="submit" name="book">Book Now</button>
        </form>
    </div>
</body>
</html>