// ================= ADD EMPLOYEE =================
// Your AJAX code to add employee
document.getElementById('addEmployeeForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Stop normal form submission

    const formData = new FormData(this);

    try {
        const response = await fetch('PHP/management_add.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Employee added successfully!');

            // Add the new employee to the table without refreshing
            const employeeBody = document.getElementById('employeeBody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>${result.employee._id}</td>
                <td class="editable" data-field="first_name">${result.employee.first_name}</td>
                <td class="editable" data-field="last_name">${result.employee.last_name}</td>
                <td class="editable" data-field="role">${result.employee.role}</td>
                <td class="editable" data-field="salary">${Number(result.employee.salary).toFixed(2)}</td>
                <td>
                    <button class="edit-btn" data-id="${result.employee._id}">Update</button>
                    <button class="delete-btn" data-id="${result.employee._id}">Delete</button>
                </td>
            `;

            employeeBody.appendChild(newRow);

            this.reset(); // Clear the form
        } else {
            alert('Failed to add employee: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding employee.');
    }
});
