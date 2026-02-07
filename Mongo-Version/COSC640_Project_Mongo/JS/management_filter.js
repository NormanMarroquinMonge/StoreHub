document.addEventListener('DOMContentLoaded', function() {
    const filterDropdown = document.getElementById('filterRole');
    const employeeTableBody = document.getElementById('employeeBody');

    filterDropdown.addEventListener('change', function() {
        const selectedRole = this.value;

        // Send an AJAX request to the PHP server to filter employees by role
        fetch(`PHP/management_filter.php?role=${selectedRole}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear the existing rows in the table
                    employeeTableBody.innerHTML = '';

                    // Add the filtered employees to the table
                    data.employees.forEach(employee => {
                        const row = document.createElement('tr');

                        row.innerHTML = `
                            <td>${employee._id}</td>
                            <td class="editable" data-field="first_name">${employee.first_name}</td>
                            <td class="editable" data-field="last_name">${employee.last_name}</td>
                            <td class="editable" data-field="role">${employee.role}</td>
                            <td class="editable" data-field="salary">${employee.salary}</td>
                            <td>
                                <button class="edit-btn" data-id="${employee._id}">Update</button>
                                <button class="delete-btn" data-id="${employee._id}">Delete</button>
                            </td>
                        `;
                        employeeTableBody.appendChild(row);
                    });
                } else {
                    alert('Error fetching employees: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load employee data.');
            });
    });
});
