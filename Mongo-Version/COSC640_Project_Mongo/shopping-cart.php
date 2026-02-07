<?php
require_once 'PHP/cart_data_logic.php';
$currentPage = 'shoppingcart';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <script
              src="https://www.paypal.com/sdk/js?client-id=AZLdThVgKuNnC6d_NeMKwxmxxTq7sO24R544e63exxMpIDrfAfHWPxRsvCwuEsiP1XCwqxJU6-Biactt&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card"
              data-sdk-integration-source="developer-studio"
      ></script>
</head>
<body class = "shopping-cart-page">
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

        <div id="paypal-button-container">
          <script>
          var totalPrice = <?php echo json_encode($totalPrice); ?>;
          console.log("Total Price from PHP:", totalPrice);
          </script>
          <script src="JS/app.js"></script>
        </div>
    </div>
</div>

</body>
</html>
