<?php

require_once('config.php');

//session 
session_start();

if(isset($_POST['empty'])){
	session_destroy();
	$cartItems = count($_SESSION['tedi']);
	$cart = $_SESSION['tedi'];
	header('Location: cart.php');
	}

//count items in array
if(isset($_SESSION['tedi'])){
    $cartItems = count($_SESSION['tedi']);
    $cart = $_SESSION['tedi'];
}


if(isset($_POST['checkout'])){
   if($cartItems < 1)
   {
   $message = "<p><span class='errMsg'>You can not checkout with an empty cart</span></p>";
   }
   else
   {
   header('Location: checkout.php');
    exit();
   }
}


?>

<!DOCTYPE html>
<html>
<head>
<title>Cart</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1><?php echo $storeName; ?></h1>

<div id="viewCart">
  <span id="viewTitle">Your Cart</span>
  <div id="viewTable">
     <table width="100%">
	   <?php
 $usdOwed = 0;
 $btcOwed = 0;
 $isdigitalcounter = 0;
 if(!isset($cartItems)){
    echo "Cart is empty"; 
 } 
 else
 { 
    for($i=0; $i<$cartItems; $i++)
	{
	$queryLoopCart = "SELECT * FROM products WHERE id = '$cart[$i]'";
	$doLoopCart = mysqli_query($conn, $queryLoopCart);
	$rowLoopCart = mysqli_fetch_assoc($doLoopCart);
	$loopName = $rowLoopCart['name'];
	$loopPrice = $rowLoopCart['price'];
    $loopDigi = $rowLoopCart['isdigital'];
    if($loopDigi == 1){ $isdigitalcounter++; }
	$usdOwed += $loopPrice;
	$btcOwed = $usdOwed / $_SESSION['exr'];
	echo "<tr><td width='80%'>".$loopName."</td>";
	echo "<td width='20%'>$".$loopPrice."</td>";
	echo "</tr>";
	}
    if($isdigitalcounter ^ $cartItems){
        //not digital only sale
        $_SESSION['digitalonly'] = 0;
    } else {
        //digital only sale
        $_SESSION['digitalonly'] = 1;
    }
	echo "<tr>";
	echo "<td class='blank' width='80%'>TOTAL USD</td>";
	echo "<td width='20%'>$".$usdOwed."</td>";
	echo "</tr>";
 }
	
	echo "<tr>";
	echo "<td class='blank' width='80%'>TOTAL BTC</td>";
	echo "<td width='20%'>&#x0E3F;".round($btcOwed, 4)."</td>";
	echo "</tr>";
	?>
	 </table>
	 <br>
	 <form method="post"><input type="submit" value="Checkout" name="checkout"> <input type="submit" value="Empty Cart" name="empty"></form>
	 <br>
  </div>
  <a href="index.php">Go Back</a>
  <?php if(isset($message)){ echo $message; }?>
</div>
<?php include('footer.php'); ?>
</body>
</html>