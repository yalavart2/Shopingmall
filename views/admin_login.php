<?php
session_start();
include dirname(__DIR__) . '/config/database.php'; // Ensure the database connection

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Query to check if the email exists
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND role = 'Administrator'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Fetch user details
            $stmt->bind_result($id, $name, $hashed_password, $role);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Set session variables
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_name'] = $name;
                $_SESSION['role'] = $role;

                // Redirect to the dashboard page
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error_message = "Incorrect password!";
            }
        } else {
            $error_message = "No user found with that email or not an admin! <a href='admin_register.php'>Register here</a>";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shopping Mall</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background:rgb(174, 188, 203); /* Light gray background */
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 450px;
            width: 100%;
            background: #ffffff; /* White background for the form container */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #007bff; /* Blue color for the title */
            margin-bottom: 30px;
        }

        .alert {
            text-align: center;
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-control {
            height: 45px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #007bff;
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Login</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="alert alert-danger text-center"><?= $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_login.php">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <p class="register-link">Don't have an account? <a href="admin_register.php">Register here</a></p>
    </form>
</div>

</body>
</html>
