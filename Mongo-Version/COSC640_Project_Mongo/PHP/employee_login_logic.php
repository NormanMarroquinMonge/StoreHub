<?php
session_start();
require_once __DIR__ . '/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];

    $employee = $employeesCollection->findOne([
       'Fname' => $fname,
       'Lname' => $lname
     ]);

    if ($employee) {
        // Check if the password matches
        if (password_verify($password, $employee['password'])) {

          session_start();

          $employee_id = $employee['_id'];
          $employee_role = $employee['role'];

          $adminRoleId = new MongoDB\BSON\ObjectId('6807ee9f4ad193cfcb31d60c');
          $inventoryRoleId = new MongoDB\BSON\ObjectId('6807ee9f4ad193cfcb31d60e');
          $checkoutRoleId = new MongoDB\BSON\ObjectId('6807ee9f4ad193cfcb31d60e');

          //might need to change  to ObjectId
          $_SESSION['employee_ID'] = (string) $employee_id;
          $_SESSION['loggedIn'] = true;
          $_SESSION['role'] = (string) $employee['role'];
          $_SESSION['fname'] = $fname;
          $_SESSION['lname'] = $lname;

            // Redirect based on role
            if ($employee_role ==   $adminRoleId) {
                header("Location: Management.php");
            } else if ($employee_role == $inventoryRoleId){
                header("Location: Inventory.php");
            } else if ($employee_role ==   $checkoutRoleId){
                header("Location: checkoutManual.php");
            }
            exit();
        } else {
            // Password is wrong
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('error-message').style.display = 'block';
                });
            </script>";
        }
    } else {
        // First/last name combination doesn't exist
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('error-message').style.display = 'block';
            });
        </script>";
    }
}
?>
