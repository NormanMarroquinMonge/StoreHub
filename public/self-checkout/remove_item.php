<?php
session_start();
require_once '../../dbConnect.php';

header("Content-Type: application/json");

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['remove_item_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$cart_item_ID = $data['remove_item_id'];

// Delete the item from the cart
$deleteQuery = "DELETE FROM cart_items WHERE cart_item_ID = :cart_item_ID";
$deleteStmt = $dbConn->prepare($deleteQuery);
$deleteStmt->bindParam(':cart_item_ID', $cart_item_ID);
$deleteStmt->execute();

// Get the updated cart count
$cart_ID = $_SESSION['cart_ID'];
$countQuery = "SELECT SUM(quantity) AS count FROM cart_items WHERE cart_ID = :cart_ID AND is_sold = 0";
$countStmt = $dbConn->prepare($countQuery);
$countStmt->bindParam(':cart_ID', $cart_ID);
$countStmt->execute();
$countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
$new_cart_count = intval($countResult['count'] ?? 0);

// Return response
echo json_encode([
  "success" => true,
  "new_cart_count" => $new_cart_count,
]);
?>
