<?php
session_start();
require_once __DIR__ . '/dbConnect.php';

// Ensure the user is logged in
if (!isset($_SESSION['customer_loggedIn']) || $_SESSION['customer_loggedIn'] !== true) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if (isset($_POST['product_ID'])) {
     $product_ID = new MongoDB\BSON\ObjectId($_POST['product_ID']);
     $cart_ID = new MongoDB\BSON\ObjectId( $_SESSION['cart_ID']);
    // Step 1: Check if the item already exists in the cart
    $cartItem = $cartItemsCollection->findOne([
        'cart_id' => $cart_ID,
        'product_id' => $product_ID,
        'is_sold' => 0
    ]);

    if ($cartItem) {
      $newQuantity = $cartItem['quantity'] + 1;
      $updateResult = $cartItemsCollection->updateOne(
          ['_id' => $cartItem['_id']],
          ['$set' => ['quantity' => $newQuantity]]
      );
  } else {
      // If item doesn't exist, insert a new one
      $cartItemsCollection->insertOne([
          'cart_id' => new MongoDB\BSON\ObjectId($cart_ID),
          'product_id' => $product_ID,
          'quantity' => 1,
          'is_sold' => 0
      ]);
  }

    // Step 4: Return the updated cart item count (sum of quantities)
    $cartItemCount = $cartItemsCollection->aggregate([
        ['$match' => ['cart_id' => $cart_ID, 'is_sold' => 0]],
        ['$group' => ['_id' => '$cart_ID', 'total_quantity' => ['$sum' => '$quantity']]],
        ['$project' => ['_id' => 0, 'total_quantity' => 1]]
    ])->toArray();

    // Extract the total quantity (if available) or default to 0
    $cart_item_count = isset($cartItemCount[0]) ? $cartItemCount[0]['total_quantity'] : 0;

    echo json_encode(['cart_item_count' => $cart_item_count]);
} else {
    echo json_encode(['error' => 'No product ID provided']);
}
?>
