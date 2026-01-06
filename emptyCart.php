<?php
    session_start(); // Start the session at the beginning of each protected page
include 'dbConnect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cartID = $_POST['userInput'];
$updateCart = "UPDATE shopping_cart set checked_out= 1 where cart_ID = $cartID";
$dbConn->query($updateCart);
}
header("Location: checkout.php ");
exit();
?>