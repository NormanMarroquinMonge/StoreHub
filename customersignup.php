<?php
  session_start();
  require_once 'dbConnect.php';


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    //Automatically gives 100 in store credit.
    $credit_line = 100.00;

      $query = 'INSERT INTO customers (Fname, Lname, credit_line, password)
      VALUES (:fname, :lname, :credit_line, :password)';

      // Prepare and bind parameters
      $stmt = $dbConn->prepare($query);
      $stmt->bindParam(':fname', $fname);
      $stmt->bindParam(':lname', $lname);
      $stmt->bindParam(':credit_line', $credit_line);
      $stmt->bindParam(':password', $hashedPassword);

      // Execute the statement
      if ($stmt->execute()) {

        //retrieve the customer_ID to create cart.
        $customer_ID = $dbConn->lastInsertId();

        $date = date("Y-m-d");
        $checked_out = 0;


        $query = 'INSERT INTO shopping_cart (customer_ID, date, checked_out)
        VALUES (:customer_ID, :date, :checked_out)';
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':customer_ID', $customer_ID);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':checked_out', $checked_out);
        $stmt->execute();

        //retrieve the cart_ID
        $cart_ID = $dbConn->lastInsertId();

        $_SESSION['customer_loggedIn'] = true;
        $_SESSION['customer_ID'] = $customer_ID;
        $_SESSION['cart_ID'] = $cart_ID;
        $_SESSION['fname'] = $fname;
        $_SESSION['lname'] = $lname;
        header("Location: beverages.php");
      } else {
        echo "There was an error signing up.";
      }
  }
 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>Customer Signup</title>
     <style>

     /* General body styles */
     body {
         font-family: Arial, sans-serif;
         background-color: #f0f0f0; /* Same as index page color */
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         margin: 0;
     }

     /* Centered rectangle */
     .login-container {
         background-color: #e6e6e6;
         width: 400px;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         display: grid;
         grid-template-rows: auto 1fr auto;
     }

     /* Title at the top */
     .login-container h2 {
         text-align: center;
         margin: 0;
         padding-bottom: 15px;
     }

     /* Form styling */
     .form-input {
         margin-bottom: 20px;
         width: 95%;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 5px;
     }

     .form-input:focus {
         border-color: #007bff;
         outline: none;
     }

     /* Login button style (same as index page) */
     .login-button {
         background-color: #007bff;
         color: white;
         border: none;
         padding: 10px;
         border-radius: 5px;
         width: 100%;
         cursor: pointer;
         transition: background-color 0.3s;
     }

     .login-button:hover {
         background-color: #0056b3;
     }
    </style>
   </head>
   <body>
     <div class="login-container">
         <h2>Customer Signup</h2>

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
                 <button type="submit" class="login-button">Sign in</button>
             </div>
         </form>
     </div>
   </body>
 </html>
