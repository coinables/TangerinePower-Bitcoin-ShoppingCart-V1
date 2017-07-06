<?php
require_once('config.php');

//session 
session_start();

if($_SESSION['logged'] != 1){
   header("Location: login.php");
    exit();
}

if(isset($_POST['logout'])){
   session_destroy();
   header("Location: login.php");
    exit();
}

//paginate
if(isset($_POST["moreBtn"])){
			$_SESSION["startPag"] += 20;
			header("Location: ");
	}
if(isset($_POST["lessBtn"])){
			$_SESSION["startPag"] -= 20;
			header("Location: ");
}

$startPag = $_SESSION["startPag"];
$showMoreBtn = "SELECT * FROM orders";
$queryMoreBtn = mysqli_query($conn, $showMoreBtn);
$rowsMoreBtn = mysqli_num_rows($queryMoreBtn);
//count num rows of orders
//if > 20 show "next" button
//$startPag = $_SESSION["startPag"] + 20;
if($rowsMoreBtn > ($startPag + 20)){
    //show next button
    $nextBtn = '<form method="post"><input type="submit" name = "moreBtn" value="OLDER"></form>';
} else {
    //dont show next button
    $nextBtn = "";
}
//back button if start pag > than 19
if($startPag > 19){
    //show back
    $backBtn = '<form method="post"><input type="submit" name = "lessBtn" value="NEWER"></form>';
} else {
    $backBtn = "";
}

?>
<!DOCTYPE html>
<html>
<head>
<title>All Orders</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1><?php echo $storeName; ?></h1>
<div id="viewCart">
<form method="post"><input type="submit" id="logout" name="logout" value="Logout"></form>
  <span id="viewTitle">All Orders</span><a href="admin.php">Back to Admin Panel</a><br><br>
     <table class="productTable">
	 <tr>
	    <td class="tableHeader">Order ID</td>
		<td class="tableHeader">Items</td>
		<td class="tableHeader">Amount</td>
		<td class="tableHeader">Paid?</td>
		<td class="tableHeader">Completed?</td>
        <td class="tableHeader">Time</td>
	 </tr>
	 <?php 
	 $queryOrders = "SELECT * FROM orders ORDER BY time DESC LIMIT $startPag,20";
	 $doOrders = mysqli_query($conn, $queryOrders) or die(mysqli_error($conn));
	 while($loopOrders = mysqli_fetch_assoc($doOrders))
	 {
	 echo "<tr>";
	 echo "<td><a href='order.php?id=".$loopOrders['orderid']."'>".$loopOrders['orderid']."</a></td>";
	 echo "<td>".$loopOrders['items']."</td>";
	 echo "<td>".$loopOrders['cost']."</td>";
	 if($loopOrders['paid'] == 1){ $loopPaid = "Yes"; } else { $loopPaid = "No"; }
	 echo "<td>".$loopPaid."</td>";
	 if($loopOrders['complete'] == 1){ $loopComplete = "Yes"; } else { $loopComplete = "No"; }
	 echo "<td>".$loopComplete."</td>";
     echo "<td>".gmdate("D M j G:i T", $loopOrders['time'])."</td>";
	 echo "</tr>";
	 }
	 ?>
	 </table><br>
    <?php 
    echo $backBtn ? $backBtn : "";
    echo $nextBtn ? $nextBtn : ""; 
    ?>
</div>
<br>
    <?php include('footer.php'); ?>
</body>
</html>