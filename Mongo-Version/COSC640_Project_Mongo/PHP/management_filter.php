<?php
require_once 'dbConnect.php'; // Adjust path if needed
use MongoDB\BSON\ObjectId;

header('Content-Type: application/json');

// Check if 'role' is sent via GET or POST
$role = isset($_GET['role']) ? $_GET['role'] : '';

$filter = [];
if (!empty($role)) {
    try {
        $filter = ['role' => new ObjectId($role)];
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Invalid Role ID format']);
        exit;
    }
}

try {
    // Fetch employees with the filter applied
    $cursor = $employeesCollection->find($filter);
    $employees = [];

    // For each employee, fetch the role name using the role's ObjectId
    foreach ($cursor as $employee) {
        // Get the role's ObjectId from the employee
        $roleId = $employee['role'];

        // Look up the role in the rolesCollection
        $roleDocument = $rolesCollection->findOne(['_id' => $roleId]);

        // If role exists, extract the role name, otherwise use 'Unknown' as a fallback
        $roleName = isset($roleDocument['name']) ? $roleDocument['name'] : 'Unknown';

        // Push employee data, replacing the role ObjectId with the actual role name
        $employees[] = [
            '_id' => (string) $employee['_id'],
            'first_name' => $employee['Fname'],  // Assuming these are correct field names
            'last_name' => $employee['Lname'],
            'role' => $roleName,  // Send role name instead of ObjectId
            'salary' => isset($employee['salary']['amount']) ? $employee['salary']['amount'] : 0
        ];
    }

    echo json_encode(['success' => true, 'employees' => $employees]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
