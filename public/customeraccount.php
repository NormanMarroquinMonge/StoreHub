<?php
session_start();
require_once '../dbConnect.php';

// Assuming customer ID is stored in session after login
$customer_ID = $_SESSION['customer_ID'];

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

// Fetch customer details
$query = "SELECT * FROM customers WHERE customer_ID = :customer_ID";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':customer_ID', $customer_ID);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch or create a shopping cart for the customer
$cartQuery = "SELECT cart_ID FROM shopping_cart WHERE customer_ID = :customer_ID";
$cartStmt = $dbConn->prepare($cartQuery);
$cartStmt->bindParam(':customer_ID', $customer_ID);
$cartStmt->execute();
$cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {

    $checked_out = 0;
    $currentDate = date('Y-m-d');
    // If no cart exists, create one
    $createCartQuery = "INSERT INTO shopping_cart (customer_ID, date, checked_out) VALUES (:customer_ID, :currentDate, :checked_out)";
    $createCartStmt = $dbConn->prepare($createCartQuery);
    $createCartStmt->bindParam(':customer_ID', $customer_ID);
    $createCartStmt->bindParam(':currentDate', $currentDate);
    $createCartStmt->bindParam(':checked_out', $checked_out);
    $createCartStmt->execute();

    // Fetch the newly created cart ID
    $cart_ID = $dbConn->lastInsertId();
} else {
    $cart_ID = $cart['cart_ID'];
}

// Handle account update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];

    // Get the selected credit line amount to add
    $add_credit = $_POST['credit_line_add'];

    // If no amount is selected, set it to 0
    if ($add_credit) {
        $new_credit_line = $customer['credit_line'] + (float)$add_credit; // Add selected amount to current credit line
    } else {
        $new_credit_line = $customer['credit_line']; // Keep the same credit line if no change is made
    }

    // Update customer details
    $updateQuery = "UPDATE customers SET Fname = :Fname, Lname = :Lname, credit_line = :credit_line WHERE customer_ID = :customer_ID";
    $updateStmt = $dbConn->prepare($updateQuery);
    $updateStmt->bindParam(':Fname', $Fname);
    $updateStmt->bindParam(':Lname', $Lname);
    $updateStmt->bindParam(':credit_line', $new_credit_line);
    $updateStmt->bindParam(':customer_ID', $customer_ID);
    $updateStmt->execute();

    // Refresh page to reflect changes
    header("Location: customeraccount.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Account</title>
    <style>
  body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      margin: 0;
      padding: 50px 0 0;
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

      .account-container {
          display: flex;
          flex-direction: column;
          width: 80%;
          max-width: 1200px;
          margin: 100px auto;
          padding: 20px;
          background-color: #fff;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      .account-header {
          text-align: center;
          margin-bottom: 20px;
      }

      .account-header h1 {
          margin: 0;
          font-size: 24px;
      }

      .account-info,
      .cart-info {
          margin-bottom: 30px;
      }

      .account-info h2,
      .cart-info h2 {
          font-size: 20px;
          margin-bottom: 10px;
      }

      .account-info p,
      .cart-info p {
          font-size: 16px;
          margin: 5px 0;
      }

      .form-group {
          margin-bottom: 15px;
      }

      input[type="text"], input[type="number"] {
          width: 100%;
          padding: 10px;
          margin-top: 5px;
          border: 1px solid #ddd;
          border-radius: 4px;
      }

      .update-btn {
          background-color: #007bff;
          color: white;
          padding: 10px 20px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
      }

      .update-btn:hover {
          background-color: #0056b3;
      }

      .cart-btn {
          background-color: #007bff;
          color: white;
          padding: 10px 20px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
      }

      .cart-btn:hover {
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
  </style>
</head>
<body>
  <?php include 'customerNav.php'; ?>

<div class="account-container">
    <div class="account-header">
        <h1>Welcome, <?php echo htmlspecialchars($customer['Fname']) . ' ' . htmlspecialchars($customer['Lname']); ?></h1>
    </div>

    <!-- Account Information Section -->
    <div class="account-info">
        <h2>Account Information</h2>
        <form method="POST" action="customeraccount.php">
            <div class="form-group">
                <label for="Fname">First Name</label>
                <input type="text" name="Fname" id="Fname" value="<?php echo htmlspecialchars($customer['Fname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="Lname">Last Name</label>
                <input type="text" name="Lname" id="Lname" value="<?php echo htmlspecialchars($customer['Lname']); ?>" required>
            </div>

            <!-- Credit Line Selection -->
            <div class="form-group">
                <label for="credit_line_add">Add to Credit Line</label>
                <select name="credit_line_add" id="credit_line_add">
                    <option value="0">No Change</option>
                    <option value="5">Add $5</option>
                    <option value="10">Add $10</option>
                    <option value="20">Add $20</option>
                </select>
            </div>

            <button type="submit" name="update_account" class="update-btn">Update Account</button>
        </form>
    </div>

    <!-- Cart Information Section -->
    <div class="cart-info">
        <h2>Your Shopping Cart</h2>
        <p><strong>Cart ID:</strong> <?php echo htmlspecialchars($cart_ID); ?></p>
        <form action="shopping-cart.php">
            <button type="submit" class="cart-btn">Go to Cart</button>
        </form>
    </div>

</div>

</body>
</html>
