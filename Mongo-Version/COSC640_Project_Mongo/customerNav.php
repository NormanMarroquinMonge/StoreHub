<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/PHP/dbConnect.php';
use MongoDB\BSON\ObjectId;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['cart_item_count'] = 0;

if (isset($_SESSION['cart_ID'])) {
    try {
        $cartItemCountResult = $cartItemsCollection->aggregate([
            ['$match' => ['cart_id' => new ObjectId($_SESSION['cart_ID']), 'is_sold' => 0]],
            ['$group' => ['_id' => '$cart_id', 'total_quantity' => ['$sum' => '$quantity']]],
            ['$project' => ['_id' => 0, 'total_quantity' => 1]]
        ])->toArray();

        $_SESSION['cart_item_count'] = isset($cartItemCountResult[0]) ? $cartItemCountResult[0]['total_quantity'] : 0;
    } catch (Exception $e) {
        $_SESSION['cart_item_count'] = 0;
    }
}

$currentPage = basename($_SERVER['PHP_SELF'], ".php");
?>

<nav class="nav-menu">
    <div class="nav-container">
        <a href="beverages.php" class="nav-link <?php echo ($currentPage == 'beverages') ? 'active' : ''; ?>">Beverages</a>
        <a href="snacks.php" class="nav-link <?php echo ($currentPage == 'snacks') ? 'active' : ''; ?>">Snacks</a>
        <a href="dairy.php" class="nav-link <?php echo ($currentPage == 'dairy') ? 'active' : ''; ?>">Dairy</a>
        <a href="produce.php" class="nav-link <?php echo ($currentPage == 'produce') ? 'active' : ''; ?>">Produce</a>
        <a href="bakery.php" class="nav-link <?php echo ($currentPage == 'bakery') ? 'active' : ''; ?>">Bakery</a>
        <a href="customeraccount.php" class="nav-link <?php echo ($currentPage == 'customeraccount') ? 'active' : ''; ?>" >Account</a>
        <a href="logout.php" class="nav-link">Log Out</a>

        <a href="shopping-cart.php" class="nav-link <?php echo ($currentPage == 'shoppingcart') ? 'active' : ''; ?>">
            <img src="images/hd-shopping-cart.png" alt="Shopping Cart" class="cart-icon" style="background: transparent;">
            <span class="cart-item-count"><?php echo isset($_SESSION['cart_item_count']) ? $_SESSION['cart_item_count'] : 0; ?></span>
        </a>
    </div>
</nav>
