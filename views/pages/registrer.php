<!DOCTYPE html>
<?php
//databasekobling

// include('./../../config\server.php'); 
include './../partials/navbar.php'; 

?>

<!--Registreringssiden -->
<!DOCTYPE html>
<html>
    <head>
        <title> Registrering PHP og (databasenavn)</title>
            <link rel="stylesheet" type="text/css" href="./../../public/css/styleGlobal.css">
            <link rel="stylesheet" type="text/css" href="./../../public/css/styleHomePage.css">

</head>
<body>

<div class="header">
    <h2>Registrer</h2>
</div>

<!--Registreringsskjema ny bruker -->

<form method="post" action="registrer.php">
    <?php include('errors.php'); ?>
    <div class="input-group">
        <label>Brukernavn</label>
        <input type="text" name="brukernavn" value="<?php echo  $username; ?>">
</div>
<div>E-post</label>
<input type="E-post" name="E-post" value="<?php echo $Epost; ?>">
</div>

</form>
<footer><?php echo "Footer" ?></footer>
</body>
</html>
