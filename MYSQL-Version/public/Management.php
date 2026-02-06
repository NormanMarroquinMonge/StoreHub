<?php
session_start();
if (!isset($_SESSION['employee_ID'])) {
    header("Location: employeelogin.php");
    exit();
}
// Establish database connection
require_once '../dbConnect.php';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create employee (process form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $firstName = $_POST['fname'];
    $lastName = $_POST['lname'];
    $role = $_POST['role'];
    $salary = $_POST['salary'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Accept hashed and salted password

    $sql = "INSERT INTO employees (Fname, Lname, role, salary, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssds", $firstName, $lastName, $role, $salary, $hashedPassword);

    if ($stmt->execute()) {
        echo "Employee added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
</head>
<body>
  <?php include 'employeeNav.html'; ?>
  <div class="manager-container">
  <h1 class="manager-header">Employee Management</h1>

  <form method="POST" class="manager-form">
      <label for="fname" class="manager-label">First Name:</label>
      <input type="text" id="fname" name="fname" class="manager-input" required>

      <label for="lname" class="manager-label">Last Name:</label>
      <input type="text" id="lname" name="lname" class="manager-input" required>

      <label for="role" class="manager-label">Role:</label>
      <input type="text" id="role" name="role" class="manager-input" required>

      <label for="salary" class="manager-label">Salary:</label>
      <input type="number" step="0.01" id="salary" name="salary" class="manager-input" required>

      <label for="password" class="manager-label">Password:</label>
      <input type="password" id="password" name="password" class="manager-input" required>

      <button type="submit" name="create" class="manager-button">Add Employee</button>
  </form>
</div>
</body>
</html>
