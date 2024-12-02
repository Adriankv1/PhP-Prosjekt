<?php
// File: views/pages/admin.php

// Start a session to ensure only admin users access this page
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Include the room controller and database connection
require_once '../../config/server.php';
require_once '../../controllers/editroomcontroller.php';
include './../partials/navbar.php';

// Instantiate the RoomController with the database connection
$roomController = new RoomController($db);

// Get the sorting parameters from the URL if available
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'room_number';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

// Toggle the sort order for the next click
$nextSortOrder = $sortOrder === 'asc' ? 'desc' : 'asc';

// Fetch room information from the controller, sorted by selected criteria
$rooms = $roomController->getAllRoomsSorted($sortColumn, $sortOrder);

// Fetch booking information
$bookings = $roomController->getRoomBookings();

// Handle the room update if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {
    $roomController->updateRoom($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Room Management</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
    <link rel="stylesheet" href="../../public/css/styleAdmin.css">
</head>
<body>
    <div class="container">
        <h1>Admin Room Management</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <!-- Display room bookings -->
        <h2>Room Bookings</h2>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Booked By</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_in_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_out_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Display room management -->
        <h2>Manage Rooms</h2>
        <table class="room-table">
            <thead>
                <tr>
                    <th><a href="?sort=room_number&order=<?php echo $nextSortOrder; ?>">Room Number</a></th>
                    <th><a href="?sort=room_type&order=<?php echo $nextSortOrder; ?>">Room Type</a></th>
                    <th><a href="?sort=max_adults&order=<?php echo $nextSortOrder; ?>">Max Adults</a></th>
                    <th><a href="?sort=max_children&order=<?php echo $nextSortOrder; ?>">Max Children</a></th>
                    <th><a href="?sort=price_per_night&order=<?php echo $nextSortOrder; ?>">Price Per Night</a></th>
                    <th>Description</th>
                    <th><a href="?sort=status&order=<?php echo $nextSortOrder; ?>">Status</a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr class="room-row <?php echo strtolower($room['status']); ?>">
                        <form method="POST" action="">
                            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                            <td><input type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>"></td>
                            <td>
                                <select name="room_type">
                                    <option value="deluxe" <?php echo $room['room_type'] === 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                                    <option value="family" <?php echo $room['room_type'] === 'family' ? 'selected' : ''; ?>>Family</option>
                                    <option value="standard" <?php echo $room['room_type'] === 'standard' ? 'selected' : ''; ?>>Standard</option>
                                    <option value="cheap" <?php echo $room['room_type'] === 'cheap' ? 'selected' : ''; ?>>Cheap</option>
                                </select>
                            </td>
                            <td><input type="number" name="max_adults" value="<?php echo htmlspecialchars($room['max_adults']); ?>"></td>
                            <td><input type="number" name="max_children" value="<?php echo htmlspecialchars($room['max_children']); ?>"></td>
                            <td><input type="number" step="0.01" name="price_per_night" value="<?php echo htmlspecialchars($room['price_per_night']); ?>"></td>
                            <td><input type="text" name="description" value="<?php echo htmlspecialchars($room['description']); ?>"></td>
                            <td>
                                <select name="status">
                                    <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                    <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                                    <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                </select>
                            </td>
                            <td><button type="submit" name="update_room">Update</button></td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add new room form -->
        <h2>Add New Room</h2>
        <form method="POST" action="" class="add-room-form">
            <label for="room_number">Room Number:</label>
            <input type="text" name="room_number" required>

            <label for="room_type">Room Type:</label>
            <select name="room_type" required>
                <option value="deluxe">Deluxe</option>
                <option value="family">Family</option>
                <option value="standard">Standard</option>
                <option value="cheap">Cheap</option>
            </select>

            <label for="max_adults">Max Adults:</label>
            <input type="number" name="max_adults" required>

            <label for="max_children">Max Children:</label>
            <input type="number" name="max_children" required>

            <label for="price_per_night">Price Per Night:</label>
            <input type="number" step="0.01" name="price_per_night" required>

            <label for="description">Description:</label>
            <input type="text" name="description">

            <label for="status">Status:</label>
            <select name="status">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
            </select>

            <button type="submit" name="add_room">Add Room</button>
        </form>
    </div>
</body>
</html>
