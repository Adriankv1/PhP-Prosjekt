<?php
session_start();
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/bookingModel.php';
require_once __DIR__ . '/../../services/PDFService.php';
require_once __DIR__ . '/../../controllers/bookingController.php';

// Initialize booking process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $bookingController = new BookingController($db);
    $result = $bookingController->processBooking($_POST);
    
    if ($result['success']) {
        header('Location: confirmation.php?booking_id=' . $result['booking_id']);
        exit;
    }
}
?>

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