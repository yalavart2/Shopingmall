<?php
session_start();
include dirname(__DIR__) . '/config/database.php'; // Ensure database connection is included correctly

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify password using password_verify()
            if (password_verify($password, $user['password'])) {
                // Store session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'Administrator':
                        header("Location: ../admin_dashboard.php");
                        break;
                    case 'client':
                        header("Location: ../client_dashboard.php");
                        break;
                    default:
                        header("Location: ../views/home.php");
                        break;
                }
                exit;
            } else {
                $error_message = "Invalid email or password!";
            }
        } else {
            $error_message = "No user found with this email!";
        }

        $stmt->close();
    } else {
        $error_message = "Please enter both email and password!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shopping Mall</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        /* Body and background */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6c757d, #ffffff);
            height: 100vh;
            margin: 0;
        }

        /* Centering the container */
        .container {
            max-width: 400px;
            margin: 5% auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: none;
            padding: 12px;
        }

        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-bottom: 15px;
            font-size: 16px;
        }

        .login-link {
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Mobile responsiveness */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
                margin-top: 10%;
                width: 90%;
            }
        }
    </style>

    <script>
        function validateLogin() {
            let email = document.forms["loginForm"]["email"].value.trim();
            let password = document.forms["loginForm"]["password"].value.trim();

            if (email === "" || password === "") {
                alert("Email and Password are required!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <div class="container">
        <h2 class="text-center">Login</h2>

        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form name="loginForm" method="POST" onsubmit="return validateLogin();">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-custom">Login</button>
            <p class="login-link">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

</body>
</html>
