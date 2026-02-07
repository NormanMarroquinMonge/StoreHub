<?php
require_once 'dbConnect.php';
use MongoDB\BSON\ObjectId;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing employee ID']);
        exit;
    }

    try {
        $employeeId = new ObjectId($data['id']);
        $result = $employeesCollection->deleteOne(['_id' => $employeeId]);

        if ($result->getDeletedCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Employee not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
