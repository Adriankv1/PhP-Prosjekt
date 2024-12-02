<?php
// File: controllers/editroomcontroller.php

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
        $query = "SELECT * FROM rooms ORDER BY $sortColumn $sortOrder";
        $result = $this->conn->query($query);

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
        $query = "SELECT * FROM rooms";
        $result = $this->conn->query($query);

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

        $stmt = $this->conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, max_adults = ?, max_children = ?, price_per_night = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param('ssiiissi', $room_number, $room_type, $max_adults, $max_children, $price_per_night, $description, $status, $room_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Room information updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update room information. Error: " . $stmt->error;
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

        $stmt = $this->conn->prepare("INSERT INTO rooms (room_number, room_type, max_adults, max_children, price_per_night, description, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssiiiss', $room_number, $room_type, $max_adults, $max_children, $price_per_night, $description, $status);

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
        $query = "SELECT rooms.room_number, users.username, bookings.check_in_date, bookings.check_out_date 
                  FROM bookings 
                  JOIN rooms ON bookings.room_id = rooms.id
                  JOIN users ON bookings.user_id = users.id";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
        }

        return $bookings;
    }
}
