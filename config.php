<?php

//database login info
$dbuser = "YOUR_DB_USERNAME";
$dbpw = "YOUR_DB_PASSWORD";
$db = "YOUR_DB";

//Specific to you the store owner
$storeName = "Tangerine Power Open Cart";
$rootURL = "mywebsite.com/cart"; //example https://mysite.org  or http://yourhomepage.com/store
$yourEmail = "your@email.com";  //email notifications will be sent to this email when a new order is placed if your hosting supports the mail() function

//default password to access the admin pages, VERY IMPORT to change this before using
$adminPW = "honeybadgerbubblegum"; 

//tangerine apikeys
$tapikey = "YOUR_TANGERINE_APIKEY";
$tapisecret = "YOUR_API_SECRET";

//connect to the database
$conn = mysqli_connect("localhost", $dbuser, $dbpw, $db);
if(!$conn){
  die('Connection error check server log');
}

?>
