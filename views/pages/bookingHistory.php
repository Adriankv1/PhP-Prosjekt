<?php
// Include the database configuration file
include('../../config/server.php');

// Check if the booking form is submitted
if (isset($_POST['book_room'])) {
  // Get the user ID from the session
  $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

  // Escape special characters in the form data to prevent SQL injection
  $room_id = mysqli_real_escape_string($db, $_POST['room_id']);
  $booking_date = date('Y-m-d H:i:s'); // Get the current date and time
  $details = mysqli_real_escape_string($db, $_POST['details']);

  // Insert the booking information into the booking_history table
  $query = "INSERT INTO booking_history (user_id, room_id, booking_date, details) 
            VALUES ('$user_id', '$room_id', '$booking_date', '$details')";
  mysqli_query($db, $query); // Execute the query

  // Set a success message in the session
  $_SESSION['success'] = "Room booked successfully";

  // Redirect to the profile page
  header('location: profile.php');
}
?>