<?php 
// Ensure the correct path for the database connection
include dirname(__DIR__) . '/config/database.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Mall - Home</title>
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg,rgb(67, 86, 122),rgb(151, 52, 52));
            font-family: 'Arial', sans-serif;
        }
        .container {
            text-align: center;
            margin-top: 10%;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            font-weight: 700;
        }
        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Welcome to the Shopping Mall</h2>
        <p>Find the best products at the most affordable prices!</p>
        
        <a href="products.php" class="btn btn-primary btn-custom">🛍️ Browse Products</a>
        <a href="cart.php" class="btn btn-warning btn-custom">🛒 View Cart</a>
        <a href="login.php" class="btn btn-success btn-custom">🔐 Login</a>
        <a href="register.php" class="btn btn-secondary btn-custom">📝 Register</a>
    </div>

</body>
</html>
