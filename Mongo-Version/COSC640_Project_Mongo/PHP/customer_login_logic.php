<?php
require_once __DIR__ . '/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];

    $customer = $customersCollection->findOne([
       'Fname' => $fname,
       'Lname' => $lname
     ]);

    if ($customer) {
        // Check if the password matches
        if (password_verify($password, $customer['password'])) {

          session_start();

          $customer_id = $customer['_id'];

          $cart = $shoppingCartsCollection->findOne([
            'customer_id' => $customer_id
          ]);

          // Might need to change to ObjectId
          $cart_ID = $cart ? (string) $cart['_id'] : null;


          // Store customer session data
          $_SESSION['customer_loggedIn'] = true;
          $_SESSION['cart_ID'] = $cart_ID;
          $_SESSION['fname'] = $fname;
          $_SESSION['lname'] = $lname;
          //might need to change  to ObjectId
          $_SESSION['customer_ID'] = (string) $customer_id;
            // Redirect to shopping page
            header("Location: beverages.php");
            exit();
        } else {
            // Password is wrong
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('error-message').style.display = 'block';
                });
            </script>";
        }
    } else {
        // First/last name combination doesn't exist
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('error-message').style.display = 'block';
            });
        </script>";
    }
}
?>
