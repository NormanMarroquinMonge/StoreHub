<?php
session_start();
require_once '../../dbConnect.php';

$data = json_decode(file_get_contents("php://input"), true);
$cartItemId = $data['cart_item_id'] ?? null;
$action = $data['action'] ?? null;

if (!$cartItemId || !in_array($action, ['increase', 'decrease'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get current quantity
$query = "SELECT quantity FROM cart_items WHERE cart_item_ID = :cart_item_ID";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':cart_item_ID', $cartItemId);
$stmt->execute();
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$newQuantity = $current['quantity'] + ($action === 'increase' ? 1 : -1);
$newQuantity = max($newQuantity, 1); // Prevent quantity from going below 1

$update = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_ID = :cart_item_ID";
$updateStmt = $dbConn->prepare($update);
$updateStmt->bindParam(':quantity', $newQuantity);
$updateStmt->bindParam(':cart_item_ID', $cartItemId);
$updateStmt->execute();

echo json_encode(['success' => true, 'new_quantity' => $newQuantity]);
?>
