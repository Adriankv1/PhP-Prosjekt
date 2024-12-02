<!DOCTYPE html> 
<?php
// Start the session to manage user authentication
include('../../controllers/registrerController.php'); 
include('../../middlewares/errors.php'); 
include './../partials/navbar.php'; 
?>

<!--Registration page-->
<html>
    <head>
        <title> Registrering PHP og (databasenavn)</title>
        <!-- Link to the CSS stylesheets -->
        <link rel="stylesheet" type="text/css" href="./../../public/css/styleRegistrering.css">
        <link rel="stylesheet" type="text/css" href="./../../public/css/styleGlobal.css">
        <link rel="stylesheet" type="text/css" href="./../../public/css/styleHomePage.css">
    </head>
    <body>
        <div class="header">
            <h2>Registrer</h2>
        </div>

        <!--Register new user -->
        <div class="form-wrapper">
            <form method="post" action="registrer.php">
                 <!-- Include error handling -->
                <?php include('../../middlewares/errors.php'); ?>
                <div class="input-group">
                    <label>Brukernavn</label>
                    <input type="text" name="brukernavn" value="<?php echo  $username; ?>">
                </div>
                <div>E-post</label>
                    <input type="email" name="email" value="<?php echo $email; ?>">
                </div>
                <div class="input-group">
                    <label>Passord</label>
                    <input type="password" name="password_1">
                </div>
                <div class="input-group">
                    <label>Bekreft passord</label>
                    <input type="password" name="password_2">
                </div>
                <div class="input-group">
                    <button type="submit" class="btn" name="reg_user">Registrer</button>
                </div>
                <div class="input-group">
                    <!-- New button to create admin and user accounts -->
                    <button type="submit" class="btn" name="create_default_users">Opprett standardbrukere</button>
                </div>
            </form>
        </div>
        <footer>
            <p><?php echo htmlspecialchars($stamp); ?></p>
        </footer>
    </body>
</html>
