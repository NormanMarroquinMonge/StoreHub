//Redirect based on button clicked on index page
function redirectToPage(userType){
  window.location.href = userType + "login.php";
}

//Add item to cart and update counter.
$(document).ready(function() {
    // Event listener for the "Add to Cart" button
    $(".add-to-cart").click(function() {
        var product_ID = $(this).data("product-id");

        $.ajax({
            type: "POST",
            url: "add-to-cart.php", // The PHP file that handles adding to the cart
            data: { product_ID: product_ID },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    alert("Error: " + response.error);
                } else {
                    // Update the cart item count
                    $(".cart-item-count").text(response.cart_item_count);
                    alert("Item added to cart");
                }
            },
            error: function() {
                alert("An error occurred while adding the item to the cart.");
            }
        });
    });
});
