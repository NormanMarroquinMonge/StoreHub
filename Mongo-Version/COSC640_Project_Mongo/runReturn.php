<?php
    session_start(); // Start the session at the beginning of each protected page
    include 'dbConnect.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $number = $_POST['order_ID'];
        $reason = $_POST['reason'];
        $orderID = filter_var($number, FILTER_VALIDATE_INT);
        if ($orderID !== false) {
            $query = "SELECT customer_ID, sale_ID FROM order_history WHERE order_ID = :orderID";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $orderHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($orderHistory)) {
                    //The return process has 5 quries
                        //get the order_ID 
                        $iIDQuery ="SELECT sales.cart_item_ID FROM order_history JOIN sales 
                        ON order_history.sale_ID = sales.sale_ID WHERE order_history.order_ID = :orderID";
                        $iIDStmt = $dbConn->prepare($iIDQuery);
                        $iIDStmt->execute([':orderID' => $orderID]);
                        $itemID = $iIDStmt->fetchColumn();
                        //get the product_ID
                        $pIDQuery = "SELECT product_ID FROM cart_items WHERE cart_item_ID = :itemID";
                        $pIDStmt = $dbConn->prepare($pIDQuery);
                        $pIDStmt->execute([':itemID' => $itemID]);
                        $productID = $pIDStmt->fetchColumn();
                        //get the original price
                        $costQuery = "SELECT price FROM products WHERE product_ID = :productID";
                        $costStmt = $dbConn->prepare($costQuery);
                        $costStmt->execute([':productID' => $productID]);
                        $price = $costStmt->fetchColumn();
                        //get the catagory
                        $categoryQuery = "SELECT category_ID from products where product_ID = :productID";
                        $categoryStmt = $dbConn->prepare($categoryQuery);
                        $categoryStmt->execute([':productID' => $productID]);
                        $category = $categoryStmt->fetchColumn();
                        //calculate any promotions
                        $discount = $price;
                        $promoQuery = "SELECT promotion_amount FROM promotions 
                           WHERE (product_ID = :productID OR category_ID = :category)
                           AND start <= CURDATE() AND end >= CURDATE()";
                        $promoStmt = $dbConn->prepare($promoQuery);
                        $promoStmt->execute([':productID' => $productID, ':category' => $category]);
                        $promotions = $promoStmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($promotions as $promotion) {
                           $promotionAmount = $promotion['promotion_amount'];
                           if (strpos($promotionAmount, '*') !== false) {
                           $multiplier = floatval(str_replace('*', '', $promotionAmount));
                           $discount *= $multiplier;
                           } elseif (strpos($promotionAmount, '-') !== false) {
                               $discount += floatval($promotionAmount);
                           }
                        }
                         //run the actual return querys
                         $updateInventory ="UPDATE inventory set quantity = quantity + 1 where inventory_ID = $productID";
                         $updateTab = "UPDATE customers set credit_line = credit_line - $discount";
                         $updateSale ="UPDATE sales set returned = true where sale_ID = (SELECT sale_ID from order_history 
                         where order_ID = $orderID)";
                         $newReturn ="INSERT into restore(sale_ID, reason) SELECT sale_ID, '$reason' FROM order_history WHERE order_ID = $orderID";
                         $deleteOrder ="DELETE FROM order_history WHERE order_ID = $orderID";

                         $dbConn->query($updateInventory);
                        $dbConn->query($updateTab);
                        $dbConn->query($updateSale);
                        $dbConn->query($newReturn);
                        $dbConn->query($deleteOrder);
                }
            }
        }

    }
    header("Location: returns.php");
    exit();
?>