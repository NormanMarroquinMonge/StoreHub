<?php
session_start();
require_once 'dbConnect.php'; // this connects you to MongoDB, and defines $cartItemsCollection, $productsCollection, etc.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemID = $_POST['userInput'];

    // 1. Get cart item
    $cartItem = $cartItemsCollection->findOne([
        '_id' => new MongoDB\BSON\ObjectId($itemID)
    ]);

    if (!$cartItem) {
        die("Cart item not found.");
    }

    $productID = $cartItem['product_id'];
    $cartID = $cartItem['cart_ID'];

    // 2. Get product price
    $product = $productsCollection->findOne([
        '_id' => $productID
    ]);

    if (!$product) {
        die("Product not found.");
    }

    $price = $product['price'];
    $discount = $price;

    // 3. Apply promotions
    $promotionsCursor = $promotionsCollection->find([
        '$and' => [
            [
                '$or' => [
                    ['product_ID' => $productID],
                    ['category_ID' => $product['category_ID']]
                ]
            ],
            ['start' => ['$lte' => new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp() * 1000)]],
            ['end' => ['$gte' => new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp() * 1000)]]
        ]
    ]);

    foreach ($promotionsCursor as $promotion) {
        $promotionAmount = $promotion['promotion_amount'];
        if (strpos($promotionAmount, '*') !== false) {
            $multiplier = floatval(str_replace('*', '', $promotionAmount));
            $discount *= $multiplier;
        } elseif (strpos($promotionAmount, '-') !== false) {
            $discount += floatval($promotionAmount);
        }
    }

    // 4. Insert into sales
    $saleResult = $salesCollection->insertOne([
        'cart_item_ID' => $cartItem['_id'],
        'date' => new MongoDB\BSON\UTCDateTime(),
        'returned' => false,
        'revenue' => $discount
    ]);

    $saleID = $saleResult->getInsertedId();

    // 5. Update cart item to sold
    $cartItemsCollection->updateOne(
        ['_id' => $cartItem['_id']],
        ['$set' => ['is_sold' => true]]
    );

    // 6. Update inventory (decrease quantity)
    $inventoryCollection->updateOne(
        ['_id' => $productID],
        ['$inc' => ['quantity' => -1]]
    );

    // 7. Update customer's credit line
    $customer = $shoppingCartsCollection->findOne([
        '_id' => $cartID
    ]);

    if (!$customer) {
        die("Shopping cart not found.");
    }

    $customersCollection->updateOne(
        ['_id' => $customer['customer_ID']],
        ['$inc' => ['credit_line' => $discount]]
    );

    // 8. Insert into order history
    $orderHistoryCollection->insertOne([
        'sale_ID' => $saleID,
        'customer_ID' => $customer['customer_ID']
    ]);
}

header("Location: checkoutManual.php");
exit();
?>
