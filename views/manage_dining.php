<?php
session_start();
require_once __DIR__ . "/../config/database.php"; // Ensure database connection

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle adding a new dining option
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_dining'])) {
    $dining_name = $_POST['dining_name'];
    $cuisine = $_POST['cuisine'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price = $_POST['price'];  // Changed from price_range to price
    $image = "";

    // Handle Image Upload
    if (!empty($_FILES["dining_image"]["name"])) {
        $target_dir = "uploads/dining/";
        
        // Check if directory exists, create if not
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  // Creates the directory if it does not exist
        }

        // Sanitize file name
        $image = time() . "_" . basename($_FILES["dining_image"]["name"]);
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file format
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            echo "<script>alert('Invalid image format! Only JPG, JPEG, PNG & GIF allowed.');</script>";
            exit;
        }

        // Check file size (2MB limit)
        if ($_FILES["dining_image"]["size"] > 2 * 1024 * 1024) {
            echo "<script>alert('Image size should not exceed 2MB.');</script>";
            exit;
        }

        // Upload file
        if (!move_uploaded_file($_FILES["dining_image"]["tmp_name"], $target_file)) {
            echo "<script>alert('Error uploading image.');</script>";
            exit;
        }
    }

    // Insert into database
    $insert_query = "INSERT INTO dining (dining_name, cuisine, location, description, price, image_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssss", $dining_name, $cuisine, $location, $description, $price, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Dining option added successfully!'); window.location='manage_dining.php';</script>";
    } else {
        echo "<script>alert('Error adding dining option.');</script>";
    }
}

// Handle deleting a dining option
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_dining'])) {
    $dining_id = $_POST['dining_id'];

    // Get image name to delete it from the folder
    $image_query = "SELECT image_path FROM dining WHERE dining_id = ?";
    $stmt = $conn->prepare($image_query);
    $stmt->bind_param("i", $dining_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image)) {
        unlink("uploads/dining/" . $image); // Delete image file
    }

    $delete_query = "DELETE FROM dining WHERE dining_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $dining_id);

    if ($stmt->execute()) {
        echo "<script>alert('Dining option deleted successfully!'); window.location='manage_dining.php';</script>";
    } else {
        echo "<script>alert('Error deleting dining option.');</script>";
    }
}

// Fetch all dining options
$query = "SELECT * FROM dining ORDER BY dining_name ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dining</title>

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
            flex: 1;
            margin-top: 30px;
            padding-bottom: 60px; /* Ensures space before footer */
        }
        .table {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .dining-img {
            width: 80px;  
            height: 80px; 
            object-fit: cover; 
            border-radius: 5px; 
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
            position: relative;
            bottom: 0;
            width: 100%;
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
    <h2 class="text-center">Manage Dining</h2>

    <!-- Add New Dining Form -->
    <div class="card p-3 mt-4">
        <h4>Add New Dining Option</h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label">Dining Name</label>
                <input type="text" name="dining_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Cuisine</label>
                <input type="text" name="cuisine" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Price</label>
                <input type="text" name="price" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Dining Image</label>
                <input type="file" name="dining_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="add_dining" class="btn btn-primary">➕ Add Dining</button>
        </form>
    </div>

    <!-- Dining Table -->
    <table class="table table-striped table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Dining Name</th>
                <th>Cuisine</th>
                <th>Location</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['dining_id'] ?></td>
                    <td>
                        <?php if (!empty($row['image_path'])) { ?>
                            <img src="uploads/dining/<?= htmlspecialchars($row['image_path']) ?>" class="dining-img">
                        <?php } else { echo "No Image"; } ?>
                    </td>
                    <td><?= htmlspecialchars($row['dining_name']) ?></td>
                    <td><?= htmlspecialchars($row['cuisine']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>  <!-- Changed from price_range to price -->
                    <td>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="dining_id" value="<?= $row['dining_id'] ?>">
                            <button type="submit" name="delete_dining" class="btn btn-danger btn-sm">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<div class="footer">
    &copy; <?php echo date("Y"); ?> Shopping Mall Admin Panel. All Rights Reserved.
</div>

</body>
</html>
