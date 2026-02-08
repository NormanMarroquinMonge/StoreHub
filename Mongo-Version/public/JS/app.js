function redirectToPage(userType){
  window.location.href = userType + "login.php";
}


console.log("Total Price: ", totalPrice);
            paypal.Buttons({
              createOrder: function(data, actions) {
                return fetch("public/self-checkout/updateprice.php") // Fetch latest total
                .then(response => response.json())
                .then(data => {
                  return actions.order.create({
                    purchase_units: [{
                      amount: {
                        value: data.total_price
                      }
                    }]
                  });
                });
              },
            onApprove: function(data, actions) {
              console.log("onApprove function triggered", data);
              return actions.order.capture().then(function(details) {
                // Send transaction details to server for storage
              fetch('public/self-checkout/save_transaction.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify(details)
                })
                .then(response => response.json())
                .then(data => {
                  console.log(data);
                  if(data.success){
                  showSuccessCard();  // Call function to show UI feedback
                  document.querySelector(".cart-items").innerHTML = "<p>Your cart is empty.</p>";
                  document.querySelector(".cart-item-count").textContent = "0";
                  document.querySelector(".cart-summary p").innerHTML = "<strong>Total Price:</strong> $0.00";
                }
                })
                .catch(error => console.error('Error:', error));
              });
            },
            onCancel: function(data) {
                alert('Payment Cancelled');
            },
            onError: function(err) {
                alert('An error occurred during payment');
            }
        }).render('#paypal-button-container'); // Render the PayPal button inside the div

        function showSuccessCard(name) {
    // Create the overlay
    let overlay = document.createElement("div");
    overlay.id = "payment-success-overlay";

    // Create the success card
      let successCard = document.createElement("div");
      successCard.id = "payment-success-card";
      successCard.innerHTML = `
        <h2>Payment Successful!</h2>
        <p>Thank you, ${name}!</p>
    `;

    // Add elements to the page
    document.body.appendChild(overlay);
    document.body.appendChild(successCard);

    // Automatically remove the card and overlay after 3 seconds
    setTimeout(() => {
        successCard.remove();
        overlay.remove();
    }, 3000);
}
