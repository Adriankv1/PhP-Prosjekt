<?php
class BookingController {
    private $db;
    private $bookingModel;

    // Constructor to initialize database connection and booking model
    public function __construct($db) {
        $this->db = $db;
        $this->bookingModel = new BookingModel($db);
    }

    // Method to process a booking
    public function processBooking($postData) {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'User not logged in'];
        }

        // Include the LoyaltyModel for handling loyalty points
        require_once __DIR__ . '/../models/LoyaltyModel.php';
        $loyaltyModel = new LoyaltyModel($this->db);

        // Calculate the original price of the booking
        $original_price = $this->calculateTotalPrice($postData['room_id'], $postData['check_in'], $postData['check_out']);
        // Determine the points to use for discount, if any
        $points_to_use = isset($postData['points_to_use']) ? min((int)$postData['points_to_use'], $original_price) : 0;

        // Check if the user has enough points and apply discount if applicable
        if ($points_to_use > 0) {
            if (!$loyaltyModel->hasEnoughPoints($_SESSION['user_id'], $points_to_use)) {
                return ['success' => false, 'error' => 'Insufficient points'];
            }
            $discounted_price = $original_price - $points_to_use;
        } else {
            $discounted_price = $original_price;
        }

        // Prepare booking data
        $bookingData = [
            'room_id' => filter_var($postData['room_id'], FILTER_VALIDATE_INT),
            'user_id' => $_SESSION['user_id'],
            'check_in_date' => filter_var($postData['check_in'], FILTER_SANITIZE_STRING),
            'check_out_date' => filter_var($postData['check_out'], FILTER_SANITIZE_STRING),
            'number_of_adults' => filter_var($postData['adults'], FILTER_VALIDATE_INT),
            'number_of_children' => filter_var($postData['children'], FILTER_VALIDATE_INT),
            'total_price' => $discounted_price,
            'points_used' => $points_to_use
        ];

        // Begin transaction
        $this->db->begin_transaction();

        try {
            // Insert booking data into the database
            $stmt = $this->db->prepare("
                INSERT INTO bookings (room_id, user_id, check_in_date, check_out_date, 
                                    number_of_adults, number_of_children, total_price, points_used)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("iissiidi", 
                $bookingData['room_id'],
                $bookingData['user_id'],
                $bookingData['check_in_date'],
                $bookingData['check_out_date'],
                $bookingData['number_of_adults'],
                $bookingData['number_of_children'],
                $bookingData['total_price'],
                $bookingData['points_used']
            );

            // Execute the statement and check if the booking was successful
            if ($stmt->execute()) {
                $bookingId = $stmt->insert_id;

                // Spend loyalty points if used
                if ($points_to_use > 0) {
                    $loyaltyModel->spendPoints($_SESSION['user_id'], $points_to_use);
                }

                // Add loyalty points for the booking
                $loyaltyModel->addPoints($_SESSION['user_id'], $bookingId, $discounted_price);

                // Update room status to 'occupied'
                $updateStmt = $this->db->prepare("UPDATE rooms SET status = 'occupied' WHERE id = ?");
                $updateStmt->bind_param("i", $bookingData['room_id']);
                $updateStmt->execute();

                // Commit the transaction
                $this->db->commit();
                // Generate booking PDF
                $this->generatePDF($bookingId);

                return ['success' => true, 'booking_id' => $bookingId];
            }

            // Rollback the transaction if booking failed
            $this->db->rollback();
            return ['success' => false, 'error' => 'Booking failed'];

        } catch (Exception $e) {
            // Rollback the transaction in case of an exception
            $this->db->rollback();
            return ['success' => false, 'error' => 'Booking failed: ' . $e->getMessage()];
        }
    }
    
    // Method to generate a PDF for a booking
    public function generatePDF($bookingId) {
        // Get booking details from the model
        $bookingData = $this->bookingModel->getBookingDetails($bookingId);
        if ($bookingData) {
            // Include the PDF service and generate the PDF
            require_once __DIR__ . '/../services/PDFService.php';
            $pdfService = new PDFService();
            $pdfService->generateBookingPDF($bookingData, 'ordrebekreftelse');
        }
    }

    // Calculate the total price for the booking based on room price per night and number of nights
    private function calculateTotalPrice($roomId, $checkIn, $checkOut) {
        // Prepare and execute SQL statement to get the price per night for the room
        $stmt = $this->db->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        
        // Calculate the number of nights and return the total price
        $nights = $this->calculateNights($checkIn, $checkOut);
        return $room['price_per_night'] * $nights;
    }

    // Calculate the number of nights between check-in and check-out dates
    private function calculateNights($checkIn, $checkOut) {
        return ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24));
    }
}
?>