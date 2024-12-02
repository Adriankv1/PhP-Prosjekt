<?php
class BookingController {
    private $db;
    private $bookingModel;

    public function __construct($db) {
        $this->db = $db;
        $this->bookingModel = new BookingModel($db);
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

        // Start transaction to ensure data consistency
        $this->db->begin_transaction();

        try {
            // Insert booking data into bookings table
            $stmt = $this->db->prepare("
                INSERT INTO bookings (room_id, user_id, check_in_date, check_out_date, 
                                      number_of_adults, number_of_children, total_price)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("iissiii", 
                $bookingData['room_id'],
                $bookingData['user_id'],
                $bookingData['check_in_date'],
                $bookingData['check_out_date'],
                $bookingData['number_of_adults'],
                $bookingData['number_of_children'],
                $bookingData['total_price']
            );

            if ($stmt->execute()) {
                $bookingId = $stmt->insert_id;

                // Update room status to 'occupied'
                $updateStmt = $this->db->prepare("UPDATE rooms SET status = 'occupied' WHERE id = ?");
                $updateStmt->bind_param("i", $bookingData['room_id']);
                $updateStmt->execute();

                // Commit the transaction
                $this->db->commit();

                // Generate PDF for the booking
                $this->generatePDF($bookingId);

                return ['success' => true, 'booking_id' => $bookingId];
            }

            // Rollback if anything fails
            $this->db->rollback();
            return ['success' => false, 'error' => 'Booking failed'];

        } catch (Exception $e) {
            // Rollback transaction in case of error
            $this->db->rollback();
            return ['success' => false, 'error' => 'Booking failed: ' . $e->getMessage()];
        }
    }

    public function generatePDF($bookingId) {
        $bookingData = $this->bookingModel->getBookingDetails($bookingId);
        if ($bookingData) {
            require_once __DIR__ . '/../services/PDFService.php';
            $pdfService = new PDFService();
            $pdfService->generateBookingPDF($bookingData, 'ordrebekreftelse');
        }
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
