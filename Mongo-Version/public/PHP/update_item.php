<?php
// update_item.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'dbConnect.php'; // connects to Mongo Atlas and gets $inventoryCollection and $productsCollection

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['inventory_ID']) || !isset($data['updated_fields'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $inventoryId = $data['inventory_ID'];
    $updatedFields = $data['updated_fields'];

    // Split fields between product and inventory updates
    $productFields = ['name', 'price', 'brand'];
    $inventoryFields = ['quantity', 'last_restock'];

    $productUpdates = [];
    $inventoryUpdates = [];

    foreach ($updatedFields as $field => $value) {
        if (in_array($field, $productFields)) {
            if ($field === 'price') {
                $productUpdates[$field] = (float)$value; // Cast price to float
            } else {
                $productUpdates[$field] = $value;
            }
        } elseif (in_array($field, $inventoryFields)) {
            // Ensure quantity is an integer
            if ($field === 'quantity') {
                $inventoryUpdates[$field] = (int)$value; // cast to integer
            } else {
                $inventoryUpdates[$field] = $value;
            }
        }
    }

    // First: find the linked product_id
    $inventoryDoc = $inventoryCollection->findOne(
        ['_id' => new MongoDB\BSON\ObjectId($inventoryId)],
        ['projection' => ['product_id' => 1]]
    );

    if (!$inventoryDoc || !isset($inventoryDoc->product_id)) {
        echo json_encode(['success' => false, 'message' => 'Inventory item not found or missing product_id']);
        exit;
    }

    $productId = $inventoryDoc->product_id;

    // Update inventory fields
    if (!empty($inventoryUpdates)) {
        $inventoryCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($inventoryId)],
            ['$set' => $inventoryUpdates]
        );
    }

    // Update product fields
    if (!empty($productUpdates)) {
        $productsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($productId)],
            ['$set' => $productUpdates]
        );
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
