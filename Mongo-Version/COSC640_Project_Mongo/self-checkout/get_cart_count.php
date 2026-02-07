<?php
session_start();
require_once '../PHP/dbConnect.php'; // Ensure dbConnect.php sets up $cartItemsCollection

header("Content-Type: application/json");

$cartId = new MongoDB\BSON\ObjectId($_SESSION['cart_ID']);

if (!$cartId) {
    echo json_encode(["success" => false, "count" => 0]);
    exit();
}

try {
    // Aggregate to sum up the quantity of all unsold items in the cart
    $countAggregation = $cartItemsCollection->aggregate([
        ['$match' => [
            'cart_id' => $cartId,
            'is_sold' => 0
        ]],
        ['$group' => [
            '_id' => null,
            'count' => ['$sum' => '$quantity']
        ]]
    ])->toArray();

    $newCartCount = 0;
    if (!empty($countAggregation)) {
        $newCartCount = intval($countAggregation[0]['count']);
    }

    echo json_encode(["success" => true, "count" => $newCartCount]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "count" => 0]);
}
?>
