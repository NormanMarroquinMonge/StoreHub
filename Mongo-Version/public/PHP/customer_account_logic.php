<?php
session_start();
require_once __DIR__ . '/dbConnect.php';

if (!isset($_SESSION['customer_ID'])) {
    die("Error: Customer not logged in. Session ID is missing.");
}

$customer_ID = $_SESSION['customer_ID'];
$customer = $customersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($customer_ID)]);

// First handle POST updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {

    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];

    $add_credit = $_POST['credit_line_add'];

    // Only update credit line if a non-zero value is selected
   if ($add_credit != "0") {
       $new_credit_line = $customer['credit_line'] + (float)$add_credit;
   } else {
       $new_credit_line = $customer['credit_line'];  // No change to credit line
   }

    // Update customer details
    $customersCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($customer_ID)],
        ['$set' => [
            'Fname' => $Fname,
            'Lname' => $Lname,
            'credit_line' => $new_credit_line
        ]]
    );

    // Redirect after update to avoid form resubmission
    header("Location: ../customeraccount.php");
    exit();
}

// Then fetch the customer data for display
$customer = $customersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($customer_ID)]);

// Also fetch cart
$cart = $shoppingCartsCollection->findOne(['customer_id' => new MongoDB\BSON\ObjectId($customer_ID)]);
$cart_ID = (string)$cart['_id'];
?>
