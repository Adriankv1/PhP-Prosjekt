<?php
//databasekobling
include('server.php') ?>

<!--Registreringssiden -->
<!DOCTYPE html>
<html>
    <head>
        <title> Registrering PHP og (databasenavn)</title>
        <link rel="stylesheet" type="text/css" href="../local/stylesheets/styleRegistrering.css">
</head>
<body>
<nav class="navbar">
    <ul>
        <li><a href="..\index.php">Home</a></li>
        <li><a href="pages/rombooking.php"><strong>Book et rom her</strong></a></li>
        <li><a href="pages/login.php">Login/Registrer</a><li>
    </ul>
</nav>
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

