<?php
session_start();
require_once 'dbConnect.php';

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
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 50px 0 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Navigation menu styles */
        .nav-menu {
            background-color: #007bff;
            width: 100%;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed; /* Keeps the navigation bar fixed */
            top: 0; /* Positions it at the top */
            left: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-weight: bold;
        }

        .nav-link:hover {
            background-color: #0056b3;
        }

        .nav-link.active {
          background-color: #0056b3;
        }

        .cart-icon {
          width: 50px;  /* Set the width of the icon */
          height: auto; /* Keep aspect ratio */
          display: inline-block;
          position: relative; /* Allows positioning of the count badge */
        }

        .cart-item-count {
          position: absolute;
          top: 10px;  /* Adjust this to position the number */
          right: 200px; /* Adjust this to position the number */
          background-color: red;  /* Background color of the badge */
          color: white; /* Text color */
          border-radius: 50%;
          width: 20px;  /* Width of the badge */
          height: 20px; /* Height of the badge */
          display: flex;
          justify-content: center;
          align-items: center;
          font-size: 12px;  /* Adjust font size */
          font-weight: bold; /* Optional, to make the number bold */
        }

        .cart-container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
          display: flex;
          justify-content: flex-start;
          align-items: center;
          padding: 10px 0;
          border-bottom: 1px solid #ddd;
          gap: 15px; /* adds spacing between image and details */
        }

        .cart-item-image {
          width: 100px;
          height: auto;
          object-fit: contain;
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .item-details {
          display: flex;
          flex-direction: column;
          flex-grow: 1;
          gap: 4px; /* spacing between name, brand, price, etc. */
        }

        .quantity-controls {
          display: flex;
          align-items: center;
          gap: 6px; /* spacing between buttons and quantity */
          margin-top: 5px; /* reduce if there's too much vertical gap */
        }
        .cart-items {
            width: 65%;
            max-height: 400px;
            overflow-y: auto;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
    
        .cart-item p {
            margin: 0;
            font-size: 16px;
        }
        .remove-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .remove-btn:hover {
            background-color: darkred;
        }
        .cart-summary {
            width: 35%;
            padding: 20px;
            text-align: center;
        }
        .cart-summary h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .cart-summary p {
            font-size: 18px;
            margin: 10px 0;
        }
        .checkout-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .checkout-btn:hover {
            background-color: #45a049;
        }
        #payment-success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3); /* Semi-transparent */
            backdrop-filter: blur(5px); /* Blurs the background */
            z-index: 1000;
        }

        /* Success card */
        #payment-success-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 1001;
            width: 300px;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #09a0e0;
            animation: fadeIn 0.5s ease-in-out;
        }

        .quantity-controls {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-top: 8px;
        }

        .quantity-controls button {
          background-color: #007bff;
          color: white;
          border: none;
          width: 30px;
          height: 30px;
          border-radius: 4px;
          font-size: 18px;
      cursor: pointer;
    }

    .quantity-controls button:hover {
      background-color: #0056b3;
    }

    .quantity-controls .item-quantity {
      font-weight: bold;
      font-size: 16px;
    }
  .cart-item-image {
    width: 100px;
    height: auto;
    object-fit: contain;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -55%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
    </style>
    <script
              src="https://www.paypal.com/sdk/js?client-id=AZLdThVgKuNnC6d_NeMKwxmxxTq7sO24R544e63exxMpIDrfAfHWPxRsvCwuEsiP1XCwqxJU6-Biactt&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card"
              data-sdk-integration-source="developer-studio"
      ></script>
</head>
<body>
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
          <script src="JS/app.js"></script>
        </div>
    </div>
</div>

</body>
</html>
