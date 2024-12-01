<?php
class RoomController {
    private $roomModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->roomModel = new RoomModel($db);
    }

    public function searchRooms() {
        // Validate and sanitize input
        $checkIn = isset($_POST['start-date']) ? $this->sanitizeInput($_POST['start-date']) : '';
        $checkOut = isset($_POST['end-date']) ? $this->sanitizeInput($_POST['end-date']) : '';
        $adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 0;
        $children = isset($_POST['children']) ? (int)$_POST['children'] : 0;

        // Validate dates
        if (!$this->validateDates($checkIn, $checkOut)) {
            return ['error' => 'Invalid dates selected'];
        }

        // Search for available rooms
        return $this->roomModel->searchAvailableRooms($checkIn, $checkOut, $adults, $children);
    }

    private function validateDates($checkIn, $checkOut) {
        $checkInDate = strtotime($checkIn);
        $checkOutDate = strtotime($checkOut);
        $today = strtotime(date('Y-m-d'));

        return $checkInDate >= $today && $checkOutDate > $checkInDate;
    }

    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }
}
?>