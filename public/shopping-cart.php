<?php
session_start();
require_once '../dbConnect.php';

// Get the cart ID from the session
$cart_ID = $_SESSION['cart_ID'];

// Fetch cart items and total price
$query = "SELECT ci.cart_item_ID, ci.quantity, p.name, p.price, p.brand, p.image_path
          FROM cart_items ci
          JOIN products p ON ci.product_ID = p.product_ID
          WHERE ci.cart_ID = :cart_ID AND ci.is_sold = 0";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':cart_ID', $cart_ID);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQuery = "SELECT SUM(p.price * ci.quantity) AS total_price
               FROM cart_items ci
               JOIN products p ON ci.product_ID = p.product_ID
               WHERE ci.cart_ID = :cart_ID AND ci.is_sold = 0";
$totalStmt = $dbConn->prepare($totalQuery);
$totalStmt->bindParam(':cart_ID', $cart_ID);
$totalStmt->execute();
$totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalPrice = $totalResult['total_price'] ?? 0;


// Retrieve the updated cart count
$cart_ID = $_SESSION['cart_ID'];
$query = 'SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE cart_ID = :cart_ID AND is_sold = 0';
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':cart_ID', $cart_ID);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$cart_item_count = $result['total_quantity'] ?? 0; // If null, set to 0
$_SESSION['cart_item_count'] = $cart_item_count;
// Update session with latest count
//$_SESSION['cart_item_count'] = $result['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Shopping Cart</title>
    <script
              src="https://www.paypal.com/sdk/js?client-id=AZLdThVgKuNnC6d_NeMKwxmxxTq7sO24R544e63exxMpIDrfAfHWPxRsvCwuEsiP1XCwqxJU6-Biactt&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card"
              data-sdk-integration-source="developer-studio"
      ></script>
</head>
<body class="account-layout">
<?php include 'customerNav.php'; ?>
<div class="cart-container">
    <!-- Cart Items Section -->
    <div class="cart-items">
        <h2>Items in Your Cart</h2>
        <?php if ($cartItems): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                   <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                  <div class = "item-details">
                    <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                    <p>Brand: <?php echo htmlspecialchars($item['brand']); ?></p>
                    <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                    <div class="quantity-controls" data-item-id="<?php echo $item['cart_item_ID']; ?>">
                      <button class="decrease-btn">-</button>
                      <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                      <button class="increase-btn">+</button>
                    </div>
                  </div>
                    <button class="remove-btn" data-item-id="<?php echo $item['cart_item_ID']; ?>">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="JS/website_features.js"></script>
    <!-- Cart Summary Section -->
    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <p><strong>Total Price:</strong> $<?php echo number_format($totalPrice, 2); ?></p>
        <!--<button class="checkout-btn">Proceed to Checkout</button>-->
        <div id="paypal-button-container">
          <script>
          var totalPrice = <?php echo json_encode($totalPrice); ?>;
          console.log("Total Price from PHP:", totalPrice);
          </script>
          <script src="JS/checkout.js"></script>
        </div>
    </div>
</div>

</body>
</html>
