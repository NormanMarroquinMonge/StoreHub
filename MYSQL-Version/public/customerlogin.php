<?php session_start();
require_once '../dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];

    $query = 'SELECT  customer_ID, password FROM customers WHERE Fname = :fname AND Lname = :lname';
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Check if the password matches
        if (password_verify($password, $result['password'])) {

          $customer_ID = $result['customer_ID'];
          $query = 'SELECT cart_ID FROM shopping_cart WHERE customer_ID = :customer_ID';
          $stmt = $dbConn->prepare($query);
          $stmt->bindParam(':customer_ID', $customer_ID);
          $stmt->execute();
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $cart_ID = $result['cart_ID'];
                // Store customer session data
          $_SESSION['customer_loggedIn'] = true;
          $_SESSION['cart_ID'] = $cart_ID;
          $_SESSION['fname'] = $fname;
          $_SESSION['lname'] = $lname;
          $_SESSION['customer_ID'] = $customer_ID;

            // Redirect to shopping page
            header("Location: beverages.php");
            exit();
        } else {
            // Password is wrong
            $loginError = true;
        }
    } else {
        // First/last name combination doesn't exist
            $loginError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Customer Login</title>
</head>
<body class = "login-layout">
    <div class="login-container">
        <h2>Customer Login</h2>

        <!-- Error message -->
        <?php if (isset($loginError) && $loginError): ?>
        <div id="error-message" style="display: block;">
            Incorrect first name, last name, or password.
        </div>
        <?php endif; ?>

        <form method="POST"> <!-- Adjust action based on PHP backend -->
            <div>
                <input type="text" name="fname" placeholder="Enter first name" class="form-input" required>
            </div>
            <div>
                <input type="text" name="lname" placeholder="Enter last name" class="form-input" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Enter Password" class="form-input" required>
            </div>
            <div>
                <button type="submit" class="login-button">Login</button>
            </div>
        </form>

        <!-- Sign up section -->
        <div class="signup-container">
            <span class="signup-text">Not a customer yet?</span>
            <a href="customersignup.php">
                <button class="signup-button">Sign Up</button>
            </a>
        </div>
    </div>
</body>
</html>
