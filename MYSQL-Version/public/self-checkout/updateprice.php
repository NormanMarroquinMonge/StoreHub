<?php
session_start();
require_once '../../dbConnect.php';

$cart_ID = $_SESSION['cart_ID'];

// Fetch the new total price
$totalQuery = "SELECT SUM(p.price * ci.quantity) AS total_price
               FROM cart_items ci
               JOIN products p ON ci.product_ID = p.product_ID
               WHERE ci.cart_ID = :cart_ID AND ci.is_sold = 0";
$totalStmt = $dbConn->prepare($totalQuery);
$totalStmt->bindParam(':cart_ID', $cart_ID);
$totalStmt->execute();
$totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalPrice = $totalResult['total_price'] ?? 0;

$totalPrice = floatval($totalPrice);

header('Content-Type: application/json');
// Return updated total as JSON
echo json_encode(['total_price' => $totalPrice]);
?>
