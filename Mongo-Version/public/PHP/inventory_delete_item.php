<?php
require_once 'dbConnect.php'; // Connect to DB

header('Content-Type: application/json'); // Tell browser it's JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['Product_ID'];

    if (!empty($productID)) {
        $productObjectId = new MongoDB\BSON\ObjectId($productID);

        // Find the product before deleting
        $product = $productsCollection->findOne(['_id' => $productObjectId]);

        if ($product) {
            $productName = $product['name'];

            // Delete inventory first
            $inventoryCollection->deleteOne(['product_id' => $productObjectId]);

            // Then delete the product
            $productsCollection->deleteOne(['_id' => $productObjectId]);

            echo json_encode([
                'success' => true,
                'name' => $productName,
                'productID' => (string)$productID
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Product ID is missing.']);
    }
}
?>
