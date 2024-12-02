<?php
class RoomModel {
    private $db;

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to search for available rooms based on the given criteria
    public function searchAvailableRooms($checkIn, $checkOut, $adults, $children, $floor = null, $roomType = null) {
        // Sanitize and cast input parameters
        $adults = (int)$adults;
        $children = (int)$children;
        $checkIn = mysqli_real_escape_string($this->db, $checkIn);
        $checkOut = mysqli_real_escape_string($this->db, $checkOut);
        
        // Base query to find available rooms
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
    
        // Add floor filter to the query if provided
        if ($floor) {
            $floor = mysqli_real_escape_string($this->db, $floor);
            $query .= " AND SUBSTRING(r.room_number, 1, 1) = '$floor'";
            $query .= " ORDER BY FIELD(r.room_type, 'cheap', 'standard', 'family', 'deluxe'), r.room_number";
        } 
        // Add room type filter to the query if provided
        elseif ($roomType) {
            $roomType = mysqli_real_escape_string($this->db, $roomType);
            $query .= " AND r.room_type = '$roomType'";
            $query .= " ORDER BY r.room_number";
        }
    
        // Execute the query
        $result = mysqli_query($this->db, $query);
    
        // Check for query execution errors
        if (!$result) {
            return ['error' => 'Database error: ' . mysqli_error($this->db)];
        }
    
        // Fetch all matching rooms and store them in the $rooms array
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
    
        // Return the list of available rooms
        return ['rooms' => $rooms];
    }
}
?>