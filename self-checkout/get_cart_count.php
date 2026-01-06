<?php
session_start();
require_once '../dbConnect.php';

header("Content-Type: application/json");

$cart_ID = $_SESSION['cart_ID'] ?? null;
if (!$cart_ID) {
    echo json_encode(["success" => false, "count" => 0]);
    exit();
}

$countQuery = "SELECT SUM(quantity) AS count FROM cart_items WHERE cart_ID = :cart_ID AND is_sold = 0";
$countStmt = $dbConn->prepare($countQuery);
$countStmt->bindParam(':cart_ID', $cart_ID);
$countStmt->execute();
$countResult = $countStmt->fetch(PDO::FETCH_ASSOC);

$new_cart_count = intval($countResult['count'] ?? 0);

echo json_encode(["success" => true, "count" => $new_cart_count]);
?>
