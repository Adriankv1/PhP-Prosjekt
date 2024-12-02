<?php
class RoomModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function searchAvailableRooms($checkIn, $checkOut, $adults, $children, $floor = null, $roomType = null) {
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
            AND r.status = 'available'";
    
        if ($floor) {
            $floor = mysqli_real_escape_string($this->db, $floor);
            $query .= " AND SUBSTRING(r.room_number, 1, 1) = '$floor'";
            $query .= " ORDER BY FIELD(r.room_type, 'cheap', 'standard', 'family', 'deluxe'), r.room_number";
        } elseif ($roomType) {
            $roomType = mysqli_real_escape_string($this->db, $roomType);
            $query .= " AND r.room_type = '$roomType'";
            $query .= " ORDER BY r.room_number";
        }
    
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