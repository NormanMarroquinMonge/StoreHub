<?php
  require_once __DIR__ . '/dbConnect.php';


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    //Automatically gives 100 in store credit.
    $credit_line = 100.00;


    $customerInsertResult = $customersCollection->insertOne([
        'Fname' => $fname,
        'Lname' => $lname,
        'credit_line' => $credit_line,
        'password' => $hashedPassword
    ]);

      if ($customerInsertResult->getInsertedCount() > 0) {

        //retrieve the customer_ID to create cart.
        $customer_ID = $customerInsertResult->getInsertedId();

        $timezone = new DateTimeZone("America/New_York");
        $date = new DateTime("now", $timezone);
        $date = $date->format("Y-m-d H:i:s");
        $checked_out = false;

        $cartInsertResult = $shoppingCartsCollection->insertOne([
          'customer_id' => $customer_ID,
          'date' => $date,
          'checked_out' => $checked_out
        ]);

        if ($cartInsertResult->getInsertedCount() > 0) {
              $cart_ID = $cartInsertResult->getInsertedId();
              // Set session variables
              $_SESSION['customer_loggedIn'] = true;
              $_SESSION['customer_ID'] = (string) $customer_ID;
              $_SESSION['cart_ID'] = (string) $cart_ID;
              $_SESSION['fname'] = $fname;
              $_SESSION['lname'] = $lname;

              header("Location: beverages.php");
              exit();
      } else {
        echo "There was an error signing up.";
      }
  }
}
 ?>
