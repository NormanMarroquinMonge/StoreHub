<?php
session_start();
require_once 'dbConnect.php';

if (!isset($_SESSION['customer_loggedIn']) || $_SESSION['customer_loggedIn'] !== true) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if (isset($_POST['product_ID'])) {
    $product_ID = $_POST['product_ID'];
    $cart_ID = $_SESSION['cart_ID'];

    // Check if item already exists in cart
    $query = 'SELECT quantity FROM cart_items WHERE cart_ID = :cart_ID AND product_ID = :product_ID AND is_sold = 0';
    $stmt = $dbConn->prepare($query);
    $stmt->execute([
        ':cart_ID' => $cart_ID,
        ':product_ID' => $product_ID
    ]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // If exists, update quantity
        $newQuantity = $item['quantity'] + 1;

        $update = 'UPDATE cart_items SET quantity = :quantity WHERE cart_ID = :cart_ID AND product_ID = :product_ID AND is_sold = 0';
        $stmt = $dbConn->prepare($update);
        $stmt->execute([
            ':quantity' => $newQuantity,
            ':cart_ID' => $cart_ID,
            ':product_ID' => $product_ID
        ]);
    } else {
        // Step 3: If not exists, insert new item with quantity = 1
        $insert = 'INSERT INTO cart_items (cart_ID, product_ID, quantity, is_sold) VALUES (:cart_ID, :product_ID, 1, 0)';
        $stmt = $dbConn->prepare($insert);
        $stmt->execute([
            ':cart_ID' => $cart_ID,
            ':product_ID' => $product_ID
        ]);
    }

    // Step 4: Return updated cart item count (sum of quantities)
    $countQuery = 'SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE cart_ID = :cart_ID AND is_sold = 0';
    $stmt = $dbConn->prepare($countQuery);
    $stmt->execute([':cart_ID' => $cart_ID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_item_count = $result['total_quantity'] ?? 0;

    echo json_encode(['cart_item_count' => $cart_item_count]);
} else {
    echo json_encode(['error' => 'No product ID provided']);
}
?>
