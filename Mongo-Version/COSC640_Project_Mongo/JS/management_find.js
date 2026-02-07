// ============== FIND EMPLOYEE ==============
async function fetchEmployee() {
    const employeeId = document.getElementById('employeeId').value.trim();

    if (!employeeId) {
        alert('Please enter an Employee ID.');
        return;
    }

    try {
        const response = await fetch('PHP/management_find.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: 'find',
              employeeId: employeeId
            })
        });

        const result = await response.json();

        if (result.success && result.employee) {
            document.getElementById('employeeFirstName').textContent = result.employee.Fname;
            document.getElementById('employeeLastName').textContent = result.employee.Lname;
            document.getElementById('employeeRole').textContent = result.employee.role;
            document.getElementById('employeeSalary').textContent = result.employee.salary;
            document.getElementById('employeeInfo').style.display = 'block';
        } else {
            alert('Employee not found.');
            document.getElementById('employeeInfo').style.display = 'none';
        }
    } catch (error) {
        console.error('Error fetching employee:', error);
        alert('Error fetching employee.');
    }
}

// ============== DELETE EMPLOYEE ==============
async function deleteEmployee() {
    const employeeId = document.getElementById('employeeId').value.trim();

    if (!employeeId) {
        alert('Please enter an Employee ID.');
        return;
    }

    if (!confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch('PHP/management_find.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              action: 'delete',
              employeeId: employeeId })
        });

        const result = await response.json();

        if (result.success) {
            alert('Employee deleted successfully.');
            // Optionally, clear the employee info display
            document.getElementById('employeeInfo').style.display = 'none';
            document.getElementById('employeeId').value = ''; // clear input box
        } else {
            alert('Failed to delete employee: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting employee:', error);
        alert('Error deleting employee.');
    }
}
