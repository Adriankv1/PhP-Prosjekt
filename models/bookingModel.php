<?php
class BookingModel {
    public function getBookingDetails($bookingId) {
        // Her ville du typisk kjøre en spørring til databasen for å hente informasjon
        // Eksempel (pseudo):
        return [
            'bookingNumber' => $bookingId,
            'customerName' => 'Ola Nordmann',
            'phone' => '99999999',
            'email' => 'ola@nordmann.no',
            'checkInDate' => '01-12-2024',
            'checkOutDate' => '03-12-2024',
            'roomType' => 'Dobbeltrom',
            'guestCount' => '2',
            'pricePerNight' => '1000',
            'nights' => '2',
            'mva' => '500',
            'totalPrice' => '2500'
        ];
    }
}

// New model for handling bookings
class RoomBookingModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createBooking($bookingData) {
        $stmt = $this->db->prepare("
            INSERT INTO bookings (room_id, user_id, check_in_date, check_out_date, 
                                number_of_adults, number_of_children, total_price)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("iissiid", 
            $bookingData['room_id'],
            $bookingData['user_id'],
            $bookingData['check_in_date'],
            $bookingData['check_out_date'],
            $bookingData['number_of_adults'],
            $bookingData['number_of_children'],
            $bookingData['total_price']
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }
}
?>
