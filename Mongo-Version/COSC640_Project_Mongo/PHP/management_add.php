<?php

require_once 'dbConnect.php'; // Make sure path is correct
// ================= ADD EMPLOYEE =================
if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
    try {
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $role = $_POST['role'] ?? '';
        $salary = floatval($_POST['salary'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($firstName && $lastName && $role && $salary && $password) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $roleObjectId = new MongoDB\BSON\ObjectId($role);
            // Create employee document
            $employeeData = [
                'Fname' => $firstName,
                'Lname' => $lastName,
                'role' =>   $roleObjectId,
                'salary' => [
                    'amount' => $salary,
                    'currency' => 'USD'
                ],
                'password' => $hashedPassword
            ];

            // Insert into MongoDB
            $insertResult = $employeesCollection->insertOne($employeeData);

            // Respond with JSON
            echo json_encode([
                'success' => true,
                'employee' => [
                    '_id' => (string) $insertResult->getInsertedId(),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'role' => $role,
                    'salary' => $salary
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

?>
