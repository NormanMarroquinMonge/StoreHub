<?php
session_start();
require_once '../PHP/dbConnect.php'; // This should set $transactionsCollection, $ordersCollection, $orderitemsCollection, and $cartItemsCollection

$data = json_decode(file_get_contents("php://input"), true);

// Extract data from PayPal's response
$paypal_transaction_id = $data['purchase_units'][0]['payments']['captures'][0]['id'];
$order_id = $data['id'];
$payer_id = $data['payer']['payer_id'];
$payer_email = $data['payer']['email_address'];
$amount = (float) $data['purchase_units'][0]['amount']['value'];
$status = $data['status'];
$create_time = new MongoDB\BSON\UTCDateTime(strtotime($data['create_time']) * 1000);
$update_time = new MongoDB\BSON\UTCDateTime(strtotime($data['update_time']) * 1000);

try {
    // Insert into transactions collection
    $transactionResult = $transactionsCollection->insertOne([
        'paypal_transaction_id' => $paypal_transaction_id,
        'order_id' => $order_id,
        'payer_id' => $payer_id,
        'payer_email' => $payer_email,
        'amount' => $amount,
        'status' => $status,
        'create_time' => $create_time,
        'update_time' => $update_time
    ]);

    if (isset($_SESSION['cart_ID']) && isset($_SESSION['customer_ID'])) {
        $cart_ID = new MongoDB\BSON\ObjectId($_SESSION['cart_ID']);
        $customer_ID = new MongoDB\BSON\ObjectId($_SESSION['customer_ID']);
        $order_date = new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp() * 1000);

        // Insert into orders collection
        $orderResult = $ordersCollection->insertOne([
            'cart_ID' => $cart_ID,
            'customer_ID' => $customer_ID,
            'order_date' => $order_date,
            'total_price' => $amount,
            'paypal_transaction_id' => $paypal_transaction_id
        ]);

        $order_ID = $orderResult->getInsertedId(); // MongoDB ObjectId of the new order

        // Get cart items from cart_items collection
        $cartItemsCursor = $cartItemsCollection->find(['cart_id' => $cart_ID]);

        foreach ($cartItemsCursor as $item) {
          // Lookup the product by its _id
          $product = $productsCollection->findOne([
            '_id' => $item['product_id']
          ]);

          if ($product) {
            $orderitemsCollection->insertOne([
              'order_ID' => $order_ID,
              'product_ID' => $item['product_id'],
              'quantity' => $item['quantity'],
              'price' => $product['price'] // pull price from products collection
            ]);
          } else {
            // Optionally handle missing product
             echo "Product not found: " . $item['product_id'];
          }
        }

        // Clear the cart (delete cart items for this cart_ID)
        $deleteResult = $cartItemsCollection->deleteMany(['cart_id' => $cart_ID]);

        echo json_encode(["success" => true, "message" => "Transaction saved, order placed, and cart cleared"]);
    } else {
        echo json_encode(["failure" => true, "message" => "Cart ID or Customer ID not found in session"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
