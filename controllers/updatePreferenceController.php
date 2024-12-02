<?php
include('../../config/server.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $preferred_room_type = mysqli_real_escape_string($db, $_POST['preferred_room_type']);

    $query = "INSERT INTO user_preferences (user_id, preference_key, preference_value) 
              VALUES ($user_id, 'preferred_room_type', '$preferred_room_type')
              ON DUPLICATE KEY UPDATE preference_value='$preferred_room_type'";
    if (mysqli_query($db, $query)) {
        echo json_encode(['success' => true, 'message' => 'Preferences updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating preferences: ' . mysqli_error($db)]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>