<?php
session_start();
require_once '../PHP/dbConnect.php'; // Make sure dbConnect.php sets up $cartItemsCollection and $productsCollection

header("Content-Type: application/json");

$cartId = new MongoDB\BSON\ObjectId($_SESSION['cart_ID']);

if (!$cartId) {
    echo json_encode(['total_price' => 0]);
    exit;
}

try {
    // Aggregate to calculate total price
    $totalAggregation = $cartItemsCollection->aggregate([
        ['$match' => [
            'cart_id' => $cartId,
            'is_sold' => 0
        ]],
        ['$lookup' => [
            'from' => 'products',
            'localField' => 'product_id',
            'foreignField' => '_id',
            'as' => 'product_info'
        ]],
        ['$unwind' => '$product_info'],
        ['$group' => [
            '_id' => null,
            'total_price' => [
                '$sum' => [
                    '$multiply' => ['$product_info.price', '$quantity']
                ]
            ]
        ]]
    ])->toArray();

    $totalPrice = 0;
    if (!empty($totalAggregation)) {
        $totalPrice = floatval($totalAggregation[0]['total_price']);
    }

    echo json_encode(['total_price' => $totalPrice]);

} catch (Exception $e) {
    echo json_encode(['total_price' => 0]);
}
?>
