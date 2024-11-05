<?php include('server.php') ?> <!-- import server -->
<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  <link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
  <div class="header"> 
        <h2>Register</h2>
  </div>
        
  <form method="post" action="register.php"> <!-- Form -->
        <?php include('errors.php'); ?> <!-- Include errors -->
        <div class="input-group"> <!-- Username group -->
          <label>Username</label>
          <input type="text" name="username" value="<?php echo $username; ?>"> <!-- Username input -->
        </div>
        <div class="input-group"> <!-- Email group -->
          <label>Email</label>
          <input type="email" name="email" value="<?php echo $email; ?>"> <!-- Email input -->
        </div>
        <div class="input-group"> <!-- Password group -->
          <label>Password</label>
          <input type="password" name="password_1"> <!-- Password input -->
        </div>
        <div class="input-group"> <!-- Confirm password group -->
          <label>Confirm password</label>
          <input type="password" name="password_2"> <!-- Confirm password input -->
        </div>
        <div class="input-group"> <!-- Button group -->
          <button type="submit" class="btn" name="reg_user">Register</button> <!-- Register button -->
        </div>
        <p>
                Already a member? <a href="login.php">Sign in</a> <!-- Sign in link -->
        </p>
  </form> 
</body>
</html>