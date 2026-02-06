<html>
<?php
    session_start(); // Start the session at the beginning of each protected page
    ?>
<form id="autoSubmitForm" action="checkout.php" method="POST">
    <input type="hidden" name="cartID" value="<?php echo $cartID; ?>" />
</form>
<?php
include '../dbConnect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item = $_POST['userInput'];
    $itemID = intval($item);
    $pIDQuery = "SELECT product_ID FROM cart_items WHERE cart_item_ID = :itemID";
    $pIDStmt = $dbConn->prepare($pIDQuery);
    $pIDStmt->execute([':itemID' => $itemID]);
    $productID = $pIDStmt->fetchColumn();

    $costQuery = "SELECT price FROM products WHERE product_ID = :productID";
    $costStmt = $dbConn->prepare($costQuery);
    $costStmt->execute([':productID' => $productID]);
    $price = $costStmt->fetchColumn();

    $cartIDQuery = "SELECT cart_ID FROM cart_items WHERE cart_item_ID = :itemID";
    $cartIDStmt = $dbConn->prepare($cartIDQuery);
    $cartIDStmt->execute([':itemID' => $itemID]);
    $cartID = $cartIDStmt->fetchColumn();

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
    $intoSales = "INSERT INTO sales(cart_item_ID,date,returned,reveue)values($itemID,CURDATE(),false,$discount)";
    $updateSold = "UPDATE cart_items set is_sold = true where cart_item_ID = '$itemID'";
    $updateInventory ="UPDATE inventory set quantity = quantity - 1 where inventory_ID = $productID";
    $updateTab = "UPDATE customers set credit_line = credit_line + $discount";
    $insertOrderHistory = "INSERT into order_history(sale_ID, customer_ID) select (select sale_ID from sales
        where cart_item_ID = $itemID) as sale_ID, customer_ID from shopping_cart where cart_ID = $cartID";

    $dbConn->query($intoSales);
    $dbConn->query($updateSold);
    $dbConn->query($updateInventory);
    $dbConn->query($updateTab);
    $dbConn->query($insertOrderHistory);
}

header("Location: checkoutManual.php ");
exit();

?>
</html>
