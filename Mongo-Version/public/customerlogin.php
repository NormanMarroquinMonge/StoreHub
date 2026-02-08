<?php
session_start();
require_once 'PHP/customer_login_logic.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Customer Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class = "login-layout">
    <div class="login-container">
        <h2>Customer Login</h2>

        <!-- Error message -->
        <div id="error-message">Incorrect first name, last name, or password.</div>

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
