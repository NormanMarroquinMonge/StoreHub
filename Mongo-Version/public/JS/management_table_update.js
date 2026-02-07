document.addEventListener('DOMContentLoaded', function() {
    const roleOptions = [
        { id: '6807ee9f4ad193cfcb31d60c', name: 'Manager' },
        { id: '6807ee9f4ad193cfcb31d60e', name: 'Inventory' },
        { id: '6807ee9f4ad193cfcb31d60d', name: 'Cashier' }
    ];

    const employeeTableBody = document.getElementById('employeeBody');

    employeeTableBody.addEventListener('click', function(event) {
        const target = event.target;

        if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            makeRowEditable(row, target);
        } else if (target.classList.contains('save-btn')) {
            const row = target.closest('tr');
            saveRow(row, target);
        } else if (target.classList.contains('cancel-btn')) {
            const row = target.closest('tr');
            cancelEdit(row, target);
        }
    });

    function makeRowEditable(row, editButton) {
        row.querySelectorAll('.editable').forEach(cell => {
        const field = cell.getAttribute('data-field');
        const currentValue = cell.textContent.trim();

        if (field === 'role') {
            const select = document.createElement('select');
            roleOptions.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id;
                option.textContent = role.name;
                if (currentValue === role.name) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            cell.innerHTML = ''; // Empty cell to add select element
            cell.appendChild(select);
        } else {
            const input = document.createElement('input');
            input.type = field === 'salary' ? 'number' : 'text';
            if (field === 'salary') {
                input.step = '0.01';
            }
            input.value = currentValue;
            cell.innerHTML = ''; // Empty cell to add input element
            cell.appendChild(input);
        }
    });

    // Save button turn from "Edit" -> "Save"
    editButton.textContent = 'Save';
    editButton.classList.remove('edit-btn');
    editButton.classList.add('save-btn');

    // Cancel button turn from "Delete" -> "Cancel"
    const deleteButton = row.querySelector('.delete-btn');
    deleteButton.textContent = 'Cancel';
    deleteButton.classList.remove('delete-btn');
    deleteButton.classList.add('cancel-btn');
    }

    function saveRow(row, saveButton) {
      const employeeId = saveButton.getAttribute('data-id');

const updatedData = {
    id: employeeId,
    first_name: row.querySelector('[data-field="first_name"] input').value.trim(),
    last_name: row.querySelector('[data-field="last_name"] input').value.trim(),
    role: row.querySelector('[data-field="role"] select').value, // Get the new role from select
    salary: parseFloat(row.querySelector('[data-field="salary"] input').value)
};

// Only update the role if it has changed
const originalRole = row.querySelector('[data-field="role"]').getAttribute('data-original');
if (updatedData.role === originalRole) {
    updatedData.role = originalRole; // Keep the original role if no change
}

// Send POST request to management_table_update.php
fetch('PHP/management_table_update.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(updatedData)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Update cells back to normal text
        row.querySelector('[data-field="first_name"]').textContent = updatedData.first_name;
        row.querySelector('[data-field="last_name"]').textContent = updatedData.last_name;

        const roleName = roleOptions.find(role => role.id === updatedData.role)?.name || 'Unknown';
        row.querySelector('[data-field="role"]').textContent = roleName;

        row.querySelector('[data-field="salary"]').textContent = updatedData.salary.toFixed(2);

        // 🚨 Update the original values too!
        row.querySelector('[data-field="first_name"]').setAttribute('data-original', updatedData.first_name);
        row.querySelector('[data-field="last_name"]').setAttribute('data-original', updatedData.last_name);
        row.querySelector('[data-field="role"]').setAttribute('data-original', roleName);
        row.querySelector('[data-field="salary"]').setAttribute('data-original', updatedData.salary.toFixed(2));

        // Turn Save -> Update
        saveButton.textContent = 'Update';
        saveButton.classList.remove('save-btn');
        saveButton.classList.add('edit-btn');

        // Turn Cancel -> Delete
        const cancelButton = row.querySelector('.cancel-btn');
        cancelButton.textContent = 'Delete';
        cancelButton.classList.remove('cancel-btn');
        cancelButton.classList.add('delete-btn');

        showSuccessMessage('Employee updated successfully!');
    } else {
        alert('You did not make any changes.');
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('An error occurred during update.');
});
    }

    function cancelEdit(row, cancelButton) {
        // Reload the original values
        row.querySelectorAll('.editable').forEach(cell => {
            const originalValue = cell.getAttribute('data-original');
            cell.textContent = originalValue;
        });

        // Turn Cancel -> Delete
        cancelButton.textContent = 'Delete';
        cancelButton.classList.remove('cancel-btn');
        cancelButton.classList.add('delete-btn');

        // Turn Save -> Update
        const saveButton = row.querySelector('.save-btn');
        saveButton.textContent = 'Update';
        saveButton.classList.remove('save-btn');
        saveButton.classList.add('edit-btn');
    }

    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.classList.add('success-message');
        successDiv.textContent = message;
        document.body.appendChild(successDiv);

        setTimeout(() => {
            successDiv.classList.add('show');
        }, 100);

        setTimeout(() => {
            successDiv.classList.remove('show');
            setTimeout(() => successDiv.remove(), 500);
        }, 5000);
    }
});
