<!DOCTYPE html>
<?php 
include('../../controllers/loginController.php');
include './../partials/navbar.php';  
include '../partials/user_info.php'; 

?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleGlobal.css">
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleHomePage.css">
    <link rel="stylesheet" type="text/css" href="./../../public/css/styleLogin.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title></title>
</head>
<body>


<div class="header">
    <h2>Login</h2>
</div>
<!-- Login skjema/form -->
 <div class="form-wrapper">

<form method="post" action="login.php">
        <?php include('../../middlewares/errors.php'); ?>
        <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" >
        </div>
        <div class="input-group">
                <label>Password</label>
                <input type="password" name="password">
        </div>
        <div class="input-group">
                <button type="submit" class="btn" name="login_user">Login</button>
        </div>
        <p>
                Not yet a member? <a href="register.php">Sign up</a>
        </p>
  </form>
</div>
<!-- footer -->

<footer>
    <p><?php echo htmlspecialchars($stamp); ?></p>
</footer>
</body>

</html>

