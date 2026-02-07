<?php

require_once __DIR__ . '/dbConnect.php';

// Check if the form is submitted
    $name = $_POST['name'];
     $price = (float)$_POST['price'];
    $brand = $_POST['brand'];
    $category_id = $_POST['category_id'];
    $image = $_FILES['image'];

    $quantity = (int)$_POST['quantity'];
    $last_restock = $_POST['last_restock'];

    // Handle image upload
    $uploadDir = '../images/';
  $imagePath = $uploadDir . basename($image['name']);
  if (move_uploaded_file($image['tmp_name'], $imagePath)) {
      // Save the *public* path (without the "../") in the database
      $publicImagePath = 'images/' . basename($image['name']);
  } else {
      $publicImagePath = '';
  }

    $productData = [
        'name' => $name,
        'price' => $price,
        'brand' => $brand,
        'category_id' => new MongoDB\BSON\ObjectId($category_id),
        'image_path' => $publicImagePath
    ];

    try {
        // Insert the product
        $insertProductResult = $productsCollection->insertOne($productData);

        // Get the product_id (ObjectId) from the inserted product
        $product_id = $insertProductResult->getInsertedId();

        $inventoryData = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'last_restock' => $last_restock
        ];

        // Insert the inventory data
        $inventoryCollection->insertOne($inventoryData);

        // Redirect to inventory page or show success message
        echo json_encode([
          'success' => true,
          'name' => $name,
        ]);
        exit();

    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
      ]);
      exit();
    }
 ?>
