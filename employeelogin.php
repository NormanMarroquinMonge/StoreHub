<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Employee Login</title>
    <style>
        /* General body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Same as index page color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Centered rectangle */
        .login-container {
            background-color: #e6e6e6;
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        /* Title at the top */
        .login-container h2 {
            text-align: center;
            margin: 0;
            padding-bottom: 15px;
        }

        /* Form styling */
        .form-input {
            margin-bottom: 20px;
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Login button style (same as index page) */
        .login-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        /* Error message styling */
        #error-message {
            display: none;
            color: red;
            text-align: center;
            margin-bottom: 20px; /* Space between the message and form */
            padding: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            font-weight: bold;
            animation: fadeInOut 5s forwards; /* Forward makes it stop at 100% opacity */
        }

        /* Animation for fading in and out */
        @keyframes fadeInOut {
            0% {
                opacity: 0;
            }
            30% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Employee Login</h2>

        <!-- Error message -->
        <div id="error-message">Incorrect first name, last name, or password.</div>

        <form method="POST"> <!-- Adjust action based on PHP backend -->
            <div>
                <input type="text" name="fname" placeholder="Enter first name" class="form-input" required>
            </div>
            <div>
                <input type="text" name="lname" placeholder="Enter last name" class="form-input" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Enter Password" class="form-input" required>
            </div>
            <div>
                <button type="submit" class="login-button">Login</button>
            </div>
        </form>
    </div>

    <?php
    session_start();
  require_once 'dbConnect.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $fname = $_POST['fname'];
      $lname = $_POST['lname'];
      $password = $_POST['password'];

      $query = 'SELECT password, role, employee_ID FROM employees WHERE Fname = :fname AND Lname = :lname';
      $stmt = $dbConn->prepare($query);
      $stmt->bindParam(':fname', $fname);
      $stmt->bindParam(':lname', $lname);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result) {
          // First/last name exists, now check password
          if (password_verify($password, $result['password'])){
              // Successful login
              $_SESSION['loggedIn'] = true;
              $_SESSION['role'] = $result['role'];
              $_SESSION['fname'] = $fname;
              $_SESSION['lname'] = $lname;
              $_SESSION['employee_ID'] = $result['employee_ID'];

              // Redirect based on role
              if ($result['role'] == 'manager') {
                  header("Location: Management.php");
              } else {
                  header("Location: checkoutDbAccess.php");
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
</body>
</html>
