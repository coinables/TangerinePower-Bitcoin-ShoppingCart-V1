<?php
require_once('config.php');

//session 
session_start();

//count items in array
$cartItems = count($_SESSION['tedi']);

//redirect if self navigating pages
if($cartItems < 1)
   {
   header("Location: cart.php");
    exit();
   }
   
if(!$_SESSION['payTo']) 
	{
	header("Location: confirm.php");
    exit();
	}  

?>

<!DOCTYPE html>
<html>
<head>
<title>Pay</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/qrcode.js"></script>

</head>
<body>
<h1><?php echo $storeName; ?></h1>
<div id="viewCart">
  <span id="viewTitle">Finish & Pay</span><br>
  <div id="payAmt"><b>Amount Due: <?php echo $_SESSION['orderCost']; ?> BTC</b><br>
  Remit payment to the below address to complete your order. <br>
  <div id="qrcode"></div>
  <script type="text/javascript">
  new QRCode(document.getElementById("qrcode"), "<?php echo $_SESSION['payTo']; ?>");
  </script>
  <br>
  <input type="text" id="payBox" value="<?php echo $_SESSION['payTo']; ?>" onclick="this.select();" readonly>
  <br><div id="messages"></div>
  </div>
</div>
<br>
    <?php include('footer.php'); ?>
<?php 
session_destroy();
?>
    
</body>
</html>