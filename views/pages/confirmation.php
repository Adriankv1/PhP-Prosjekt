<?php
// Start the session to manage user authentication
session_start();

// Include necessary files for database connection and models
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/bookingModel.php';

// Check if the booking ID is set in the URL, if not redirect to the index page
if (!isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
}

// Instantiate the BookingModel with the database connection
$bookingModel = new BookingModel($db);
// Get the booking details using the booking ID from the URL
$bookingDetails = $bookingModel->getBookingDetails($_GET['booking_id']);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestilling Bekreftet</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
    <link rel="stylesheet" href="../../public/css/styleConfirmation.css">
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include '../partials/navbar.php'; ?>
    
    <div class="confirmation-container">
        <h2>Bestilling Bekreftet!</h2>
        <div class="booking-details">
            <p><strong>Bestillingsnummer:</strong> <?php echo htmlspecialchars($bookingDetails['bookingNumber']); ?></p>
            <p><strong>Gjestens navn:</strong> <?php echo htmlspecialchars($bookingDetails['customerName']); ?></p>
            <p><strong>E-post:</strong> <?php echo htmlspecialchars($bookingDetails['email']); ?></p>
            <p><strong>Romtype:</strong> <?php echo htmlspecialchars($bookingDetails['roomType']); ?></p>
            <p><strong>Innsjekkingsdato:</strong> <?php echo htmlspecialchars($bookingDetails['checkInDate']); ?></p>
            <p><strong>Utsjekkingsdato:</strong> <?php echo htmlspecialchars($bookingDetails['checkOutDate']); ?></p>
            <p><strong>Antall netter:</strong> <?php echo htmlspecialchars($bookingDetails['nights']); ?></p>
            <p><strong>Pris per natt:</strong> <?php echo htmlspecialchars($bookingDetails['pricePerNight']); ?> NOK</p>
            <p><strong>Total pris:</strong> <?php echo htmlspecialchars($bookingDetails['totalPrice']); ?> NOK</p>
            <p><strong>MVA:</strong> <?php echo htmlspecialchars($bookingDetails['mva']); ?> NOK</p>
        </div>
        
        <div class="action-buttons">
            <!-- Link to download the receipt PDF -->
            <a href="../../uploads/ordrebekreftelse_<?php echo htmlspecialchars($bookingDetails['bookingNumber']); ?>.pdf" 
               class="download-btn">Last ned kvittering</a>
            <!-- Link to go back to the homepage -->
            <a href="../../index.php" class="back-btn">Tilbake til forsiden</a>
        </div>
    </div>
</body>
</html>