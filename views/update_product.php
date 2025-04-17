<?php
session_start();
require_once __DIR__ . "/../config/database.php"; 

// Restrict access to logged-in admins only
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Get product ID from the URL
if (!isset($_GET['product_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$product_id = $_GET['product_id'];
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit;
}

$product = $result->fetch_assoc();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $new_image = $product['image']; // Keep the existing image if no new one is uploaded

    // Handle image upload if a new file is provided
    if (!empty($_FILES['image']['name'])) {
        $target_dir = __DIR__ . "/../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image type
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $new_image = $target_file; // Update image path
            } else {
                echo "<script>alert('Error uploading image');</script>";
            }
        } else {
            echo "<script>alert('Invalid image file format (JPG, PNG, GIF allowed)');</script>";
        }
    }

    // Update product in the database
    $sql = "UPDATE products SET name=?, category=?, image=?, description=?, price=?, quantity=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $category, $new_image, $description, $price, $quantity, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">🛠️ Update Product</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category:</label>
                <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($product['category']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Image:</label>
                <br>
                <img src="<?= htmlspecialchars($product['image']); ?>" alt="Product Image" width="150">
                <input type="file" name="image" class="form-control mt-2">
            </div>

            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price:</label>
                <input type="number" name="price" class="form-control" step="0.01" value="<?= htmlspecialchars($product['price']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($product['quantity']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">💾 Save Changes</button>
        </form>

        <br>
        <a href="admin_dashboard.php" class="btn btn-secondary w-100">⬅️ Back to Dashboard</a>
    </div>
</body>


</html>