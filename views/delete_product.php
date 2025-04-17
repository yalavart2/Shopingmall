<?php
session_start();
include dirname(__DIR__) . '/config/database.php';

// Ensure only an admin can delete products
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== "Administrator") {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

// Get the raw JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    echo json_encode(["status" => "error", "message" => "Product ID is missing"]);
    exit;
}

$product_id = $data['product_id']; // No need for intval() since it's a string

// Fetch product image to delete it from the server
$sql = "SELECT image FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id); // Change "i" to "s" for string binding
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(["status" => "error", "message" => "Product not found"]);
    exit;
}

// Delete the product image if it's not the default one
if (!empty($product['image']) && file_exists($product['image']) && $product['image'] !== 'uploads/default.png') {
    unlink($product['image']);
}

// Delete the product from the database
$sql = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id); // Change "i" to "s" for string binding

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Product deletion failed. No rows affected."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
