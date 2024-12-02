<?php
session_start();
require_once __DIR__ . '/../../config/server.php';
require_once __DIR__ . '/../../models/RoomModel.php';
require_once __DIR__ . '/../../controllers/RoomController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$controller = new RoomController($db);
$user_preferences = $controller->getUserPreferences($user_id);
$preferred_room_type = $user_preferences['preference_value'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $results = $controller->searchRooms();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room and Guests Selector with Date</title>
    <link rel="stylesheet" type="text/css" href="../../public/css/styleGlobal.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/styleRombooking.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="room-guest-selector">
        <form method="POST" action="">
            <h4>Room 1</h4>
            
            <!-- Calendar -->
            <div class="date-options">
                <div class="date-option">
                    <label for="start-date">Start Dato</label>
                    <input type="date" id="start-date" name="start-date" 
                           value="<?php echo isset($_POST['start-date']) ? htmlspecialchars($_POST['start-date']) : ''; ?>" required>
                </div>
                <div class="date-option">
                    <label for="end-date">Slutt Dato</label>
                    <input type="date" id="end-date" name="end-date" 
                           value="<?php echo isset($_POST['end-date']) ? htmlspecialchars($_POST['end-date']) : ''; ?>" required>
                </div>
            </div>
                
            <!-- Adult selector -->
            <div class="guest-options">

                <div class="guest-option">
                    <label>Voksne (Alder 13+)</label>
                    <div class="guest-counter">
                        <select name="adults" required>
                            <?php for($i = 1; $i <= 4; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($_POST['adults']) && $_POST['adults'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Children selector -->
                <div class="guest-option">
                    <label>Barn (Alder 0-12)</label>
                    <div class="guest-counter">
                        <select name="children" required>
                            <?php for($i = 0; $i <= 4; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($_POST['children']) && $_POST['children'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

    <div class="guest-selector">
        <div class="guest-option">
            <label>Voksne (Alder 13+)</label>
            <div class="guest-counter">
                <select name="adults" required>
                    <?php for($i = 1; $i <= 4; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php echo (isset($_POST['adults']) && $_POST['adults'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>

            </div>
        </div>
        
        <div class="guest-option">
            <label>Barn (Alder 0-12)</label>
            <div class="guest-counter">
                <select name="children" required>
                    <?php for($i = 0; $i <= 3; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php echo (isset($_POST['children']) && $_POST['children'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="room-option">
            <label>Etasje</label>
            <select name="floor">
                <option value="">Velg etasje</option>
                <option value="1">1. etasje</option>
                <option value="2">2. etasje</option>
                <option value="3">3. etasje</option>
                <option value="4">4. etasje</option>
            </select>
        </div>


            <!-- Room Type selector -->
            <div class="input-group">
                <label>Room Type</label>
                <select name="room_type">
                    <option value="deluxe" <?php echo $preferred_room_type == 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                    <option value="family" <?php echo $preferred_room_type == 'family' ? 'selected' : ''; ?>>Family</option>
                    <option value="standard" <?php echo $preferred_room_type == 'standard' ? 'selected' : ''; ?>>Standard</option>
                    <option value="cheap" <?php echo $preferred_room_type == 'cheap' ? 'selected' : ''; ?>>Cheap</option>
                </select>
            </div>


        <div class="room-option">
            <label>Romtype</label>
            <select name="room_type">
                <option value="">Velg romtype</option>
                <option value="deluxe">Deluxe</option>
                <option value="family">Familie</option>
                <option value="standard">Standard</option>
                <option value="cheap">Budsjett</option>
            </select>
        </div>
    </div>
</div>

            <button type="submit" name="search" class="search-btn">Search</button>
        </form>
<!-- 
        <style>
            .room-preferences {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
            }

            .room-option {
                flex: 1;
            }

            .room-option select {
            padding: 8px 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 140px;
            }

        </style> -->
        <!-- Search Results Section -->
        <?php if (isset($results)): ?>
            <div class="search-results">
                <?php if (isset($results['error'])): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($results['error']); ?>
                    </div>
                <?php elseif (empty($results['rooms'])): ?>
                    <div class="no-results">
                        <p>No rooms available for the selected criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="rooms-grid">
                        <?php foreach ($results['rooms'] as $room): ?>
                            <div class="room-card">
                                <h3>Room <?php echo htmlspecialchars($room['room_number']); ?> - 
                                    <?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <p><?php echo htmlspecialchars($room['description']); ?></p>
                                <p>Price per night: <?php echo htmlspecialchars($room['price_per_night']); ?> NOK</p>
                                <form method="POST" action="booking.php">
                                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                    <input type="hidden" name="check_in" value="<?php echo $_POST['start-date']; ?>">
                                    <input type="hidden" name="check_out" value="<?php echo $_POST['end-date']; ?>">
                                    <input type="hidden" name="adults" value="<?php echo $_POST['adults']; ?>">
                                    <input type="hidden" name="children" value="<?php echo $_POST['children']; ?>">
                                    <button type="submit" name="book" class="book-btn">Book Now</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>