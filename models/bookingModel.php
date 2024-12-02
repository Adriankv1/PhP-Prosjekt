<?php
class BookingModel {
    private $db;

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to get booking details by booking ID
    public function getBookingDetails($bookingId) {
        // Prepare the SQL statement to fetch booking details
        $stmt = $this->db->prepare("
            SELECT b.*, u.username as customerName, u.email,
                   r.room_type, r.price_per_night
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            WHERE b.id = ?
        ");
        
        // Bind the booking ID parameter to the SQL statement
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();

        // Check if the booking exists
        if (!$booking) {
            return null;
        }

        // Calculate the number of nights for the booking
        $nights = ceil((strtotime($booking['check_out_date']) - strtotime($booking['check_in_date'])) / (60 * 60 * 24));

        // Return the booking details as an associative array
        return [
            'bookingNumber' => $bookingId,
            'customerName' => $booking['customerName'],
            'email' => $booking['email'],
            'checkInDate' => date('d-m-Y', strtotime($booking['check_in_date'])),
            'checkOutDate' => date('d-m-Y', strtotime($booking['check_out_date'])),
            'roomType' => $booking['room_type'],
            'guestCount' => $booking['number_of_adults'] + $booking['number_of_children'],
            'pricePerNight' => $booking['price_per_night'],
            'nights' => $nights,
            'mva' => $booking['total_price'] * 0.25, // Calculate VAT (25% of total price)
            'totalPrice' => $booking['total_price']
        ];
    }
}
?>