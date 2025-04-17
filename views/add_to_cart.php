<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Check if product_id is received via POST
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
    exit;
}

$product_id = $_POST['product_id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Use session user_id or 0 for guest

// Check if the product is already in the cart
$sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItem = $result->fetch_assoc();

if ($cartItem) {
    // If the product exists in the cart, increase the quantity
    $newQuantity = $cartItem['quantity'] + 1;
    $updateSql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
    $updateStmt->execute();
} else {
    // If the product is not in the cart, insert a new row
    $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("is", $user_id, $product_id);
    $insertStmt->execute();
}


echo json_encode(["status" => "success", "message" => "Product added to cart."]);
?>
