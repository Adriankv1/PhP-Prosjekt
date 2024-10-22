<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room and Guests Selector with Date</title>
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleGlobal.css">
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleHomePage.css">
    

</head>
<body>
<?php include './../partials/navbar.php';  ?>
<div class="room-guest-selector">
    <h4>Room 1</h4>
    
    <!-- Kalender -->
    <div class="date-options">
        <div class="date-option">
            <label for="start-date">Start Dato</label>
            <input type="date" id="start-date" name="start-date">
        </div>
        <div class="date-option">
            <label for="end-date">Slutt Dato</label>
            <input type="date" id="end-date" name="end-date">
        </div>
    </div>

            <!-- Voksne Valg -->
    <div class="guest-options">
        <div class="guest-option">
            <label>Voksne (Alder 13+)</label>
            <div class="guest-counter">
                <button type="button" class="counter-btn" onclick="changeValue('adults', -1)">-</button>
                <span id="adults" class="counter-value">1</span>
                <button type="button" class="counter-btn" onclick="changeValue('adults', 1)">+</button>
            </div>
        </div>
        
        <!-- Barn Valg -->
        <div class="guest-option">
            <label>Barn (Alder 0-12)</label>
            <div class="guest-counter">
                <button type="button" class="counter-btn" onclick="changeValue('children', -1)">-</button>
                <span id="children" class="counter-value">0</span>
                <button type="button" class="counter-btn" onclick="changeValue('children', 1)">+</button>
            </div>
        </div>
    </div>

    <button class="search-btn" onclick="submitForm()">Search</button>
</div>
<footer><?php echo "footer" ?></footer>
</body>
</html>
