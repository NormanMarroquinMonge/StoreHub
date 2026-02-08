<?php
session_start();
$currentPage = 'bakery';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Shopping Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="JS/add-to-cart.js"></script>
</head>
<body>
    <?php include 'customerNav.php'; ?>
    <div class="main-content">
      <div class="product-container">
          <?php
          $categoryID = new MongoDB\BSON\ObjectId('67fffc438453308d8056674a');
          $productsCursor = $productsCollection->find(['category_id' => $categoryID]);

          foreach ($productsCursor as $product) {
           $product_ID = $product['_id'];
           $name = $product['name'];
           $price = $product['price'];
           $brand = $product['brand'];
           $imagePath = $product['image_path'];

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
         ?>
       </div>
     </div>
</body>
</html>
