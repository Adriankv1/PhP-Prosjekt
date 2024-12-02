<?php
// Start the session to manage user authentication
session_start();

// Include necessary files for database connection and models
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/LoyaltyModel.php';

// Check if the room ID and user ID are set, if not redirect to the room booking page
if (!isset($_POST['room_id']) || !isset($_SESSION['user_id'])) {
    header('Location: rombooking.php');
    exit;
}

// Instantiate the LoyaltyModel with the database connection
$loyaltyModel = new LoyaltyModel($db);
// Get the loyalty information for the logged-in user
$loyaltyInfo = $loyaltyModel->getLoyaltyInfo($_SESSION['user_id']);

// Prepare and execute the query to get room details
$room_query = "SELECT * FROM rooms WHERE id = ?";
$stmt = $db->prepare($room_query);
$stmt->bind_param("i", $_POST['room_id']);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

// Calculate the number of nights and total price for the booking
$nights = ceil((strtotime($_POST['check_out']) - strtotime($_POST['check_in'])) / (60 * 60 * 24));
$total_price = $room['price_per_night'] * $nights;
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bekreft Bestilling</title>
    <link rel="stylesheet" href="../../public/css/styleGlobal.css">
    <style>
        .booking-confirm {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .price-breakdown {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .points-section {
            margin: 20px 0;
            padding: 15px;
            background: #e9ecef;
            border-radius: 4px;
        }
        .final-price {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include '../partials/navbar.php'; ?>
    
    <div class="booking-confirm">
        <h2>Bekreft din bestilling</h2>
        
        <div class="room-details">
            <h3>Rom detaljer</h3>
            <p>Romnummer: <?php echo htmlspecialchars($room['room_number']); ?></p>
            <p>Type: <?php echo htmlspecialchars(ucfirst($room['room_type'])); ?></p>
            <p>Innsjekking: <?php echo htmlspecialchars($_POST['check_in']); ?></p>
            <p>Utsjekking: <?php echo htmlspecialchars($_POST['check_out']); ?></p>
            <p>Antall netter: <?php echo $nights; ?></p>
        </div>

        <div class="price-breakdown">
            <h3>Prisdetaljer</h3>
            <p>Pris per natt: <?php echo number_format($room['price_per_night'], 2); ?> NOK</p>
            <p>Total pris: <?php echo number_format($total_price, 2); ?> NOK</p>
        </div>

        <?php if ($loyaltyInfo['spendable_points'] > 0): ?>
        <div class="points-section">
            <h3>Bruk Lojalitetspoeng</h3>
            <p>Tilgjengelige poeng: <?php echo number_format($loyaltyInfo['spendable_points']); ?></p>
            <p>1 poeng = 1 NOK</p>
        </div>
        <?php endif; ?>

        <!-- Booking confirmation form -->
        <form method="POST" action="booking.php">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($_POST['room_id']); ?>">
            <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($_POST['check_in']); ?>">
            <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($_POST['check_out']); ?>">
            <input type="hidden" name="adults" value="<?php echo htmlspecialchars($_POST['adults']); ?>">
            <input type="hidden" name="children" value="<?php echo htmlspecialchars($_POST['children']); ?>">
            
            <?php if ($loyaltyInfo['spendable_points'] > 0): ?>
            <div class="points-input">
                <label>Poeng å bruke (maks <?php echo min($loyaltyInfo['spendable_points'], $total_price); ?>):</label>
                <input type="number" name="points_to_use" 
                       min="0" 
                       max="<?php echo min($loyaltyInfo['spendable_points'], $total_price); ?>" 
                       value="0"
                       onchange="updateFinalPrice(this.value)">
            </div>
            <?php endif; ?>

            <div class="final-price">
                Endelig pris: <span id="final-price"><?php echo number_format($total_price, 2); ?></span> NOK
            </div>

            <button type="submit" name="book" class="book-btn">Bekreft Bestilling</button>
        </form>
    </div>

    <script>
        // Update the final price based on the points used
        function updateFinalPrice(points) {
            const totalPrice = <?php echo $total_price; ?>;
            const finalPrice = Math.max(0, totalPrice - points);
            document.getElementById('final-price').textContent = finalPrice.toFixed(2);
        }
    </script>
</body>
</html>