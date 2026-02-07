<?php
session_start();
require_once '../PHP/dbConnect.php'; // Ensure dbConnection.php contains the MongoDB connection

$data = json_decode(file_get_contents("php://input"), true);
$cartItemId = $data['cart_item_id'] ?? null;
$action = $data['action'] ?? null;

if (!$cartItemId || !in_array($action, ['increase', 'decrease'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Find the cart item by its cart_item_ID
    $cartItem = $cartItemsCollection->findOne([
        '_id' => new MongoDB\BSON\ObjectId($cartItemId)
    ]);

    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }

    // Calculate the new quantity
    $newQuantity = $cartItem['quantity'] + ($action === 'increase' ? 1 : -1);
    $newQuantity = max($newQuantity, 1); // Prevent quantity from going below 1

    // Update the cart item with the new quantity
    $updateResult = $cartItemsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($cartItemId)],
        ['$set' => ['quantity' => $newQuantity]]
    );

    if ($updateResult->getModifiedCount() === 1) {
        // Return success and the new quantity
        echo json_encode(['success' => true, 'new_quantity' => $newQuantity]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
