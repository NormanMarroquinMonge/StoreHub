<?php
session_start();
if (!isset($_SESSION['employee_ID'])) {
    header("Location: employeelogin.php");
    exit();
}
require_once __DIR__ . '/dbConnect.php';

$categoryId = $_GET['category'] ?? null;

$pipeline = [
    [
        '$lookup' => [
            'from' => 'products',
            'localField' => 'product_id',
            'foreignField' => '_id',
            'as' => 'product_info'
        ]
    ],
    ['$unwind' => '$product_info']
];

if ($categoryId) {
    $pipeline[] = [
        '$match' => [
            'product_info.category_id' => new MongoDB\BSON\ObjectId($categoryId)
        ]
    ];
}

$inventoryItems = $inventoryCollection->aggregate($pipeline);
return $inventoryItems;
?>
