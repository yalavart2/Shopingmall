<?php
// Start the session
session_start();

// Include database configuration
include dirname(__DIR__) . '/config/database.php';

// Initialize error and success messages
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if all fields are filled
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows == 0) {
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'Administrator'; // Admin role assigned by default

                // Insert new admin into the database
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    $success_message = "Admin registered successfully. You can now log in.";

                    // Redirect to admin login page after successful registration
                    header("Location: admin_login.php");
                    exit; // Terminate further script execution after the redirect
                } else {
                    $error_message = "Error registering admin. Please try again.";
                }

                $stmt->close();
            } else {
                $error_message = "Email is already in use!";
            }
            $check_stmt->close();
        } else {
            $error_message = "Passwords do not match!";
        }
    } else {
        $error_message = "All fields are required!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Shopping Mall</title>

    <style>
        /* Body and Background */
        body {
            background: linear-gradient(135deg,rgb(166, 209, 193),rgb(203, 140, 140));
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container Styles */
        .container {
            max-width: 450px;
            width: 100%;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 30px;
        }

        /* Error and Success Messages */
        .alert {
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        /* Input Fields */
        .form-control {
            width: 100%;
            height: 45px;
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Button Styles */
        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        /* Link Styles */
        .register-link {
            font-size: 16px;
            margin-top: 20px;
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
        <h2>Admin Registration</h2>

        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_register.php">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn-custom">Register as Admin</button>
            <p class="register-link">Already have an account? <a href="admin_login.php">Login</a></p>
        </form>
    </div>

</body>

</html>
