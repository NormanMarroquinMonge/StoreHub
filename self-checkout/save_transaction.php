<?php
session_start();
require_once '../dbConnect.php';

$data = json_decode(file_get_contents("php://input"), true);

// Extract data from PayPal's response
$paypal_transaction_id = $data['purchase_units'][0]['payments']['captures'][0]['id'];
$order_id = $data['id'];
$payer_id = $data['payer']['payer_id'];
$payer_email = $data['payer']['email_address'];
$amount = $data['purchase_units'][0]['amount']['value'];
$status = $data['status'];
$create_time = date('Y-m-d H:i:s', strtotime($data['create_time']));
$update_time = date('Y-m-d H:i:s', strtotime($data['update_time']));

// Prepare SQL query for inserting the transaction into the transactions table
$query = "INSERT INTO transactions (paypal_transaction_id, order_id, payer_id,
          payer_email, amount, status, create_time, update_time)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $dbConn->prepare($query);

// Bind parameters correctly. Use bindValue to bind directly.
$stmt->bindValue(1, $paypal_transaction_id, PDO::PARAM_STR);
$stmt->bindValue(2, $order_id, PDO::PARAM_STR);
$stmt->bindValue(3, $payer_id, PDO::PARAM_STR);
$stmt->bindValue(4, $payer_email, PDO::PARAM_STR);
$stmt->bindValue(5, $amount, PDO::PARAM_STR);
$stmt->bindValue(6, $status, PDO::PARAM_STR);
$stmt->bindValue(7, $create_time, PDO::PARAM_STR);
$stmt->bindValue(8, $update_time, PDO::PARAM_STR);

// Execute the query to insert the transaction
if ($stmt->execute()) {
    // Ensure cart_ID is available in the session
    if (isset($_SESSION['cart_ID'])) {
        $cart_ID = $_SESSION['cart_ID'];

        // Step 1: Insert order into the orders table
        $total_price = $amount;  // Assuming total price from PayPal response
        $user_ID = $_SESSION['customer_ID'];  // Assuming the user is logged in
        $order_date = date('Y-m-d H:i:s');

        $orderQuery = "INSERT INTO orders (cart_ID, user_ID, order_date, total_price, paypal_transaction_id)
                       VALUES (?, ?, ?, ?, ?)";

        $orderStmt = $dbConn->prepare($orderQuery);
        $orderStmt->bindValue(1, $cart_ID, PDO::PARAM_INT);
        $orderStmt->bindValue(2, $user_ID, PDO::PARAM_INT);
        $orderStmt->bindValue(3, $order_date, PDO::PARAM_STR);
        $orderStmt->bindValue(4, $total_price, PDO::PARAM_STR);
        $orderStmt->bindValue(5, $paypal_transaction_id, PDO::PARAM_STR);

        if ($orderStmt->execute()) {
            // Step 2: Insert order items into the order_items table
            $order_ID = $dbConn->lastInsertId();  // Get the order_ID of the recently inserted order
            $getCartItemsQuery = "SELECT ci.product_ID, ci.quantity, p.price
                                  FROM cart_items ci
                                  JOIN products p ON ci.product_ID = p.product_ID
                                  WHERE ci.cart_ID = :cart_ID";
            $getCartItemsStmt = $dbConn->prepare($getCartItemsQuery);
            $getCartItemsStmt->bindParam(':cart_ID', $cart_ID, PDO::PARAM_INT);
            $getCartItemsStmt->execute();

            while ($item = $getCartItemsStmt->fetch(PDO::FETCH_ASSOC)) {
                // Insert each cart item into the order_items table
                $insertItemQuery = "INSERT INTO order_items (order_ID, product_ID, quantity, price)
                                    VALUES (?, ?, ?, ?)";
                $insertItemStmt = $dbConn->prepare($insertItemQuery);
                $insertItemStmt->bindValue(1, $order_ID, PDO::PARAM_INT);
                $insertItemStmt->bindValue(2, $item['product_ID'], PDO::PARAM_INT);
                $insertItemStmt->bindValue(3, $item['quantity'], PDO::PARAM_INT);
                $insertItemStmt->bindValue(4, $item['price'], PDO::PARAM_STR);
                $insertItemStmt->execute();
            }

            // Step 3: Clear the cart after successful transaction
            $clearCartQuery = "DELETE FROM cart_items WHERE cart_ID = :cart_ID";
            $clearStmt = $dbConn->prepare($clearCartQuery);
            $clearStmt->bindParam(':cart_ID', $cart_ID, PDO::PARAM_INT);

            if ($clearStmt->execute()) {
                echo json_encode(["success" => true, "message" => "Transaction saved, order placed, and cart cleared"]);
            } else {
                echo json_encode(["failure" => true, "message" => "Failed to clear the cart"]);
            }
        } else {
            echo json_encode(["failure" => true, "message" => "Failed to insert order"]);
        }
    } else {
        echo json_encode(["failure" => true, "message" => "Cart ID not found in session"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error: " . implode(", ", $stmt->errorInfo())]);
}
?>
