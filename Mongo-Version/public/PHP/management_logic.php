<?php
require_once 'dbConnect.php'; // Make sure path is correct

try {
    // Fetch all employees
    $employeesCursor = $employeesCollection->find();
    $employees = [];

    foreach ($employeesCursor as $employee) {
        // Default values
        $roleName = 'Unknown Role';

        // If role exists, fetch role name
        if (!empty($employee->role)) {
            // Make sure the role is an ObjectId
            $roleId = $employee->role instanceof MongoDB\BSON\ObjectId ? $employee->role : new MongoDB\BSON\ObjectId($employee->role);

            $roleDoc = $rolesCollection->findOne(['_id' => $roleId]);
            if ($roleDoc) {
                $roleName = $roleDoc->name ?? 'Unknown Role';
            }
        }

        // Build employee array
        $employees[] = [
            '_id' => (string) $employee->_id,
            'first_name' => $employee->Fname ?? '',
            'last_name' => $employee->Lname ?? '',
            'role' => $roleName, // ✅ Now store the role's NAME instead of ObjectId
            'salary' => $employee->salary->amount ?? 0,
        ];
    }
} catch (Exception $e) {
    echo "Failed to fetch employees: " . $e->getMessage();
    $employees = []; // fallback
}
?>
