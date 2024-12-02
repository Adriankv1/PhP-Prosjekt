<?php

session_start();
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/bookingModel.php';

if (!isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
}

$bookingModel = new BookingModel($db);
$bookingDetails = $bookingModel->getBookingDetails($_GET['booking_id']);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestilling Bekreftet</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .booking-details {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .booking-details p {
            margin: 10px 0;
            line-height: 1.6;
        }
        .download-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
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
            <a href="../../uploads/ordrebekreftelse_<?php echo htmlspecialchars($bookingDetails['bookingNumber']); ?>.pdf" 
               class="download-btn">Last ned kvittering</a>
            <a href="index.php" class="back-btn">Tilbake til forsiden</a>
        </div>
    </div>
</body>
</html>