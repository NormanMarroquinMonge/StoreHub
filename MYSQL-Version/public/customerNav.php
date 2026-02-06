<?php
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
?>

<nav class="nav-menu">
    <div class="nav-container">
        <a href="beverages.php" class="nav-link <?php echo ($currentPage == 'beverages') ? 'active' : ''; ?>">Beverages</a>
        <a href="snacks.php" class="nav-link <?php echo ($currentPage == 'snacks') ? 'active' : ''; ?>">Snacks</a>
        <a href="dairy.php" class="nav-link <?php echo ($currentPage == 'dairy') ? 'active' : ''; ?>">Dairy</a>
        <a href="produce.php" class="nav-link <?php echo ($currentPage == 'produce') ? 'active' : ''; ?>">Produce</a>
        <a href="bakery.php" class="nav-link <?php echo ($currentPage == 'bakery') ? 'active' : ''; ?>">Bakery</a>
        <a href="customeraccount.php" class="nav-link">Account</a>
        <a href="logout.php" class="nav-link">Log Out</a>

        <a href="shopping-cart.php" class="nav-link">
            <img src="images/hd-shopping-cart.png" alt="Shopping Cart" class="cart-icon" style="background: transparent;">
            <span class="cart-item-count"><?php echo isset($_SESSION['cart_item_count']) ? $_SESSION['cart_item_count'] : 0; ?></span>
        </a>
    </div>
</nav>
