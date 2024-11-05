<!DOCTYPE html>
<?php 
// imports navbar
include 'views\partials\navbar.php'; 

// json import
function loadJson($filename) {
    $jsonString = file_get_contents($filename);
    return json_decode($jsonString, true);
}
// decides language for page
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'nb';
// condition for language
$data = loadJson($lang === 'nb' ? './config/nb.json' : './config/en.json');

// imported
$home = $data['homePage']['home'];
$titlePage = $data['homePage']['titlePage'];
$stamp = $data['footer']['stamp'];
$welcomeTitle = $data['welcomeBox']['welcomeTitle'];
$welcomeText = $data['welcomeBox']['welcomeText'];
$viewDeals = $data['welcomeBox']['viewDeals'];
$search = $data['welcomeBox']['search'];
?>

<!-- changes language -->
<html lang="<?php echo htmlspecialchars($lang); ?>"> 
<head>
    <!-- imports html -->
    <link rel="icon" type="image/png" href="./public/images/favicon.png">
    <link rel="stylesheet" type="text/css" href="./public/css/styleGlobal.css">
    <link rel="stylesheet" type="text/css" href="./public/css/styleHomePage.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title><?php echo htmlspecialchars($titlePage); ?></title>
</head>
<body>

<main>
    <!-- welcome box -->
    <div class="welcomeBox">
        <div class="insideWelcomeBox">
            <?php echo "<h1>$welcomeTitle</h1>"; 
                  echo "<p>$welcomeText</p>"; ?>
            <div class="dealSearch"> 
                <div class="viewDetails">
                    <?php echo $viewDeals;?>
                </div>
                <div class="search">
                    <?php echo $search;?>
                </div>
            </div>
        </div>
    </div>

    <!-- explore rooms -->
    <div class="exploreRooms">
        <h2>Explore Our Rooms</h2>
        <div class="roomContainer"> 
            <div class="room">
                <img src="./public/images/deluxeroom.jpg" alt="room1" width="500" height="300">
                <p>Deluxe Room</p>
                <h3>Room 1</h3>
            </div>
            <div class="room">
                <img src="./public/images/familyroom.jpg" alt="room1" width="500" height="300">
                <p>Family Suite</p>
                <h3>Room 2</h3>
            </div>
            <div class="room">
                <img src="./public/images/standardroom.jpg" alt="room1" width="500" height="300">
                <p>Standard Room</p>
                <h3>Room 3</h3>
            </div>
            <div class="room">
                <img src="./public/images/cheaproom.jpg" alt="room1" width="500" height="300">
                <p>Cheap Room</p>
                <h3>Room 4</h3>
            </div>
        </div>
    </div>
</main>

<!-- footer -->
<footer>
    <p><?php echo $stamp; ?></p>
</footer>

</body>
</html>
