<?php
class RoomController {
    private $roomModel;
    private $db;

    // Constructor to initialize the database connection and room model
    public function __construct($db) {
        $this->db = $db;
        $this->roomModel = new RoomModel($db);
    }

    // Method to search for available rooms based on user input
    public function searchRooms() {
        // Sanitize and retrieve user input
        $checkIn = isset($_POST['start-date']) ? $this->sanitizeInput($_POST['start-date']) : '';
        $checkOut = isset($_POST['end-date']) ? $this->sanitizeInput($_POST['end-date']) : '';
        $adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 0;
        $children = isset($_POST['children']) ? (int)$_POST['children'] : 0;
        $floor = isset($_POST['floor']) ? $this->sanitizeInput($_POST['floor']) : null;
        $roomType = isset($_POST['room_type']) ? $this->sanitizeInput($_POST['room_type']) : null;
    
        // Validate the check-in and check-out dates
        if (!$this->validateDates($checkIn, $checkOut)) {
            return ['error' => 'Invalid dates selected'];
        }
    
        // Search for available rooms using the room model
        return $this->roomModel->searchAvailableRooms($checkIn, $checkOut, $adults, $children, $floor, $roomType);
    }

    // Method to validate the check-in and check-out dates
    private function validateDates($checkIn, $checkOut) {
        $checkInDate = strtotime($checkIn);
        $checkOutDate = strtotime($checkOut);
        $today = strtotime(date('Y-m-d'));

        // Ensure check-in date is today or later and check-out date is after check-in date
        return $checkInDate >= $today && $checkOutDate > $checkInDate;
    }

    // Method to sanitize user input
    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    // Method to get user preferences for room type
    public function getUserPreferences($user_id) {
        $query = "SELECT preference_value FROM user_preferences WHERE user_id = ? AND preference_key = 'preferred_room_type'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>