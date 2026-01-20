<?php
session_start();
$currentPage = 'bakery';
// Check if the user is logged in as a customer
if (!isset($_SESSION['customer_loggedIn']) || $_SESSION['customer_loggedIn'] !== true) {
    // Redirect to customer login page
    header("Location: customer_login.php");
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
    <title>Shopping Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
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
        /* Main content container */
        .main-content {
            max-width: 1200px;
            margin: 80px auto 20px auto;
            padding: 20px;
        }

        .product-container {
          display: flex;
          flex-wrap: wrap;
          gap: 15px;
        }

        .product {
          width: calc(25% - 10px);
          background-color: #e6e6e6;
          border-radius: 10px;
          padding: 15px;
          margin-bottom: 40px;
          box-sizing: border-box;
          margin: 50px;
        }

        .product-image {
          width: 100%;
          height: 250px;
          border-radius: 10px;
          overflow: hidden;
        }

        .product-image img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          border-radius: 10px;
        }

        .product-content {
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        .product-info {
          text-align: center;
          margin-top: 10px;
        }

        .product-info {
          text-align: center;
          margin-top: 10px;
          margin-left: 8px;
        }

        .add-to-cart {
          background-color: #007bff;
          color: white;
          border: none;
          padding: 8px 12px;
          border-radius: 5px;
          cursor: pointer;
          margin-right: 20px; /* Space between button and product-info */
          font-size: 1em;
        }

        .add-to-cart:hover {
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

        /* Responsive design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 10px;
            }

            .nav-link {
                display: block;
                text-align: center;
                width: 200px;
            }
        }
    </style>
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
          $query = 'SELECT * FROM products WHERE category_ID = 5';
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

     <script>
$(document).ready(function() {
    // Event listener for the "Add to Cart" button
    $(".add-to-cart").click(function() {
        var product_ID = $(this).data("product-id");

        $.ajax({
            type: "POST",
            url: "add-to-cart.php", // The PHP file that handles adding to the cart
            data: { product_ID: product_ID },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    alert("Error: " + response.error);
                } else {
                    // Update the cart item count
                    $(".cart-item-count").text(response.cart_item_count);
                    alert("Item added to cart");
                }
            },
            error: function() {
                alert("An error occurred while adding the item to the cart.");
            }
        });
    });
});
</script>
</body>
</html>
