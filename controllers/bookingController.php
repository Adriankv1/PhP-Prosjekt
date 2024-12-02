<?php
class BookingController {
    private $db;
    private $bookingModel;

    public function __construct($db) {
        $this->db = $db;
        $this->bookingModel = new BookingModel();
    }

    public function generatePDF($bookingId) {
        $bookingData = $this->bookingModel->getBookingDetails($bookingId);
        if ($bookingData) {
            $pdfService = new PDFService();
            $pdfService->generateBookingPDF($bookingData, 'ordrebekreftelse');
            $pdfService->generateBookingPDF($bookingData, 'kvittering');
        }
    }

    public function processBooking($postData) {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'User not logged in'];
        }

        $bookingId = rand(1000, 9999); // Temporary ID generation
        $this->generatePDF($bookingId);
        return ['success' => true, 'booking_id' => $bookingId];
    }
}
?>
