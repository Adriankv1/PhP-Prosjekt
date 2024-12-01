<?php
include('../../config/server.php');

// Assuming you have a booking form submission
if (isset($_POST['book_room'])) {
  $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
  $room_id = mysqli_real_escape_string($db, $_POST['room_id']);
  $booking_date = date('Y-m-d H:i:s');
  $details = mysqli_real_escape_string($db, $_POST['details']);

  $query = "INSERT INTO booking_history (user_id, room_id, booking_date, details) 
            VALUES ('$user_id', '$room_id', '$booking_date', '$details')";
  mysqli_query($db, $query);

  $_SESSION['success'] = "Room booked successfully";
  header('location: profile.php');
}
?>