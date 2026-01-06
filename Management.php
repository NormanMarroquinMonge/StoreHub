<?php
session_start();
if (!isset($_SESSION['employee_ID'])) {
    header("Location: employeelogin.php");
    exit();
}
// Establish database connection
$host = '100.15.171.64';
$dbname = 'storeHub';
$username = 'tim';
$password = '13';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Management</h1>

        <!-- Employee Creation Form -->
        <form method="POST">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required>

            <label for="role">Role:</label>
            <input type="text" id="role" name="role" required>

            <label for="salary">Salary:</label>
            <input type="number" step="0.01" id="salary" name="salary" required>

            <label for="password">Password:</label>
            <input type="text" id="password" name="password" required>

            <button type="submit" name="create">Add Employee</button>
        </form>
    </div>
</body>
</html>
