<?php
// management_action.php

require_once 'dbConnect.php'; // Connect to your database

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Get the JSON body
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['action'])) {
        throw new Exception('Action is required.');
    }

    $action = $input['action'];

    if (!isset($input['employeeId'])) {
        throw new Exception('Employee ID is required.');
    }

    $employeeId = $input['employeeId'];

    if ($action === 'find') {
      // FIND employee
      $employee = $employeesCollection->findOne([
      '_id' => new MongoDB\BSON\ObjectId($employeeId)
    ]);

    if (!$employee) {
     echo json_encode([
         'success' => false,
         'message' => 'Employee not found.'
       ]);
       exit;
     }

     // Default role name
     $roleName = 'Unknown Role';

     // Lookup the role name
  if (!empty($employee->role)) {
    $roleId = $employee->role instanceof MongoDB\BSON\ObjectId ? $employee->role : new MongoDB\BSON\ObjectId($employee->role);
     $roleDoc = $rolesCollection->findOne(['_id' => $roleId]);
     if ($roleDoc) {
         $roleName = $roleDoc->name ?? 'Unknown Role';
     }
   }

   // Now send role NAME instead of ObjectId
  echo json_encode([
      'success' => true,
      'employee' => [
          '_id' => (string) $employee->_id,
          'Fname' => $employee->Fname,
          'Lname' => $employee->Lname,
          'role' => $roleName, // ✅ send back the readable role name
          'salary' => isset($employee->salary->amount)
          ? number_format($employee->salary->amount, 2)
          : number_format(0, 2)
       ]
   ]);
  exit;

    } elseif ($action === 'delete') {
        // DELETE employee
        $deleteResult = $employeesCollection->deleteOne([
            '_id' => new MongoDB\BSON\ObjectId($employeeId)
        ]);

        if ($deleteResult->getDeletedCount() === 0) {
            throw new Exception('Employee not found or already deleted.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ]);
        exit;

    } else {
        throw new Exception('Invalid action.');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
