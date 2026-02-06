<?php
    session_start(); // Start the session at the beginning of each protected page
    include 'dbConnect.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $number = $_POST['cart_ID'];

        $cartID = filter_var($number, FILTER_VALIDATE_INT);
        if ($cartID !== false) {
            $query = "SELECT cart_item_ID, product_ID FROM cart_items WHERE cart_ID = :cartID";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':cartID', $cartID, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($cartItems)) {

                    //checkout proccess has 6 queries this is the prep for them and then the actual queries
                    foreach ($cartItems as $item) {
                        $itemID = intval($item); 
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
                        // get the cart_ID
                        $cartIDQuery = "SELECT cart_ID FROM cart_items WHERE cart_item_ID = :itemID";
                        $cartIDStmt = $dbConn->prepare($cartIDQuery);
                        $cartIDStmt->execute([':itemID' => $itemID]);
                        $cartID = $cartIDStmt->fetchColumn();
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
                        //write the 6 queries of the actual checkout process
                        $intoSales = "INSERT INTO sales(cart_item_ID,date,returned,reveue)values($itemID,CURDATE(),false,$discount)";
                        $updateSold = "UPDATE cart_items set is_sold = true where cart_item_ID = '$itemID'";
                        $updateInventory ="UPDATE inventory set quantity = quantity - 1 where inventory_ID = $productID";
                        $updateTab = "UPDATE customers set credit_line = credit_line + $discount";
                        $insertOrderHistory = "INSERT into order_history(sale_ID, customer_ID) select (select sale_ID from sales
                            where cart_item_ID = $itemID) as sale_ID, customer_ID from shopping_cart where cart_ID = $cartID";
                        $updateCart = "UPDATE shopping_cart set checked_out= true where cart_ID = (select cart_ID from
                            cart_items where cart_items_ID=$itemID";
                        //run the checkout process
                        $dbConn->query($intoSales);
                        $dbConn->query($updateSold);
                        $dbConn->query($updateInventory);
                        $dbConn->query($updateTab);
                        $dbConn->query($insertOrderHistory);
                        $dbConn->query($updateCart);
                    } 
                }
            } else {
                echo "Error executing query.";
            }
        } else {
            echo "Invalid cart ID.";
        }
    }
    header("Location: checkout.php");
    exit();
?>