<?php
session_start();
if (!isset($_SESSION['employee_ID'])) {
    header("Location: employeelogin.php");
    exit();
}
// Database connection settings
$host = '100.15.171.64';
$dbname = 'storeHub';
$username = 'tim';
$password = '13';


// Establish the database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add or update item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $inventory_ID = $_POST['inventory_ID'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $brand = $_POST['brand'];
    $quantity = $_POST['quantity'];
    $category_ID = $_POST['category_id'];
    $last_restock = $_POST['last_restock'];


    //Checks to see if an image of the product has been uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get the image details
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        // Define the folder where you want to store images
        $uploadDir = 'images/';
        $imagePath = $uploadDir . basename($imageName);

        // Move the uploaded image to the images folder
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // If successful, insert the product into the database, including the image path
            $product_sql = "INSERT INTO products (product_ID, name, price, brand, category_ID, image_path)
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                            name = VALUES(name),
                            price = VALUES(price),
                            brand = VALUES(brand),
                            category_ID = VALUES(category_ID),
                            image_path = VALUES(image_path)";
            $stmt_product = $conn->prepare($product_sql);

            if (!$stmt_product) {
                die("Error preparing product statement: " . $conn->error);
            }
              $stmt_product->bind_param("isdsis", $inventory_ID, $name, $price, $brand, $category_ID, $imagePath);

              if (!$stmt_product->execute()) {
                  die("Error inserting into products: " . $stmt_product->error);
              }

            echo "Product added successfully!";
              $stmt_product->close();
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "No image selected or there was an error uploading.";
    }//End of image file check


    // Insert or update inventory
    $inventory_sql = "INSERT INTO inventory (inventory_ID, quantity, last_restock)
                      VALUES (?, ?, ?)
                      ON DUPLICATE KEY UPDATE
                      quantity = VALUES(quantity),
                      last_restock = VALUES(last_restock)";
    $stmt_inventory = $conn->prepare($inventory_sql);

    if (!$stmt_inventory) {
        die("Error preparing inventory statement: " . $conn->error);
    }

    $stmt_inventory->bind_param("iis", $inventory_ID, $quantity, $last_restock);

    if (!$stmt_inventory->execute()) {
        die("Error inserting into inventory: " . $stmt_inventory->error);
    }

    echo "Item added/updated successfully!";
    $stmt_inventory->close();
}

// Update item in inventory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $inventory_ID = $_POST['inventory_ID'];
    $quantity = $_POST['quantity'];
    $last_restock = $_POST['last_restock'];

    $update_sql = "UPDATE inventory SET quantity = ?, last_restock = ? WHERE inventory_ID = ?";
    $stmt = $conn->prepare($update_sql);

    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    }

    $stmt->bind_param("isi", $quantity, $last_restock, $inventory_ID);

    if ($stmt->execute()) {
        echo "Item updated successfully!";
    } else {
        echo "Error updating item: " . $stmt->error;
    }

    $stmt->close();
}

// Delete item from inventory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $inventory_ID = $_POST['inventory_ID'];

    $delete_inventory_sql = "DELETE FROM inventory WHERE inventory_ID = ?";
    $stmt_inventory = $conn->prepare($delete_inventory_sql);

    if (!$stmt_inventory) {
        die("Error preparing delete statement: " . $conn->error);
    }

    $stmt_inventory->bind_param("i", $inventory_ID);

    if ($stmt_inventory->execute()) {
        echo "Item deleted successfully!";
    } else {
        echo "Error deleting item: " . $stmt_inventory->error;
    }

    $stmt_inventory->close();
}

// Fetch inventory items with product details
$sql = "SELECT i.inventory_ID, p.name, p.price, p.brand, i.quantity, i.last_restock
        FROM inventory i
        JOIN products p ON i.inventory_ID = p.product_ID";
$result = $conn->query($sql);

if ($result === false) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Inventory Management</h1>

    <!-- Add Item Form -->
    <h2>Add or Update Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="inventory_ID">Inventory ID:</label>
        <input type="number" id="inventory_ID" name="inventory_ID" required><br>

        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br>

        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand"><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>

        <label for="last_restock">Last Restock Date:</label>
        <input type="date" id="last_restock" name="last_restock" required><br>

        <!-- New Category Dropdown -->
        <label for="category">Category:</label>
          <select id="category" name="category_id" required>
            <option value="1">Beverages</option>
            <option value="2">Snacks</option>
            <option value="3">Dairy</option>
            <option value="4">Produce</option>
            <option value="5">Bakery</option>
          </select><br>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br>

        <button type="submit" name="add_item">Add or Update Item</button>
    </form>

    <!-- Update Item Form -->
    <h2>Update Inventory</h2>
    <form method="POST">
        <label for="inventory_ID">Inventory ID:</label>
        <input type="number" id="inventory_ID" name="inventory_ID" required><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>

        <label for="last_restock">Last Restock Date:</label>
        <input type="date" id="last_restock" name="last_restock" required><br>

        <button type="submit" name="update_item">Update Item</button>
    </form>

    <!-- Delete Item Form -->
    <h2>Delete Item</h2>
    <form method="POST">
        <label for="inventory_ID">Inventory ID:</label>
        <input type="number" id="inventory_ID" name="inventory_ID" required><br>

        <button type="submit" name="delete_item">Delete Item</button>
    </form>

    <!-- Inventory Table -->
    <h2>Inventory Items</h2>
    <table>
        <thead>
            <tr>
                <th>Inventory ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Brand</th>
                <th>Quantity</th>
                <th>Last Restock</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['inventory_ID']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?></td>
                        <td><?= htmlspecialchars($row['brand']) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                        <td><?= htmlspecialchars($row['last_restock']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php $conn->close(); // Close the connection ?>
</body>
</html>
