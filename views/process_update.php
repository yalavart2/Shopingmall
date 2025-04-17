<?php
session_start();
include dirname(__DIR__) . '/config/database.php';

header("Content-Type: application/json");

// Restrict access to logged-in admins only
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access!"]);
    exit;
}

// Validate input fields
if (!isset($_POST['product_id'], $_POST['name'], $_POST['category'], $_POST['price'], $_POST['quantity'], $_POST['description'])) {
    echo json_encode(["success" => false, "message" => "Invalid request! Data missing."]);
    exit;
}

$product_id = trim($_POST['product_id']);
$name = trim($_POST['name']);
$category = trim($_POST['category']);
$price = floatval($_POST['price']);
$quantity = intval($_POST['quantity']);
$description = trim($_POST['description']);

// Fetch current product image from the database
$sql = "SELECT image FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Product not found."]);
    exit;
}

$product = $result->fetch_assoc();
$current_image = $product['image'];
$new_image = $current_image;

// Handle image upload if a new file is provided
if (!empty($_FILES['image']['name'])) {
    $target_dir = __DIR__ . "/../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $_FILES['image']['size'];

    // Validate image type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        echo json_encode(["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."]);
        exit;
    }

    // Check file size (Max 5MB)
    if ($file_size > 5 * 1024 * 1024) {
        echo json_encode(["success" => false, "message" => "File size exceeds 5MB limit."]);
        exit;
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $new_image = "uploads/" . basename($target_file);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image."]);
        exit;
    }
}

// Update product details in the database
$sql = "UPDATE products SET name = ?, category = ?, price = ?, quantity = ?, description = ?, image = ? WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdiiss", $name, $category, $price, $quantity, $description, $new_image, $product_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Product updated successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update product: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>