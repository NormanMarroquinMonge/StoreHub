<?php
session_start();
$currentPage = 'produce';
// Check if the user is logged in as a customer
if (!isset($_SESSION['customer_loggedIn']) || $_SESSION['customer_loggedIn'] !== true) {
    // Redirect to customer login page
    header("Location: customerlogin.php");
    exit();
}

require_once '../dbConnect.php';

/*****************************************************************
retrieves the number of items in a customers cart.
It updates the count ontop of the cart icon.
is_sold is used to determine the num of items that haven't been bought.

******************************************************************/
$cart_ID = $_SESSION['cart_ID'];
$query = 'SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE cart_ID = :cart_ID AND is_sold = 0';
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':cart_ID', $cart_ID);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$cart_item_count = $result['total_quantity'] ?? 0; // If null, set to 0
$_SESSION['cart_item_count'] = $cart_item_count;
/****************************************************************/
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Shopping Page</title>
</head>
<body>
    <?php include 'customerNav.php'; ?>
    <div class="main-content">
      <div class="product-container">
          <?php
          /*******************************************************
          retreives all the items based on its associated category_ID
          While loop iterates through entire list to create containers
          for each product.
          *******************************************************/
          $query = 'SELECT * FROM products WHERE category_ID = 4';
          $stmt = $dbConn->prepare($query);
          $stmt->execute();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $product_ID = $row['product_ID'];
              $name = $row['name'];
              $price = $row['price'];
              $brand = $row['brand'];
              $price = $row['price'];
              $imagePath = $row['image_path'];
              echo '<div class="product">';
              echo '<div class="product-image">';
              echo '<img src="' . $imagePath . '" alt="Product Image">';
              echo '</div>';
              echo '<div class="product-content">'; // Flex container
              echo '<div class="product-info">';
              echo '<p>' . $name . '</p>';
              echo '<p>' . $brand . '</p>';
              echo '<p>$' . $price . '</p>';
              echo '</div>';
              echo '<button class="add-to-cart" data-product-id="' . $product_ID . '">Add to Cart</button>';
              echo '</div>';
              echo '</div>';
            }
            /******************************************************/
         ?>
       </div>
     </div>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="JS/cart.js"></script>
</body>
</html>
