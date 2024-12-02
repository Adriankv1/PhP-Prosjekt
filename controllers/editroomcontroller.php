<?php
// File: controllers/editroomcontroller.php

// Include the server configuration file
require_once '../../config/server.php';

class RoomController
{
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Method to get all rooms sorted dynamically
    public function getAllRoomsSorted($sortColumn = 'room_number', $sortOrder = 'asc')
    {
        // Define valid columns to prevent SQL injection
        $validColumns = ['room_number', 'room_type', 'max_adults', 'max_children', 'price_per_night', 'status'];
        if (!in_array($sortColumn, $validColumns)) {
            $sortColumn = 'room_number';
        }

        // Ensure sort order is either ASC or DESC
        $sortOrder = $sortOrder === 'desc' ? 'DESC' : 'ASC';

        $rooms = [];
        // Query to get all rooms sorted by the specified column and order
        $query = "SELECT * FROM rooms ORDER BY $sortColumn $sortOrder";
        $result = $this->conn->query($query);

        // Fetch all rooms and store them in the $rooms array
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }

        return $rooms;
    }

    // Method to get all rooms (basic, unsorted)
    public function getAllRooms()
    {
        $rooms = [];
        // Query to get all rooms
        $query = "SELECT * FROM rooms";
        $result = $this->conn->query($query);

        // Fetch all rooms and store them in the $rooms array
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }

        return $rooms;
    }

    // Method to update a room
    public function updateRoom($data)
    {
        $room_id = $data['room_id'];
        $room_number = $data['room_number'];
        $room_type = $data['room_type'];
        $max_adults = $data['max_adults'];
        $max_children = $data['max_children'];
        $price_per_night = $data['price_per_night'];
        $description = $data['description'];
        $status = $data['status'];

        // Check if the room status is being updated to 'available' from 'occupied'
        $currentStatusQuery = "SELECT status FROM rooms WHERE id = ?";
        $stmt = $this->conn->prepare($currentStatusQuery);
        $stmt->bind_param('i', $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentStatus = $result->fetch_assoc()['status'];

        // If the room is currently occupied and we are changing it to available, delete the booking
        if ($currentStatus === 'occupied' && $status === 'available') {
            // Delete the booking related to this room (assuming the bookings table has a room_id column)
            $deleteBookingQuery = "DELETE FROM bookings WHERE room_id = ?";
            $deleteStmt = $this->conn->prepare($deleteBookingQuery);
            $deleteStmt->bind_param('i', $room_id);
            $deleteStmt->execute();
        }

        // Update the room details in the rooms table
        $updateQuery = "UPDATE rooms SET room_number = ?, room_type = ?, max_adults = ?, max_children = ?, price_per_night = ?, description = ?, status = ? WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param('ssiiissi', $room_number, $room_type, $max_adults, $max_children, $price_per_night, $description, $status, $room_id);

        // Execute the update statement and set a session message based on the result
        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Room information updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update room information. Error: " . $updateStmt->error;
        }

        // Redirect back to prevent resubmission
        header('Location: admin.php');
        exit();
    }

    // Method to add a new room
    public function addRoom($data)
    {
        $room_number = $data['room_number'];
        $room_type = $data['room_type'];
        $max_adults = $data['max_adults'];
        $max_children = $data['max_children'];
        $price_per_night = $data['price_per_night'];
        $description = $data['description'];
        $status = $data['status'];

        // Prepare the insert statement
        $stmt = $this->conn->prepare("INSERT INTO rooms (room_number, room_type, max_adults, max_children, price_per_night, description, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssiiiss', $room_number, $room_type, $max_adults, $max_children, $price_per_night, $description, $status);

        // Execute the insert statement and set a session message based on the result
        if ($stmt->execute()) {
            $_SESSION['message'] = "New room added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add new room. Error: " . $stmt->error;
        }

        // Redirect back to admin.php
        header('Location: admin.php');
        exit();
    }

    // Method to get booking information for all rooms
    public function getRoomBookings()
    {
        $bookings = [];
        // Query to get booking information for all rooms
        $query = "SELECT rooms.room_number, users.username, bookings.check_in_date, bookings.check_out_date 
                  FROM bookings 
                  JOIN rooms ON bookings.room_id = rooms.id
                  JOIN users ON bookings.user_id = users.id";
        $result = $this->conn->query($query);

        // Fetch all bookings and store them in the $bookings array
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
        }

        return $bookings;
    }
}
?>