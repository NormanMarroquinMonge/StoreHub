<?php
// findProduct.php

require_once 'dbConnect.php';

try {

    if (!isset($_GET['objectId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No ObjectId provided']);
        exit;
    }

    $objectId = $_GET['objectId'];

    // Validate ObjectId
    if (!preg_match('/^[a-f\d]{24}$/i', $objectId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ObjectId']);
        exit;
    }

    $product = $productsCollection->findOne([
        '_id' => new MongoDB\BSON\ObjectId($objectId)
    ]);

    if ($product) {
        echo json_encode([
            'name' => $product['name'],
            'price' => $product['price'],
            'brand'=>$product['brand'],
            'id' => (string)$product['_id']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
