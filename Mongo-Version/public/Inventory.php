<?php
require_once 'PHP/inventory_logic.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="CSS/employee_style.css">
    <style>
.action-form {
    display: inline;
    margin: 0;
    padding: 0;
    border: none;
}

.action-form button {
    margin: 0 5px 0 0; /* optional: little space between buttons */
}
#messageBox {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000; /* stay above everything */
  max-width: 600px;
  width: 90%;
  padding: 15px;
  border-radius: 8px;
  font-size: 16px;
  text-align: center;
  display: none;
  animation: fadeIn 0.5s;
}
#messageBox.success {
  background-color:  #f8d7da;
  color: #721c24;
  border: 2px solid #36322c;
}
#messageBox.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 2px solid #36322c;
}
.cancel-btn {
    background-color: red;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.cancel-btn:hover {
    background-color: darkred;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
</head>
<body>
  <?php include 'employeeNav.html' ?>
<div class="container">
    <h1>Inventory Management</h1>

<div class="form-container">
  <div class="form-box">
    <!-- Add Item Form -->
    <h2>Add New Item</h2>
    <form id="addItemForm" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required>

        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand">

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="date_added">Date Added:</label>
        <input type="date" id="last_restock" name="last_restock" required>

        <label for="category">Category:</label>
        <select id="category" name="category_id" required>
            <option value="" selected disabled>Choose a category</option>
            <option value="67fffc428453308d80566746">Beverages</option>
            <option value="67fffc438453308d80566747">Snacks</option>
            <option value="67fffc438453308d80566748">Dairy</option>
            <option value="67fffc438453308d80566749">Produce</option>
            <option value="67fffc438453308d8056674a">Bakery</option>
        </select>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <div class="image-preview-container">
           <img id="imagePreview" style="display:none; max-width: 200px; margin-top: 10px;" />
          <script src="JS/employee_features.js"></script>
        </div>

        <button type="submit" name="add_item" style="margin-top: 20px;">Add Item</button>
    </form>
    </div>
<div class="form-box">
    <!-- Delete Item Form -->
    <h2>Delete Item</h2>
    <form id="deleteForm">
      <label for="objectId">Enter Product Object ID:</label><br>
      <input type="text" id="objectId" name="objectId" required>
      <button type="button" onclick="fetchProduct()">Find Product</button>

      <div id="productInfo" style="margin-top: 20px; display: none;">
        <p><strong>Product Name:</strong> <span id="productName"></span></p>
        <p><strong>Price:</strong> $<span id="productPrice"></span></p>
        <p><strong>Brand:</strong> <span id="productBrand"></span></p>
        <button type="button" onclick="deleteProduct()">Delete Product</button>
      </div>
    </form>
    </div>
</div>

<!-- Category Filter -->
<h2>Filter Inventory by Category</h2>
<select id="filterCategory">
<option value="">All Categories</option>
<option value="67fffc428453308d80566746">Beverages</option>
<option value="67fffc438453308d80566747">Snacks</option>
<option value="67fffc438453308d80566748">Dairy</option>
<option value="67fffc438453308d80566749">Produce</option>
<option value="67fffc438453308d8056674a">Bakery</option>
</select>
    <!-- Inventory Table -->
    <h2>Inventory Items</h2>
<table id="inventoryTable">
    <thead>
        <tr>
            <th>Inventory ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Brand</th>
            <th>Quantity</th>
            <th>Last Restock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="inventoryBody">
        <?php foreach ($inventoryItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_info']['_id']) ?></td>
                <td class="editable" data-field="name"><?= htmlspecialchars($item['product_info']['name']) ?></td>
                <td class="editable" data-field="price"><?= htmlspecialchars(number_format($item['product_info']['price'], 2)) ?></td>
                <td class="editable" data-field="brand"><?= htmlspecialchars($item['product_info']['brand']) ?></td>
                <td class="editable" data-field="quantity"><?= htmlspecialchars($item['quantity']) ?></td>
                <td class="editable" data-field="last_restock"><?= htmlspecialchars($item['last_restock']) ?></td>
                <td>
                    <!-- Action Buttons -->
                     <button class="edit-btn" data-id="<?= $item['_id'] ?>">Update</button>

                    <form method="POST" action="PHP/inventory_delete_item.php" class="action-form">
                        <input type="hidden" name="Product_ID" value="<?= $item['product_info']['_id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<script src="JS/inventory_filter.js"></script>
<script src="JS/inventory_update.js"></script>
</body>
</html>
