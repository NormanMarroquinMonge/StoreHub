<?php

//---------CODE TO POPULATE TABLE-----------//
require_once 'PHP/management_logic.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" href="CSS/employee_style.css">
    <style>
    /* Same button and form styles you used for inventory */
    .action-form {
        display: inline;
        margin: 0;
        padding: 0;
        border: none;
    }
    .action-form button {
        margin: 0 5px 0 0;
    }
    .success-message {
      position: fixed;
      top: 20px; /* Changed from 50% to 20px from top */
      left: 50%;
      transform: translateX(-50%); /* Only translate horizontally now */
      background-color: #4caf50;
      color: white;
      padding: 15px 30px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      font-size: 16px;
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 9999;
      text-align: center;
  }
  .success-message.show {
      opacity: 1;
  }

  .cancel-btn {
    background-color: #e74c3c; /* A nice red color */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.cancel-btn:hover {
    background-color: #c0392b; /* Darker red on hover */
}
    </style>
</head>
<body>
<div class="container">
    <h1>Employee Management</h1>

<div class="form-container">
  <div class="form-box">
    <!-- Add Employee Form -->
    <h2>Add New Employee</h2>
    <form id="addEmployeeForm" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="category">Category:</label>
        <select id="category" name="role" required>
            <option value="" selected disabled>Choose a role</option>
            <option value="6807ee9f4ad193cfcb31d60c">Manager</option>
            <option value="6807ee9f4ad193cfcb31d60e">Inventory</option>
            <option value="6807ee9f4ad193cfcb31d60d">Cashier</option>
        </select>

        <label for="salary">Salary:</label>
        <input type="number" step="0.01" id="salary" name="salary" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" name="add_employee" style="margin-top: 20px;">Add Employee</button>
    </form>
  </div>

  <div class="form-box">
    <!-- Delete Employee -->
    <h2>Delete Employee</h2>
    <form id="deleteEmployeeForm">
        <label for="employeeId">Enter Employee ID:</label><br>
        <input type="text" id="employeeId" name="employeeId" required>
        <button type="button" onclick="fetchEmployee()">Find Employee</button>

        <div id="employeeInfo" style="margin-top: 20px; display: none;">
            <p><strong>First Name:</strong> <span id="employeeFirstName"></span></p>
            <p><strong>Last Name:</strong> <span id="employeeLastName"></span></p>
            <p><strong>Role:</strong> <span id="employeeRole"></span></p>
            <p><strong>Salary:</strong> <span id="employeeSalary"></span></p>
            <button type="button" onclick="deleteEmployee()">Delete Employee</button>
        </div>
    </form>
  </div>
</div>
<h2>Filter Employees by Role</h2>
<select id="filterRole">
    <option value="">All Roles</option>
    <option value="6807ee9f4ad193cfcb31d60c">Manager</option>
    <option value="6807ee9f4ad193cfcb31d60e">Inventory</option>
    <option value="6807ee9f4ad193cfcb31d60d">Cashier</option>
</select>
<!-- Employee Table -->
<h2>Employee List</h2>
<table id="employeeTable">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Role</th>
            <th>Salary</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="employeeBody">
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= htmlspecialchars($employee['_id']) ?></td>
                <td class="editable" data-field="first_name" data-original="<?= htmlspecialchars($employee['first_name']) ?>"><?= htmlspecialchars($employee['first_name']) ?></td>
                <td class="editable" data-field="last_name" data-original="<?= htmlspecialchars($employee['last_name']) ?>"><?= htmlspecialchars($employee['last_name']) ?></td>
                <td class="editable" data-field="role" data-original="<?= htmlspecialchars($employee['role']) ?>"><?= htmlspecialchars($employee['role']) ?></td>
                <td class="editable" data-field="salary" data-original="<?= htmlspecialchars(number_format($employee['salary'], 2)) ?>"><?= htmlspecialchars(number_format($employee['salary'], 2)) ?></td>
                <td>
                    <!-- Action Buttons -->
                    <button class="edit-btn" data-id="<?= $employee['_id'] ?>">Update</button>
                    <button class="delete-btn" data-id="<?= $employee['_id'] ?>">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

<!---JS CODE FOR ADD EMPLOYEE FORM--->
<script src="JS/management_add.js"></script>
<script src="JS/management_find.js"></script>
<script src="JS/management_filter.js"></script>
<script src="JS/management_table_delete.js"></script>
<script src="JS/management_table_update.js"></script>
</body>
</html>>
