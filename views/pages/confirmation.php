<?php

session_start();
require_once __DIR__ . '/../../config/server.php';

if (!isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
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
        <p>Your booking reference: <?php echo htmlspecialchars($_GET['booking_id']); ?></p>
        <p>A confirmation email has been sent to your registered email address.</p>
        <a href="../../uploads/ordrebekreftelse_<?php echo htmlspecialchars($_GET['booking_id']); ?>.pdf" 
           class="download-btn">Download Receipt</a>
        <br><br>
        <a href="../../index.php" class="back-btn">Return to Home</a>
    </div>

</body>
</html>