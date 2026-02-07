<?php
session_start();
require_once __DIR__ . '/dbConnect.php';

if (!isset($_SESSION['cart_ID'])) {
    die("Error: Cart ID not found in session.");
}

$cart_ID = new MongoDB\BSON\ObjectId($_SESSION['cart_ID']);

//get cart items
function getCartItems($cartItemsCollection, $cart_ID) {
    $pipeline = [
        ['$match' => ['cart_id' => $cart_ID, 'is_sold' => 0]],
        [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'product_id',
                'foreignField' => '_id',
                'as' => 'product_info'
            ]
        ],
        ['$unwind' => '$product_info'],
        [
            '$project' => [
                'cart_item_ID' => '$_id',
                'quantity' => '$quantity',
                'name' => '$product_info.name',
                'price' => '$product_info.price',
                'brand' => '$product_info.brand',
                'image_path' => '$product_info.image_path'
            ]
        ]
    ];
    $cartItems = $cartItemsCollection->aggregate($pipeline)->toArray();
    return $cartItems;
}

//get total price
function getTotalPrice($cartItemsCollection, $cart_ID) {
    $pipeline = [
        ['$match' => ['cart_id' => $cart_ID, 'is_sold' => 0]],
        [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'product_id',
                'foreignField' => '_id',
                'as' => 'product_info'
            ]
        ],
        ['$unwind' => '$product_info'],
        [
            '$group' => [
                '_id' => null,
                'total_price' => ['$sum' => ['$multiply' => ['$quantity', '$product_info.price']]]
            ]
        ]
    ];

    $result = $cartItemsCollection->aggregate($pipeline)->toArray();

    return $result[0]['total_price'] ?? 0;
}

$cartItems = getCartItems($cartItemsCollection, $cart_ID);
$totalPrice = getTotalPrice($cartItemsCollection, $cart_ID);
 ?>
