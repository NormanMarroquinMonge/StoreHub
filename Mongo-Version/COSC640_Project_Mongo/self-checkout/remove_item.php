<?php
session_start();
require_once '../PHP/dbConnect.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$cartItemId = $data['remove_item_id'] ?? null;

if (!$cartItemId) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Attempt to delete the item by its _id
    $deleteResult = $cartItemsCollection->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($cartItemId)
    ]);

    if ($deleteResult->getDeletedCount() !== 1) {
        echo json_encode(['success' => false, 'message' => 'Item not found or could not be deleted']);
        exit;
    }

    // After deleting, get the updated cart count
    $cartId = $_SESSION['cart_ID'];

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

    echo json_encode([
        'success' => true,
        'new_cart_count' => $newCartCount
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
