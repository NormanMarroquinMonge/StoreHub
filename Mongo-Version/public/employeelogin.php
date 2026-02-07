<?php
require_once 'PHP/employee_login_logic.php';
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Employee Login</title>
    <link rel="stylesheet" href="CSS/styles.css">
  </head>
  <body>
    <div class="center-page">
      <div class="login-container">
          <h2>Employee Login</h2>

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
      </div>
    </div>
  </body>
</html>
