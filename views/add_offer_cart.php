<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Check if product_id and discounted_price are set
if (isset($_GET['product_id']) && isset($_GET['discounted_price'])) {
    $product_id = $_GET['product_id'];
    $discounted_price = (float)$_GET['discounted_price'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // 0 for guests

    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT name, image FROM products WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $stmt->close();

        // Check if product is already in the cart for this user
        $checkStmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->bind_param("is", $user_id, $product_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Update quantity if product already in cart
            $cartItem = $checkResult->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + 1;
            $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
            $updateStmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert new product into cart
            $insertStmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, discounted_price) VALUES (?, ?, ?, ?)");
            $quantity = 1;
            $insertStmt->bind_param("isid", $user_id, $product_id, $quantity, $discounted_price);
            $insertStmt->execute();
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}

// Redirect to cart page
header("Location: cart.php");
exit;
?>
