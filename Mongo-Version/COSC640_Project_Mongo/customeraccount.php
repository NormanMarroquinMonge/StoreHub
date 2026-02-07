<?php
require_once 'PHP/customer_account_logic.php';
$currentPage = 'customeraccount';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Account</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body class="customer-account-page">
  <?php include 'customerNav.php'; ?>

<div class="account-container">
    <div class="account-header">
        <h1>Welcome, <?php echo htmlspecialchars($customer['Fname']) . ' ' . htmlspecialchars($customer['Lname']); ?></h1>
    </div>

    <!-- Account Information Section -->
    <div class="account-info">
        <h2>Account Information</h2>
        <form method="POST" action="PHP/customer_account_logic.php">
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
