<?php
class BookingController {
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
}
?>
