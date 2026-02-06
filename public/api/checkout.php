<Html>

<head>
<title>Checkout Page</title>
<style>
        /* Basic body styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        /* Carousel styling for logos */
        .logos {
            overflow: hidden;
            padding: 20px 0;
            background: #e0e0e0;
            white-space: nowrap;
            position: relative;
            width: 100%;
        }

        /* Gradient effect at edges */
        .logos:before,
        .logos:after {
            position: absolute;
            top: 0;
            width: 250px;
            height: 100%;
            content: "";
            z-index: 2;
        }

        .logos:before {
            left: 0;
            background: linear-gradient(to left, rgba(255, 255, 255, 0), white);
        }

        .logos:after {
            right: 0;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), white);
        }

        /* Animation effect */
        @keyframes slide {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-100%);
            }
        }

        /* Reverse animation for bottom carousel */
        @keyframes slide-reverse {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        /* Logo images styling */
        .logos-slide {
            display: inline-block;
            animation: 50s slide infinite linear;
        }

        /* Apply the reverse animation to the bottom carousel */
        .logos.bottom .logos-slide {
            animation: 50s slide-reverse infinite linear;
            padding-bottom: 10px;
        }

        .logos-slide img {
            height: 150px;
            width: 300px;
            margin: 0;
        }

        /* Container with text and buttons */
        .container {
            text-align: center;
            font-size: 1.2em;
            color: #555;
            padding: 30px 0;
            width: 100%;
        }

        .message {
            font-size: 2em;
            color: #333;
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 0.5s;
        }

        .login-as {
            opacity: 0;
            animation: fadeIn 1s forwards;
            animation-delay: 0.8s;
            font-size: 1.2em;
            color: #555;
            margin-top: 15px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 1.2s;
            margin-top: 15px;
        }

        .button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 1em;
            cursor: pointer;
            border: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            to { opacity: 1; }
        }
    </style>
</head>

<body>
    <?php
    session_start() // Start the session at the beginning of each protected page
    ?>
<div class="logos">
        <div class="logos-slide">
            <img src="images/store1.jpg" alt="Image 1">
            <img src="images/store2.jpg" alt="Image 2">
            <img src="images/store3.jpg" alt="Image 3">
            <img src="images/store4.jpg" alt="Image 4">
            <img src="images/store5.jpg" alt="Image 5">
        </div>
        <div class="logos-slide">
            <img src="images/store1.jpg" alt="Image 1">
            <img src="images/store2.jpg" alt="Image 2">
            <img src="images/store3.jpg" alt="Image 3">
            <img src="images/store4.jpg" alt="Image 4">
            <img src="images/store5.jpg" alt="Image 5">
        </div>
    </div>
<?php

// Check if the user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true || !in_array($_SESSION['role'], ['manager', 'cashier'])) {
    // Redirect to an error page or the login page
    header("Location: index.php");
    exit();
}
 include "employeeNav.html";
 ?>
    <h1>Checkout Page</h1>
    <h3>Open Carts</h3>
   <?php include 'dbConnect.php';
    $query="SELECT shopping_cart.cart_ID, shopping_cart.date, customers.fname, customers.lname
    FROM shopping_cart JOIN customers ON shopping_cart.customer_ID = customers.customer_ID";

    //this query will show the user (employee) the current carts and the cart's
    //owners name and date to identify it
    $result = $dbConn->query($query);
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "Cart #: " . $row['cart_ID'].",  ";
            echo " " . $row['fname'] ." " . $row['lname'].",   ";
            echo " Opened: " . $row['date'] . "<br>";
        }
    } else {
        echo "There are currently no open carts";
    }
    ?>

    <form id="checkoutForm" method="POST">
        <label for="cart_ID">Cart ID:</label>
        <input type="number" id="cart_ID" name="cart_ID" required>

        <label for="checkoutType">Checkout Mode:</label>
        <select id="checkoutType" name="checkoutType" required>
            <option value="manual">Manual</option>
            <option value="auto">Auto</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <script>
        const form = document.getElementById("checkoutForm");
        const checkoutType = document.getElementById("checkoutType");
        form.addEventListener("submit", function(event) {
            if (checkoutType.value === "manual") {
                form.action = "checkoutManual.php";
            } else if (checkoutType.value === "auto") {
                form.action = "checkoutAuto.php";
            }
        });
    </script>
 <div class="logos bottom">
        <div class="logos-slide">
          <img src="images/store1.jpg" alt="Image 1">
          <img src="images/store2.jpg" alt="Image 2">
          <img src="images/store3.jpg" alt="Image 3">
          <img src="images/store4.jpg" alt="Image 4">
          <img src="images/store5.jpg" alt="Image 5">
        </div>
        <div class="logos-slide">
          <img src="images/store1.jpg" alt="Image 1">
          <img src="images/store2.jpg" alt="Image 2">
          <img src="images/store3.jpg" alt="Image 3">
          <img src="images/store4.jpg" alt="Image 4">
          <img src="images/store5.jpg" alt="Image 5">
        </div>
    </div>

</body>
</HTML>
