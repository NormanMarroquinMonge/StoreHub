// 🆕 Message box function
function showMessage(message, type) {
let messageBox = document.getElementById('messageBox');
if (!messageBox) {
  messageBox = document.createElement('div');
  messageBox.id = 'messageBox';
  document.body.prepend(messageBox);
}
messageBox.innerHTML = message;
messageBox.className = type === "success" ? 'message success' : 'message error';

messageBox.style.display = 'block';

setTimeout(() => {
  messageBox.style.display = 'none';
}, 4000); // Hide after 4 seconds
}

document.addEventListener('DOMContentLoaded', function() {

  const addItemForm = document.getElementById('addItemForm');

  if (addItemForm) {
    addItemForm.addEventListener('submit', async function(event) {
      event.preventDefault(); // Stop normal form submit (no refresh)

      const formData = new FormData(addItemForm); // Collect form data

      try {
        const response = await fetch('PHP/inventory_add_item.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          showMessage(`✅ Item "${data.name}" added successfully!`, "success");

          // Optional: clear the form fields
          addItemForm.reset();

          // Optional: hide image preview
          const imagePreview = document.getElementById('imagePreview');
          if (imagePreview) {
            imagePreview.style.display = 'none';
            imagePreview.src = '';
          }

        } else {
          showMessage(`❌ Error: ${data.error}`, "error");
        }
      } catch (error) {
        showMessage(`❌ Error: ${error.message}`, "error");
      }
    });
  }

  const categoryDropdown = document.getElementById('filterCategory');
  const tableBody = document.getElementById('inventoryBody');

  // Filter by category
  categoryDropdown.addEventListener('change', function() {
    const selectedCategory = this.value;

    fetch('inventory.php?category=' + selectedCategory)
      .then(response => response.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.getElementById('inventoryBody');

        tableBody.innerHTML = newTableBody.innerHTML;
      })
      .catch(error => {
        console.error('Error fetching inventory:', error);
      });
  });

  document.addEventListener('submit', function(event) {
    if (event.target.classList.contains('action-form')) {
      event.preventDefault(); // Stop normal form submit

      if (!confirm('Are you sure you want to delete this item?')) {
        return; // If user cancels, do nothing
      }

      const form = event.target;
      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showMessage(`✅ Item deleted: <strong>${data.name}</strong> (ID: ${data.productID})`, "success");

          // Reload the table
          const selectedCategory = document.getElementById('filterCategory').value;
          let url = 'inventory.php';
          if (selectedCategory) {
            url += '?category=' + encodeURIComponent(selectedCategory);
          }
          return fetch(url);
        } else {
          throw new Error(data.error || 'Failed to delete item.');
        }
      })
      .then(response => response.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.getElementById('inventoryBody');
        tableBody.innerHTML = newTableBody.innerHTML;
      })
      .catch(error => {
        showMessage(`❌ Error: ${error.message}`, "error");
        console.error('Error during delete:', error);
      });
    }
  });
});

function fetchProduct() {
  const objectId = document.getElementById('objectId').value.trim();

  fetch('PHP/findProduct.php?objectId=' + encodeURIComponent(objectId))
    .then(response => {
      if (!response.ok) {
        throw new Error('Product not found');
      }
      return response.json();
    })
    .then(product => {
      document.getElementById('productInfo').style.display = 'block';
      document.getElementById('productName').innerText = product.name;
      document.getElementById('productPrice').innerText = product.price;
      document.getElementById('productBrand').innerText = product.brand;
    })
    .catch(error => {
      alert(error.message);
      document.getElementById('productInfo').style.display = 'none';
    });
}

function deleteProduct() {
  const objectId = document.getElementById('objectId').value.trim();

  if (!confirm('Are you sure you want to delete this product?')) {
    return;
  }

  const formData = new FormData();
  formData.append('Product_ID', objectId);

  fetch('PHP/inventory_delete_item.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showMessage(`✅ Deleted: <strong>${data.name}</strong> (ID: ${data.productID})`, "success");

      // Hide the product info after deletion
      document.getElementById('productInfo').style.display = 'none';
      document.getElementById('objectId').value = '';

      // Refresh the inventory table dynamically (same as other delete!)
      const selectedCategory = document.getElementById('filterCategory').value;
      let url = 'inventory.php';
      if (selectedCategory) {
        url += '?category=' + encodeURIComponent(selectedCategory);
      }
      return fetch(url);
    } else {
      throw new Error(data.error || 'Failed to delete product.');
    }
  })
  .then(response => response.text())
  .then(html => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newTableBody = doc.getElementById('inventoryBody');
    document.getElementById('inventoryBody').innerHTML = newTableBody.innerHTML;
  })
  .catch(error => {
    showMessage(`❌ Error: ${error.message}`, "error");
    console.error('Error during delete:', error);
  });
}
