<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'registration');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
        $password = md5($password_1);//encrypt the password before saving in the database

        $query = "INSERT INTO users (username, email, password) 
                          VALUES('$username', '$email', '$password')";
        mysqli_query($db, $query);
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        header('location: index.php');
  }
}


// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($username)) {
          array_push($errors, "Username is required");
    }
    if (empty($password)) {
          array_push($errors, "Password is required");
    }
  
    if (count($errors) == 0) {
          $password = md5($password);
          $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
          $results = mysqli_query($db, $query);
          if (mysqli_num_rows($results) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: index.php');
          }else {
                  array_push($errors, "Wrong username/password combination");
          }
    }
  }
  
  ?><?php 
session_start();

// initialisere variablene
$brukernavn = "";
$Epost    = "";
$errors = array(); 

// koble til database
$db = mysqli_connect('localhost', 'root', '', 'registration');

// REGISTER BRUKER
if (isset($_POST['reg_user'])) {
  // motta verdier fra registreringsskjema
  $brukernavn = mysqli_real_escape_string($db, $_POST['brukernavn']);
  $Epost = mysqli_real_escape_string($db, $_POST['E-post']);
  $passord_1 = mysqli_real_escape_string($db, $_POST['passord_1']);
  $passord_2 = mysqli_real_escape_string($db, $_POST['passord_2']);

  // Skjema validering: sjekke at skjema er riktig utfylt
  // ved å legge til (array_push()) korresponderer feil til $errors array
  if (empty($brukernavn)) { array_push($errors, "Brukernavn er nødvendig"); }
  if (empty($Epost)) { array_push($errors, "E-post er nødvendig"); }
  if (empty($passord_1)) { array_push($errors, "Passord er nødvendig"); }
  if ($passord_1 != $passord_2) {
        array_push($errors, "Passordene er ulike");
  }

  // sjekke databasen at ikke Epost og brukernavn allerede er registrert
  $user_check_query = "SELECT * FROM users WHERE username='$brukernavn' OR email='$Epost' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // Om brukernavn eksisterer
    if ($user['username'] === $brukernavn) {
      array_push($errors, "Brukernavn er allerede tatt i bruk");
    }

    if ($user['email'] === $Epost) {
      array_push($errors, "E-post er allerede i bruk");
    }
  }

  // Registrer bruker om bruker ikke finnes i skjema
  if (count($errors) == 0) {
        $passord = md5($passord_1);//kryptere passordet 

        $query = "INSERT INTO users (brukernavn, epost, password) 
                          VALUES('$brukernavn', '$Epost', '$passord')";
        mysqli_query($db, $query);
        $_SESSION['brukernavn'] = $brukernavn;
        $_SESSION['Vellykket!'] = "Du er no innlogget";
        header('location: index.php');
  }
}


/* LOGG INN BRUKER: Matcher opp mot databasen at nødvendige kriterier blir møtt eks.
 *brukernavn er i databasen og passordet samsvarer i databasen*/

 if (isset($_POST['login_user'])) {
    $brukernavn = mysqli_real_escape_string($db, $_POST['brukernavn']);
    $passord = mysqli_real_escape_string($db, $_POST['passord']);
  
    if (empty($brukernavn)) {
          array_push($errors, "Brukernavn er nødvendig");
    }
    if (empty($passord)) {
          array_push($errors, "Passord er nødvendig");
    }
  
    if (count($errors) == 0) {
          $passord = md5($passord);
          $query = "SELECT * FROM users WHERE username='$brukernavn' AND password='$passord'";
          $results = mysqli_query($db, $query);
          if (mysqli_num_rows($results) == 1) {
            $_SESSION['username'] = $brukernavn;
            $_SESSION['success'] = "Du er nå logget inn!";
            header('location: index.php');
          }else {
                  array_push($errors, "Feil brukernavn/passord");
          }
    }
  }
  
  ?>