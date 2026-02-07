<?php
require 'dbConnect.php'; // your MongoDB connection file

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Proper update structure
$updateResult = $employeesCollection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectId($data['id'])],
    ['$set' => [
        'Fname' => $data['first_name'],   // <-- Fname (capital F), matches your structure
        'Lname' => $data['last_name'],     // <-- Lname (capital L)
        'role' => new MongoDB\BSON\ObjectId($data['role']),
        'salary.amount' => (float)$data['salary'],  // <-- Only updating the amount inside salary object
    ]]
);

if ($updateResult->getModifiedCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No changes made']);
}
?>
