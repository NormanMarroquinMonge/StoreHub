<?php
  session_start();
  require_once 'PHP/customer_signup_logic.php';
 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>Customer Signup</title>
      <link rel="stylesheet" href="CSS/styles.css">
   </head>
   <body>
     <div class="center-page">
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
    </div>
   </body>
 </html>
