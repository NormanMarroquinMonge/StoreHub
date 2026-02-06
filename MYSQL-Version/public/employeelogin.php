<?php
session_start();
require_once '../dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $password = $_POST['password'];

  $query = 'SELECT password, role, employee_ID FROM employees WHERE Fname = :fname AND Lname = :lname';
  $stmt = $dbConn->prepare($query);
  $stmt->bindParam(':fname', $fname);
  $stmt->bindParam(':lname', $lname);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result) {
      // First/last name exists, now check password
      if (password_verify($password, $result['password'])){
          // Successful login
          $_SESSION['loggedIn'] = true;
          $_SESSION['role'] = $result['role'];
          $_SESSION['fname'] = $fname;
          $_SESSION['lname'] = $lname;
          $_SESSION['employee_ID'] = $result['employee_ID'];

          // Redirect based on role
          if ($result['role'] == 'manager') {
              header("Location: Management.php");
          } else {
              header("Location: checkoutDbAccess.php");
          }
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
    <title>Employee Login</title>
</head>
<body class = "login-layout">
    <div class="login-container">
        <h2>Employee Login</h2>

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
    </div>
</body>
</html>
