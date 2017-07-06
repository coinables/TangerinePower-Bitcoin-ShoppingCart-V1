<?php
require_once('config.php');

//session 
session_start();

//count items in array
$cartItems = count($_SESSION['tedi']);
$cart = $_SESSION['tedi'];
$digitalonly = $_SESSION['digitalonly'];
if($digitalonly ^ 0){
}

if($cartItems < 1){
   header("Location: cart.php");
   exit();
}

mysqli_set_charset($conn,"utf8");

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

$usdOwed = 0;
	for($i=0; $i<$cartItems; $i++)
	{
	$queryLoopCart = "SELECT * FROM products WHERE id = '$cart[$i]'";
	$doLoopCart = mysqli_query($conn, $queryLoopCart) or die(mysqli_error($conn));
	$rowLoopCart = mysqli_fetch_assoc($doLoopCart);
	$loopPrice = $rowLoopCart['price'];
	$usdOwed += $loopPrice;
	}
	$btcOwed = $usdOwed / $_SESSION['exr'];

if(isset($_POST['confirm']))
{
    $paid = 0;
	$time = time();
	$complete = 0;
    $orderID = uniqid();
	$orderData = implode(" ",$_SESSION['tedi']);
	$orderCost = round($btcOwed, 4);
	$_SESSION['orderCost'] = $orderCost;
	$email = $_SESSION['email'];
	$name = $_SESSION['name'];
	$address = $_SESSION['address'];
	$address2 = $_SESSION['address2'];
	$city = $_SESSION['city'];
	$state = $_SESSION['state'];
	$zip = $_SESSION['zip'];
	$country = $_SESSION['country'];
	
	//generate new key pair
    $tangnewkey = "https://tangerine.pw/create?apikey=".$tapikey."&secret=".$tapisecret;
	$coin = curlit($tangnewkey);
	if($coin["status"] != "success"){
		die("Config Error");
	}
	$payTo = $coin["address"];
	
	$_SESSION['payTo'] = $payTo;
	
	//insert into DB
	$queryOrder = "INSERT INTO orders (orderid, time, name, address, address2, city, state, zip, country, email, cost, payto, items, paid, complete) VALUES ('$orderID', '$time', '$name', '$address', '$address2', '$city', '$state', '$zip', '$country', '$email', '$orderCost', '$payTo', '$orderData', '$paid', '$complete')";
	$doOrder = mysqli_query($conn, $queryOrder) or die(mysqli_error($conn));
	if(!$doOrder)
			{
			die('Error: '.mysqli_error($conn));
			}
			else 
			{
			$host = $_SERVER['SERVER_NAME'];
			$emailTitle = "New Purchase";
			$emailTitle_Customer = "Order Confirmation";
			$bodyEmail = <<<EOD
        <h1>New Purchase</h1>
        Order: $orderID <br>
        Email: $email <br>
        Name: $name <br>
        Address: $address <br>
		$address2 <br>
        City: $city <br>
        State: $state <br>
        Zip: $zip <br>
        Payment Address: $payTo <br>
		Payment Amount: $orderCost <br>
EOD;
 
        $headers = "From: noreply@".$host."\r\n";
        $headers .= "Content-type: text/html\r\n";
        $success = mail("$yourEmail", "$emailTitle", "$bodyEmail", "$headers");
         
        $custEmail = <<<EOD
        <h3>Please send payment to finalize your purchase</h3>
        Payment Address: $payTo <br>
        Payment Amount: $orderCost <br>
        Order: $orderID <br>
        Email: $email <br>
        Name: $name <br>
        Address: $address <br>
		$address2 <br>
        City: $city <br>
        State: $state <br>
        Zip: $zip <br>        
EOD;
        $customerCopy = mail($email, $emailTitle_Customer, $custEmail, $headers);
			header('Location: pay.php');
            exit();
			}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Confirm</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1><?php echo $storeName; ?></h1>

<div id="viewCart">
  <span id="viewTitle">Review & Confirm</span><br>
  Ship To:
  <div class="confirmShip">
  <?php 
    
    if($digitalonly ^ 0){  
        echo "<b>THIS IS A DIGITAL SALE</b><br>";
        echo htmlspecialchars($_SESSION['email'])."<br>";
    } else {
        echo htmlspecialchars($_SESSION['name'])."<br>";
        echo htmlspecialchars($_SESSION['address'])."<br>";
        echo htmlspecialchars($_SESSION['address2'])."<br>";
        echo htmlspecialchars($_SESSION['city']);
        echo ", ".htmlspecialchars($_SESSION['state']);
        echo " ".htmlspecialchars($_SESSION['zip'])."<br>";
        echo htmlspecialchars($_SESSION['email'])."<br>";
    }
  
  ?>
  <a href="checkout.php">EDIT</a>
  </div><br><br>
  Order:
  <div class="confirmShip">
  <?php
 $usdOwed = 0;
	for($i=0; $i<$cartItems; $i++)
	{
	$queryLoopCart = "SELECT * FROM products WHERE id = '$cart[$i]'";
	$doLoopCart = mysqli_query($conn, $queryLoopCart);
	$rowLoopCart = mysqli_fetch_assoc($doLoopCart);
	$loopName = $rowLoopCart['name'];
	$loopPrice = $rowLoopCart['price'];
	$usdOwed += $loopPrice;
	$btcOwed = $usdOwed / $_SESSION['exr'];
	echo $loopName." $".$loopPrice."<br>";
	}
	echo "$".$usdOwed."<br>";
	echo "&#x0E3F;".round($btcOwed, 4)."<br>";
	?>
  <a href="cart.php">EDIT</a>
  </div><br>
  <div id="checkCont"><form method="post"><input type="submit" class="button" value="CONFIRM & PAY" name="confirm"></form></div>
  <br>
</div>
<br>
    <?php include('footer.php'); ?>
</body>
</html>