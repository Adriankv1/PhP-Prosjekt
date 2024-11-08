<?php
class RoomModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function searchAvailableRooms($checkIn, $checkOut, $adults, $children) {
        // Sanitize inputs
        $adults = (int)$adults;
        $children = (int)$children;
        $checkIn = mysqli_real_escape_string($this->db, $checkIn);
        $checkOut = mysqli_real_escape_string($this->db, $checkOut);

        $query = "
            SELECT r.* 
            FROM rooms r
            WHERE r.max_adults >= $adults 
            AND r.max_children >= $children
            AND r.id NOT IN (
                SELECT room_id 
                FROM bookings 
                WHERE (check_in_date <= '$checkOut' AND check_out_date >= '$checkIn')
            )
            AND r.status = 'available'
        ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return ['error' => 'Database error: ' . mysqli_error($this->db)];
        }

        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }

        return ['rooms' => $rooms];
    }
}