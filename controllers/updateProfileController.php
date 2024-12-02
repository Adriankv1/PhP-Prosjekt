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

    // Sanitize input data
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);

    // Validate input (add more checks as needed)
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Username and email are required!']);
        exit();
    }

    // Update user information in the database
    $query = "UPDATE users 
              SET username = '$username', email = '$email'
              WHERE id = $user_id";

    if (mysqli_query($db, $query)) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . mysqli_error($db)]);
    }
    exit();
} else {
    // Return error if accessed without POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>