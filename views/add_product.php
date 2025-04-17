<?php
session_start();
require_once __DIR__ . "/../config/database.php"; 

// Restrict access to logged-in admins only
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Handle image upload
    $target_dir = __DIR__ . "/../uploads/";  // Absolute directory path on server
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Create directory if it doesn't exist
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;  // Full server path (e.g., /var/www/html/shopping_mall/uploads/image.jpg)
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image type
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Store the relative image path in the database
            $image_path = "uploads/" . $image_name;  // Relative path to be saved in the database (e.g., "uploads/image.jpg")

            // Insert product into the database
            $sql = "INSERT INTO products (product_id, name, category, image, description, price, quantity) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $product_id, $name, $category, $image_path, $description, $price, $quantity);

            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully!'); window.location='admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Error adding product');</script>";
            }
        } else {
            echo "<script>alert('Error uploading image');</script>";
        }
    } else {
        echo "<script>alert('Invalid image file format (JPG, PNG, GIF allowed)');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #d5f5e3;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .navbar {
            background: #007bff;
            padding: 10px;
        }
        .navbar a {
            color: white;
            font-size: 32px;
            text-decoration: none;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .sub-menu {
            background: #d7dbdd;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .sub-menu a {
            color: #007bff;
            margin: 0 15px;
            font-size: 16px;
            text-decoration: none;
            font-weight: bold;
        }
        .sub-menu a:hover {
            text-decoration: underline;
        }
        .footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
            position: relative; 
        }
        .logout-btn {
            float: right;
            margin-right: 20px;
            margin-top: -5px;
            font-size: 14px;
            padding: 5px 10px;
            color: white;  
            background-color: #d7dbdd;
            border: none;  
            border-radius: 5px;  
        }

        .logout-btn:hover {
            background-color: #CD5C5C;  
            text-decoration: none;  
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar text-center">
    <a href="admin_dashboard.php">Admin Dashboard</a>
</div>

<!-- Sub Menu -->
<div class="sub-menu">
    <a href="admin_dashboard.php">Home</a>
    <a href="add_product.php">Add Product</a>
    <a href="manage_orders.php">Manage Orders</a>
    <a href="manage_offers.php">Manage Offers</a>
    <a href="manage_events.php">Manage Events</a>
    <a href="manage_dining.php">Manage Dining</a>
    <a href="logout.php" class="btn btn-danger btn-sm logout-btn">Logout</a>
</div>

    <div class="container">
        <h2 class="text-center">Add New Product</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product ID:</label>
                <input type="text" name="product_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category:</label>
                <input type="text" name="category" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Image:</label>
                <input type="file" name="image" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price:</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Product</button>
        </form>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> Shopping Mall Admin Panel. All Rights Reserved.
    </div>

</body>
</html>
