<?php
// booking.php - Handles the booking process after room selection
session_start();
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/bookingModel.php';
require_once __DIR__ . '/../../services/PDFService.php';

class BookingController {
    private $db;
    private $bookingModel;
    private $pdfService;

    public function __construct($db) {
        $this->db = $db;
        $this->bookingModel = new BookingModel($db);
        $this->pdfService = new PDFService();
    }

    public function processBooking() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        // Validate booking data
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
        $checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
        $adults = filter_input(INPUT_POST, 'adults', FILTER_VALIDATE_INT);
        $children = filter_input(INPUT_POST, 'children', FILTER_VALIDATE_INT);

        // Calculate total price
        $totalPrice = $this->calculateTotalPrice($roomId, $checkIn, $checkOut);
        
        // Create booking
        $bookingData = [
            'room_id' => $roomId,
            'user_id' => $_SESSION['user_id'],
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'number_of_adults' => $adults,
            'number_of_children' => $children,
            'total_price' => $totalPrice
        ];

        $bookingId = $this->roomBookingModel->createBooking($bookingData);
        
        if ($bookingId) {
            // Generate PDF receipt
            $receiptData = array_merge($bookingData, [
                'bookingNumber' => $bookingId,
                'reservationDate' => date('d-m-Y'),
                'customerName' => $_SESSION['username'],
                'phone' => $_SESSION['phone'],
                'email' => $_SESSION['email'],
                'roomType' => $this->getRoomType($roomId),
                'guestCount' => $adults + $children,
                'pricePerNight' => $totalPrice / $this->calculateNights($checkIn, $checkOut),
                'nights' => $this->calculateNights($checkIn, $checkOut),
                'mva' => $totalPrice * 0.25,
            ]);

            $this->pdfService->generateBookingPDF($receiptData, 'ordrebekreftelse');
            
            return [
                'success' => true,
                'booking_id' => $bookingId,
                'receipt_url' => '../uploads/ordrebekreftelse_' . $bookingId . '.pdf'
            ];
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
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        return $checkOutDate->diff($checkInDate)->days;
    }

    private function getRoomType($roomId) {
        $stmt = $this->db->prepare("SELECT room_type FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        return $room['room_type'];
    }
}

// Initialize booking process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $bookingController = new BookingController($db);
    $result = $bookingController->processBooking();
    
    if ($result['success']) {
        header('Location: confirmation.php?booking_id=' . $result['booking_id']);
        exit;
    }
}
?>

<!-- confirmation.php - Booking confirmation page -->
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
</head>
<body>
    <?php include '../partials/navbar.php'; ?>
    
    <div class="confirmation-container">
        <h2>Booking Confirmed!</h2>
        <?php if (isset($_GET['booking_id'])): ?>
            <p>Your booking reference: <?php echo htmlspecialchars($_GET['booking_id']); ?></p>
            <p>A confirmation email has been sent to your registered email address.</p>
            <a href="../uploads/ordrebekreftelse_<?php echo htmlspecialchars($_GET['booking_id']); ?>.pdf" 
               class="download-btn">Download Receipt</a>
        <?php endif; ?>
    </div>
</body>
</html>