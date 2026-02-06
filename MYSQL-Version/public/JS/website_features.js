document.addEventListener("DOMContentLoaded", function () {
    // Select all remove buttons
    document.querySelectorAll(".remove-btn").forEach(button => {
        button.addEventListener("click", function () {
            let cartItemId = this.getAttribute("data-item-id");

            fetch("/COSC640 Project/self-checkout/remove_item.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ remove_item_id: cartItemId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the item from the UI
                    this.closest(".cart-item").remove();

                    // Update cart count in the icon
                    document.querySelector(".cart-item-count").textContent = data.new_cart_count;

                    // Fetch the updated total price
                    fetch("/COSC640 Project/self-checkout/updateprice.php")
                    .then(response => response.json())
                    .then(updatedData => {
                      console.log("Updated data from updateprice.php:", updatedData);

                       let newTotalPrice = parseFloat(updatedData.total_price) || 0;
                        document.querySelector(".cart-summary p").innerHTML =
                            "<strong>Total Price:</strong> $" + updatedData.total_price.toFixed(2);

                        // Update PayPal amount dynamically
                        totalPrice = updatedData.total_price; // Update global JS variable
                        console.log("Updated total:", totalPrice);
                    })  // <-- Missing closing parenthesis was added here
                    .catch(error => console.error("Error updating price:", error));

                } else {
                    console.error("Error removing item:", data.message);
                  }
              })
              .catch(error => console.error("Fetch error:", error));
          });
      });

      document.querySelectorAll(".quantity-controls").forEach(control => {
      const itemId = control.getAttribute("data-item-id");

      control.querySelector(".increase-btn").addEventListener("click", () => {
          updateQuantity(itemId, "increase", control);
      });

      control.querySelector(".decrease-btn").addEventListener("click", () => {
          updateQuantity(itemId, "decrease", control);
      });
  });

  function updateQuantity(itemId, action, controlElement) {
      fetch("/COSC640 Project/self-checkout/update_quantity.php", {
          method: "POST",
          headers: {
              "Content-Type": "application/json"
          },
          body: JSON.stringify({
              cart_item_id: itemId,
              action: action
          })
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              // Update the quantity on the page
              controlElement.querySelector(".item-quantity").textContent = data.new_quantity;

              // Recalculate total
              fetch("/COSC640 Project/self-checkout/updateprice.php")
              .then(response => response.json())
              .then(updatedData => {
                  let newTotal = parseFloat(updatedData.total_price) || 0;
                  document.querySelector(".cart-summary p").innerHTML =
                      "<strong>Total Price:</strong> $" + newTotal.toFixed(2);
                    totalPrice = newTotal;


              })

              .catch(error => console.error("Error updating total:", error));

              // Update cart count in icon
              fetch("/COSC640 Project/self-checkout/get_cart_count.php")
              .then(response => response.json())
              .then(countData => {
                if (countData.success) {
                  document.querySelector(".cart-item-count").textContent = countData.count;
                }
              })
              .catch(err => console.error("Error fetching cart count:", err));
          } else {
              console.error("Failed to update quantity:", data.message);
          }
      })
      .catch(error => console.error("Fetch error:", error));
  }
});
