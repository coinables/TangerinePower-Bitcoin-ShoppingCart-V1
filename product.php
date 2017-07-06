<?php
require_once('config.php');

//session 
session_start();

if($_SESSION['logged'] != 1){
   header("Location: login.php");
    exit();
}

$product = $_GET['item'];

$queryItem = "SELECT * FROM products WHERE id = '$product'";
$doItem = mysqli_query($conn, $queryItem) or die(mysqli_error($conn));
$fetchItem = mysqli_fetch_assoc($doItem);
$iname = $fetchItem['name'];
$iprice = $fetchItem['price'];
$idesc = $fetchItem['description'];
$iimage = $fetchItem['image'];
$istock = $fetchItem['in_stock'];
$idigital = $fetchItem['isdigital'];

if(isset($_POST['update']))
{
   
  $name = mysqli_real_escape_string($conn, $_POST['pname']);
  $cost = mysqli_real_escape_string($conn, $_POST['price']);
  if($cost < 0.01){ $cost = 0.01; }
  $desc = mysqli_real_escape_string($conn, $_POST['description']);
  $image = mysqli_real_escape_string($conn, $_POST['image']);
  $instock = mysqli_real_escape_string($conn, $_POST['stock']);
  $isdigital = mysqli_real_escape_string($conn, $_POST['digital']);
  $queryUpdate = "UPDATE products SET name = '$name', price = '$cost', description = '$desc', image = '$image', in_stock = '$instock', isdigital = '$isdigital' WHERE id = '$product'";
  $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
  $message = "<h3>Item Updated</h3>";
  //update form 
  $queryItem = "SELECT * FROM products WHERE id = '$product'";
  $doItem = mysqli_query($conn, $queryItem) or die(mysqli_error($conn));
  $fetchItem = mysqli_fetch_assoc($doItem);
  $iname = $fetchItem['name'];
  $iprice = $fetchItem['price'];
  $idesc = $fetchItem['description'];
  $iimage = $fetchItem['image'];
  $istock = $fetchItem['in_stock'];
  $idigital = $fetchItem['isdigital'];
}

if(isset($_POST["delete"])){
  $deleteItem = "DELETE FROM products WHERE id = '$product'";
  mysqli_query($conn, $deleteItem);
  header("Location: admin.php");
  exit();
}


?>
<!DOCTYPE html>
<html>
<head>
<title>Product Edit</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1><?php echo $storeName; ?></h1>
<?php if(isset($message)){ echo "<center>".$message."</center>"; } ?>
<div id="viewCart">
  <span id="viewTitle">Edit Product: <?php echo $product; ?></span><a href="admin.php">Back to Admin Panel</a><br><br>
    <b>Product Name</b><br>
	  <form method="post">
	  <input type="text" class="text" name="pname" value="<?php echo $iname; ?>"><br>
	  <b>Price USD</b><br>
	  <input type="text" class="text" name="price" value="<?php echo $iprice; ?>"><br>
	  <b>Description</b><br>
	  <textarea class="inputArea" name="description"><?php echo $idesc; ?></textarea><br><br>
	  <b>Image Link</b> example: http://i.stack.imgur.com/m9uaE.png<br><br>
	  <input type="url" class="text" name="image" value="<?php echo $iimage; ?>"><br>
	  <b>Item In Stock?</b> Marking it "No" will hide the item from visitors<br>
	  <input type="radio" name="stock" <?php if(isset($istock) && $istock == "1"){ echo "checked"; } ?> value="1">Yes
	  <input type="radio" name="stock" <?php if(isset($istock) && $istock == "0"){ echo "checked"; } ?> value="0">No <br><br>
      <b>Digital Item?</b> Shipping Address not required on digital item checkout<br>
	  <input type="radio" name="digital" <?php if(isset($idigital) && $idigital == "1"){ echo "checked"; } ?> value="1">Yes
	  <input type="radio" name="digital" <?php if(isset($idigital) && $idigital == "0"){ echo "checked"; } ?> value="0">No <br>
	  <input type="submit" id="add" name="update" value="Update">
	  </form>
  
  <br>

        <form method="post">
        <input type="submit" value="DELETE ITEM" class="dangerbtn" name="delete" onclick="return confirm('Are you sure?');">
        </form>
</div>
<br>
    <?php include('footer.php'); ?>
</body>
</html>