<!DOCTYPE html>
<?php 
// imports navbar
include 'views/partials/navbar.php'; 
include 'views/partials/user_info.php'; 

// json import
function loadJson($filename) {
    $jsonString = file_get_contents($filename);
    return json_decode($jsonString, true);
}

// decides language for page
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'nb'; //for testing change "en" to "nb"
$data = loadJson($lang === 'nb' ? './config/nb.json' : './config/en.json');

// imported json data
$home = $data['homePage']['home'];
$titlePage = $data['homePage']['titlePage'];
$stamp = $data['footer']['stamp'];
$welcomeTitle = $data['welcomeBox']['welcomeTitle'];
$welcomeText = $data['welcomeBox']['welcomeText'];
$viewDeals = $data['welcomeBox']['viewDeals'];
$search = $data['welcomeBox']['search'];

$exploreRoomsTitle = $data['exploreRooms']['title'];
$exploreRoomsText = $data['exploreRooms']['text'];
$rooms = $data['exploreRooms']['rooms'];

$discoverMoreTitle = $data['discoverMore']['title'];
$discoverMoreText = $data['discoverMore']['text'];
$learnMoreBtn = $data['discoverMore']['learnMoreBtn'];

$localAttractionsTitle = $data['localAttractions']['title'];
$localAttractionsText = $data['localAttractions']['text'];
$localGuide = $data['localAttractions']['localGuide'];

$hotelAmenitiesTitle = $data['hotelAmenities']['title'];
$hotelAmenitiesText = $data['hotelAmenities']['text'];

$guestReviewsTitle = $data['guestReviews']['title'];
$guestReviewsText = $data['guestReviews']['text'];

$excellentService = $data['review']['excellentService'];
$greatLocation = $data['review']['greatLocation'];
$lovelyStay = $data['review']['lovelyStay'];
?>

<html lang="<?php echo htmlspecialchars($lang); ?>"> 
<head>
    <!-- imports html -->
    <link rel="icon" type="image/png" href="./public/images/favicon.png">
    <link rel="stylesheet" type="text/css" href="./public/css/styleHomePage.css">
    <link rel="stylesheet" type="text/css" href="./public/css/styleGlobal.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title><?php echo htmlspecialchars($titlePage . ' - ' . $stamp); ?></title>
</head>
<body>

<main>
    <!-- welcome box -->
    <div class="welcomeBox">
        <div class="insideWelcomeBox">
            <h1><?php echo htmlspecialchars($welcomeTitle); ?></h1>
            <p><?php echo htmlspecialchars($welcomeText); ?></p>
            <div class="dealSearch"> 
    <div class="viewDetails"><?php echo htmlspecialchars($viewDeals); ?></div>
        <a href="views/pages/rombooking.php" class="search">
        <?php echo htmlspecialchars($search); ?>
        </a>
    </div>      
  </div>
    </div>

    <!-- explore rooms -->
    <div class="exploreRooms">
        <h2><?php echo htmlspecialchars($exploreRoomsTitle); ?></h2>
        <p><?php echo htmlspecialchars($exploreRoomsText); ?></p>
        <div class="roomContainer"> 
            <div class="room">
                <img src="./public/images/deluxeroom.jpg" alt="Deluxe Room">
                <p><?php echo htmlspecialchars($rooms[0]['name']); ?></p>
                <h3><?php echo htmlspecialchars($rooms[0]['price']); ?></h3>
            </div>
            <div class="room">
                <img src="./public/images/familyroom.jpg" alt="Family Suite">
                <p><?php echo htmlspecialchars($rooms[1]['name']); ?></p>
                <h3><?php echo htmlspecialchars($rooms[1]['price']); ?></h3>
            </div>
            <div class="room">
                <img src="./public/images/standardroom.jpg" alt="Standard Room">
                <p><?php echo htmlspecialchars($rooms[2]['name']); ?></p>
                <h3><?php echo htmlspecialchars($rooms[2]['price']); ?></h3>
            </div>
            <div class="room">
                <img src="./public/images/cheaproom.jpg" alt="Cheap Room">
                <p><?php echo htmlspecialchars($rooms[3]['name']); ?></p>
                <h3><?php echo htmlspecialchars($rooms[3]['price']); ?></h3>
            </div>
        </div>
    </div>

    <!-- discover -->
    <div class="discoverMore">
        <div class="discoverHeader">
            <div class="discoverIcon"><img src="./public/images/discovermore.jpg" alt="Discover Icon"></div>
            <h2><?php echo htmlspecialchars($discoverMoreTitle); ?></h2>
            <p><?php echo htmlspecialchars($discoverMoreText); ?></p>
            <button class="learnMoreBtn"><?php echo htmlspecialchars($learnMoreBtn); ?></button>
        </div>

        <div class="discoverCards">
            <div class="card">
                <div class="cardIcon"><img src="./public/images/museum.png"></div>
                <h3><?php echo htmlspecialchars($localAttractionsTitle); ?></h3>
                <p><?php echo htmlspecialchars($localAttractionsText); ?></p>
                <a href="https://www.visitoslo.com/en/activities-and-attractions/attractions/" class="localGuide" target="_blank"><?php echo htmlspecialchars($localGuide); ?></a>
            </div>
            <div class="card">
                <div class="cardIcon"><img src="./public/images/pool.jpg"></div>
                <h3><?php echo htmlspecialchars($hotelAmenitiesTitle); ?></h3>
                <p><?php echo htmlspecialchars($hotelAmenitiesText); ?></p>
            </div>
        </div>
    </div>

    <!-- reviews -->
    <div class="guestReviews">
        <div class="reviewsHeader">
            <h2><?php echo htmlspecialchars($guestReviewsTitle); ?></h2>
            <p><?php echo htmlspecialchars($guestReviewsText); ?></p>
        </div>

        <div class="reviewsContainer">
            <div class="reviewCard">
                <div class="reviewHeader">
                    <div class="profileImage"><img src="./public/images/review2.jpg"></div>
                    <span class="reviewerName">Sarah M.</span>
                    <span class="reviewRating">★★★★★</span>
                </div>
                <p class="reviewText"><?php echo htmlspecialchars($excellentService); ?></p>
            </div>

            <div class="reviewCard">
                <div class="reviewHeader">
                    <div class="profileImage"><img src="./public/images/review1.jpg"></div>
                    <span class="reviewerName">Michael P.</span>
                    <span class="reviewRating">★★★★★</span>
                </div>
                <p class="reviewText"><?php echo htmlspecialchars($greatLocation); ?></p>
            </div>

            <div class="reviewCard">
                <div class="reviewHeader">
                    <div class="profileImage"><img src="./public/images/review3.jpg"></div>
                    <span class="reviewerName">Emily S.</span>
                    <span class="reviewRating">★★★★★</span>
                </div>
                <p class="reviewText"><?php echo htmlspecialchars($lovelyStay); ?></p>
            </div>
        </div>
    </div>

</main>

<!-- footer -->
<footer>
    <p><?php echo htmlspecialchars($stamp); ?></p>
</footer>

</body>
</html>
