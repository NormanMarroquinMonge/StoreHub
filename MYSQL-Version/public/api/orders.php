<?php
// This code assumes that you already have PayPal's SDK installed (composer install)
require 'vendor/autoload.php';  // Path to Composer's autoload.php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Details;
use PayPal\Api\Payer;

// Set up the PayPal API context
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        'YOUR_CLIENT_ID', // PayPal Client ID
        'YOUR_CLIENT_SECRET' // PayPal Client Secret
    )
);

// Get the payment ID from the frontend (this will be returned after the user approves the payment)
$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];

// Execute the payment
$payment = Payment::get($paymentId, $apiContext);

$execution = new PaymentExecution();
$execution->setPayerId($payerId);

// Capture the payment
$payment->execute($execution, $apiContext);

// Success response
echo "Payment was successful!";
