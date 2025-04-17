<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT c.product_id, p.name, p.price AS default_price, c.discounted_price, c.quantity 
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $price = ($row['discounted_price'] !== null) ? $row['discounted_price'] : $row['default_price'];
    $total = $price * $row['quantity'];
    $total_price += $total;
    $cart_items[] = $row;
}

// If cart is empty, return error
if (empty($cart_items)) {
    echo json_encode(["status" => "error", "message" => "Your cart is empty."]);
    exit();
}

// Get shipping and payment details from POST request
$full_name = $_POST['full_name'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$payment_method = $_POST['payment_method'];

$order_status = "Pending"; // Default order status
$order_date = date("Y-m-d H:i:s");

// Start transaction
$conn->begin_transaction();

try {
    // Insert into `customer_orders`
    $sql = "INSERT INTO customer_orders (user_id, full_name, address, city, state, zip, phone, email, total_price, payment_method, order_status, order_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssdsss", $user_id, $full_name, $address, $city, $state, $zip, $phone, $email, $total_price, $payment_method, $order_status, $order_date);
    $stmt->execute();

    $order_id = $stmt->insert_id; // Get the last inserted order ID

    // Insert each cart item into `order_items`
    $sql = "INSERT INTO order_items (order_id, user_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($cart_items as $item) {
        $price = ($item['discounted_price'] !== null) ? $item['discounted_price'] : $item['default_price'];
        $stmt->bind_param("iisid", $order_id, $user_id, $item['product_id'], $item['quantity'], $price);
        $stmt->execute();
    }

    // Clear cart after successful order placement
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Return success response
    echo json_encode(["status" => "success", "order_id" => $order_id]);
} catch (Exception $e) {
    $conn->rollback(); // Rollback if error occurs
    echo json_encode(["status" => "error", "message" => "Error processing order: " . $e->getMessage()]);
}
exit();
?>
