<?php
class BookingController {
    private $db;
    private $roomBookingModel;

    public function __construct($db) {
        $this->db = $db;
        $this->roomBookingModel = new RoomBookingModel($db);
    }

    public function generatePDF($bookingId) {
        $bookingModel = new BookingModel();
        $bookingData = $bookingModel->getBookingDetails($bookingId);
        
        if ($bookingData) {
            require_once 'services/PDFService.php';
            $pdfService = new PDFService();
            $pdfService->generateBookingPDF($bookingData, 'ordrebekreftelse');
            $pdfService->generateBookingPDF($bookingData, 'kvittering');
        }
    }

    public function processBooking($postData) {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'User not logged in'];
        }

        $bookingData = [
            'room_id' => filter_var($postData['room_id'], FILTER_VALIDATE_INT),
            'user_id' => $_SESSION['user_id'],
            'check_in_date' => filter_var($postData['check_in'], FILTER_SANITIZE_STRING),
            'check_out_date' => filter_var($postData['check_out'], FILTER_SANITIZE_STRING),
            'number_of_adults' => filter_var($postData['adults'], FILTER_VALIDATE_INT),
            'number_of_children' => filter_var($postData['children'], FILTER_VALIDATE_INT),
            'total_price' => $this->calculateTotalPrice($postData['room_id'], $postData['check_in'], $postData['check_out'])
        ];

        $bookingId = $this->roomBookingModel->createBooking($bookingData);
        
        if ($bookingId) {
            $this->generatePDF($bookingId);
            return ['success' => true, 'booking_id' => $bookingId];
        }

        return ['success' => false, 'error' => 'Booking failed'];
    }

    private function calculateTotalPrice($roomId, $checkIn, $checkOut) {
        $stmt = $this->db->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        
        $nights = $this->calculateNights($checkIn, $checkOut);
        return $room['price_per_night'] * $nights;
    }

    private function calculateNights($checkIn, $checkOut) {
        return ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24));
    }
}
?>
