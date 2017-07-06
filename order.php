<?php
require_once('config.php');

//session 
session_start();

//curl function
function curlit($receive_url){
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $receive_url);
    $ccc = curl_exec($ch);
    $json = json_decode($ccc, true);
    return $json;
}

function fgc($receive_url){
    $fgc = json_decode(file_get_contents($receive_url), true);
    return $fgc;
}

mysqli_set_charset($conn,"utf8");

if($_SESSION['logged'] != 1){
   header("Location: login.php");
    exit();
}

$order = $_GET['id'];

$queryOrder = "SELECT * FROM orders WHERE orderid = '$order'";
$doOrder = mysqli_query($conn, $queryOrder) or die(mysqli_error($conn));
$fetchOrder = mysqli_fetch_assoc($doOrder);
$uname = $fetchOrder['name'];
$ship = $fetchOrder['address'];
$ship2 = $fetchOrder['address2'];
$city = $fetchOrder['city'];
$state = $fetchOrder['state'];
$zip = $fetchOrder['zip'];
$country = $fetchOrder['country'];
$email = $fetchOrder['email'];
$confirmShip = htmlspecialchars($uname)."<br>".htmlspecialchars($ship)."<br>".htmlspecialchars($ship2)."<br>".htmlspecialchars($city).", ".htmlspecialchars($state)." ".htmlspecialchars($zip)."<br>".htmlspecialchars($country)."<br>".htmlspecialchars($email);
$address = $fetchOrder['payto'];
$pkey = $fetchOrder['pkey'];
$items = $fetchOrder['items'];
$itemsArr = explode(" ", $items);
$orderItems = count($itemsArr);
$paid = $fetchOrder['paid'];
$recd = $fetchOrder['recd'];
$recdCalc = $recd / 100000000;
$cost = $fetchOrder['cost'];
if($cost > $recdCalc){
  $difMsg = "Under Paid";
} else if($recdCalc > $cost){
  $difMsg = "Over Paid";
} else {
  $difMsg = "None";
}
if($paid == 1){
   $paidMsg = "Yes - <a href='https://blockchain.info/address/".$address."' target='_blank'>View on Blockchain.info</a>";
} else {
   $paidMsg = "No - <form method='post'><input class='checkPmt' type='submit' value='Check For Payment' name='".$order."'></form>";
}

if(isset($_POST[$order])){
		   $order_num = $order;
		   $address = $address;
		   $getBalance = curlit("https://tangerine.pw/getbalance?apikey=".$tapikey."&secret=".$tapisecret."&address=".$address);
		   $fetchStatus = $getBalance["status"];
		   if($fetchStatus != "success"){
			 $message = "Error ".$getBalance["message"];   
		   } else if($getBalance["balance"] > 0){
		   $getBalAmt = $getBalance["balance"];   
		   $queryUpdate = "UPDATE orders SET paid = 1, recd = '$getBalAmt' WHERE orderid = '$order'";
		   $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
		   header("Location: order.php");
           exit();   
		   } else {
		   $message = "No Payment Yet";
		   }
		}

if(isset($_POST['complete'])){
   $queryComplete = "UPDATE orders SET complete = 1 WHERE orderid = '$order'";
   $doComplete = mysqli_query($conn, $queryComplete) or die(mysqli_error($conn));
   $message = "Order Marked Complete";
}

if(isset($_POST["sweephome"])){
	$sweepurl = "https://tangerine.pw/sweep?apikey=".$tapikey."&secret=".$tapisecret."&from=".$address;
	$dosweep = curlit($sweepurl);
	if($dosweep["status"] == "error"){
		$message = "Error: ".$dosweep["message"];		
	} else if($dosweep["status"] == "success"){
		$message = "TXID: ".$dosweep["txid"];
	} else {
		$message = "No response from tangerine server try later";
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Order</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/qrcode.js"></script>
</head>
<body>
<h1><?php echo $storeName; ?></h1>
<?php if(isset($message)){ echo "<center>".$message."</center>"; } ?>
<div id="viewCart">
  <span id="viewTitle">Order: <?php echo $order; ?></span><a href="admin.php">Back to Admin Panel</a><br><br>
     <b>Paid:</b> <?php echo $paidMsg; ?><br>
	 <b>Amount Paid:</b> <?php echo number_format($recdCalc, 8); ?><br>
	 <b>Order Amount:</b> <?php echo $cost; ?><br>
	 <b>Difference:</b> <?php echo $difMsg; ?><br>
	 <form method="post"><input type="submit" name="complete" value="Mark Order Complete"></form>
	 <br>
	 Receiving Address: <?php echo $address; ?>
	 <br>
	 <form method="post"><input type="submit" name="sweephome" value="SWEEP FUNDS TO HOME ADDRESS"></form><br>
	 
	 <br><br>
	 Ship To:
  <div class="confirmShip">
  <?php echo $confirmShip; ?>
  </div><br><br>
  Order:
  <div class="confirmShip">
 <?php echo $items."<br>"; 
 for($i=0; $i<$orderItems; $i++)
	{
	$queryLoopOrder = "SELECT * FROM products WHERE id = '$itemsArr[$i]'";
	$doLoopOrder = mysqli_query($conn, $queryLoopOrder);
	$rowLoopOrder = mysqli_fetch_assoc($doLoopOrder);
	$loopName = $rowLoopOrder['name'];
	$loopPrice = $rowLoopOrder['price'];
	echo $loopName." $".$loopPrice."<br>";
	}
 ?>
  
  </div><br>
</div>
<br>
    <?php include('footer.php'); ?>
</body>
<script   src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
</html>