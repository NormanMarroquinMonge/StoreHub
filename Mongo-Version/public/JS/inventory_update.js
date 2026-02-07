// Event delegation for the update button click event
document.getElementById('inventoryBody').addEventListener('click', function(event) {
    if (event.target && event.target.matches('.edit-btn')) {
        const button = event.target;
        const row = button.closest('tr');
        const isEditing = row.getAttribute('data-editing') === 'true';

        if (!isEditing) {
            // Store initial values before editing
            const initialValues = {};
            row.querySelectorAll('.editable').forEach(cell => {
                const fieldName = cell.getAttribute('data-field');
                const value = cell.textContent.trim();
                initialValues[fieldName] = value;

                // Turn fields into input boxes
                if (fieldName === 'last_restock') {
                    // Make sure the date value is in the correct format (YYYY-MM-DD)
                    const formattedDate = value ? new Date(value).toISOString().split('T')[0] : '';
                    cell.innerHTML = `<input type="date" name="${fieldName}" value="${formattedDate}" />`;
                } else {
                    // For other fields, use a text input
                    cell.innerHTML = `<input type="text" name="${fieldName}" value="${value}" />`;
                }
            });

            // Hide the delete button
            const deleteButton = row.querySelector('.action-form');
            deleteButton.style.display = 'none';

            // Add a cancel button
            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Cancel';
            cancelButton.classList.add('cancel-btn');
            row.querySelector('td:last-child').appendChild(cancelButton);

            // Turn the "Update" button into "Save"
            button.textContent = 'Save';
            row.setAttribute('data-editing', 'true');

            // Store the initial values on the row so we can revert them later
            row.setAttribute('data-initial-values', JSON.stringify(initialValues));
        } else {
            // Collect updated data and send via fetch POST
            const updatedData = {};
            row.querySelectorAll('input').forEach(input => {
                updatedData[input.name] = input.value.trim();
            });

            const inventoryId = button.getAttribute('data-id');

            fetch('PHP/update_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    inventory_ID: inventoryId,
                    updated_fields: updatedData
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the row display with new values
                    row.querySelectorAll('.editable').forEach(cell => {
                        const fieldName = cell.getAttribute('data-field');
                        // Set the updated value to the cell
                        cell.textContent = updatedData[fieldName];
                    });
                    button.textContent = 'Update';
                    row.setAttribute('data-editing', 'false');

                    // Remove the cancel button after successful save
                    row.querySelector('.cancel-btn').remove();

                    // Show the delete button again
                    const deleteButton = row.querySelector('.action-form');
                    deleteButton.style.display = '';
                } else {
                    alert('Update failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating:', error);
                alert('An error occurred while updating.');
            });
        }
    }

    // Event delegation for the cancel button click event
    if (event.target && event.target.matches('.cancel-btn')) {
        const button = event.target;
        const row = button.closest('tr');

        // Revert changes to the initial values
        const initialValues = JSON.parse(row.getAttribute('data-initial-values'));
        row.querySelectorAll('.editable').forEach(cell => {
            const fieldName = cell.getAttribute('data-field');
            const originalValue = initialValues[fieldName];

            // Revert the field values to the initial ones (non-editable text)
            cell.textContent = originalValue; // Keep the text content as it was

            // If the field was an input field, replace the input with the original text
            const input = cell.querySelector('input');
            if (input) {
                input.remove(); // Remove the input field
                cell.innerHTML = originalValue; // Restore the original text
            }
        });

        // Revert the "Save" button back to "Update"
        row.querySelector('.edit-btn').textContent = 'Update';
        row.setAttribute('data-editing', 'false');

        // Remove the cancel button
        row.querySelector('.cancel-btn').remove();

        // Show the delete button again
        const deleteButton = row.querySelector('.action-form');
        deleteButton.style.display = '';
    }
});
