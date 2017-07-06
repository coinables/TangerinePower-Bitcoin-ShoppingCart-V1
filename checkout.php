<?php
require_once('config.php');

//session 
session_start();

//count items in array
$cartItems = count($_SESSION['tedi']);
$cart = $_SESSION['tedi'];
$digitalonly = $_SESSION['digitalonly'];
if($digitalonly ^ 0){ $digitalonlymsg = "<h2>THIS IS A DIGITAL ONLY SALE. THE ONLY REQUIRED FIELD IS YOUR EMAIL ADDRESS.</h2>"; }

//redirect if self navigating pages
if($cartItems < 1)
   {
   header("Location: cart.php");
    exit();
   }

if(isset($_POST['submit'])){
    
        //digital only
        if($digitalonly ^ 0){
            $_SESSION['email'] = mysqli_real_escape_string($conn, $_POST['email']);

            if(empty($_SESSION['email'])){
            $message = "<span class='errMsg'>Form is incomplete!</span>";
            } else {
            header('Location: confirm.php');
            exit();
            } 
        }
        else
        {
            $_SESSION['email'] = mysqli_real_escape_string($conn, $_POST['email']);
            $_SESSION['name'] = trim($_POST['name']);
            $_SESSION['address'] = trim($_POST['address']);
            $_SESSION['address2'] = trim($_POST['address2']);
            $_SESSION['city'] = trim($_POST['city']);
            $_SESSION['state'] = trim($_POST['state']);
            $_SESSION['zip'] = trim($_POST['zip']);
            $_SESSION['country'] = trim($_POST['country']);
            $_SESSION['name'] = mysqli_real_escape_string($conn, $_SESSION['name']);
            $_SESSION['address'] = mysqli_real_escape_string($conn, $_SESSION['address']);
            $_SESSION['address2'] = mysqli_real_escape_string($conn, $_SESSION['address2']);
            $_SESSION['city'] = mysqli_real_escape_string($conn, $_SESSION['city']);
            $_SESSION['state'] = mysqli_real_escape_string($conn, $_SESSION['state']);
            $_SESSION['zip'] = mysqli_real_escape_string($conn, $_SESSION['zip']);
            $_SESSION['country'] = mysqli_real_escape_string($conn, $_SESSION['country']);

            if(empty($_SESSION['email']) || empty($_SESSION['name']) || empty($_SESSION['address']) || empty($_SESSION['city']) || empty($_SESSION['zip'])){
            $message = "<span class='errMsg'>Form is incomplete!</span>";
            } else {
            header('Location: confirm.php');
            exit();
            }   
        }        
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1><?php echo $storeName; ?></h1>

<div id="viewCart">
  <span id="viewTitle">Shipping Information</span><br>
    <?php echo isset($digitalonlymsg) ? $digitalonlymsg : ""; ?>
  <form method="post">
  EMAIL<br>
  <input type="email" class="text" name="email" value="<?php if(isset($_SESSION['email'])){ echo $_SESSION['email']; }?>"><br>
  NAME<br>
  <input type="text" class="text" name="name" value="<?php if(isset($_SESSION['name'])){ echo $_SESSION['name']; }?>"><br>
  ADDRESS<br>
  <input type="text" class="text" name="address" value="<?php if(isset($_SESSION['address'])){ echo $_SESSION['address']; }?>"><br>
  ADDRESS 2<br>
  <input type="text" class="text" name="address2" value="<?php if(isset($_SESSION['address2'])){ echo $_SESSION['address2']; }?>"><br>
  CITY<br>
  <input type="text" class="text" name="city" value="<?php if(isset($_SESSION['city'])){ echo $_SESSION['city']; }?>"><br>
  STATE/PROVINCE/REGION<br>
  <input type="text" class="text" name="state" value="<?php if(isset($_SESSION['state'])){ echo $_SESSION['state']; }?>"><br>
  ZIP/POSTAL CODE<br>
  <input type="text" class="text" name="zip" value="<?php if(isset($_SESSION['zip'])){ echo $_SESSION['zip']; }?>"><br>
  COUNTRY<br>
  <input type="text" class="text" name="country" value="<?php if(isset($_SESSION['country'])){ echo $_SESSION['country']; }?>"><br><br>
  <div id="checkCont"><input type="submit" class="button" value="SUBMIT" name="submit"></form></div>
  <a href="cart.php">Go Back</a><br>
  <?php if(isset($message)){ echo $message; } ?>
</div>
<br>
    <?php include('footer.php'); ?>
</body>
</html>