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
?>
