<?php
session_start();
require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $quantity = max(1, (int) $_POST['quantity']); 

    // Update quantity in the cart
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();

    // Get updated product price
    $stmt = $conn->prepare("SELECT (COALESCE(discounted_price, price) * quantity) AS item_total, quantity FROM cart WHERE cart_id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $item_total = number_format($row['item_total'], 2);
    $updated_quantity = $row['quantity'];

    // Get new cart total
    $result = $conn->query("SELECT SUM((COALESCE(discounted_price, price) * quantity)) AS total FROM cart WHERE user_id = " . $_SESSION['user_id']);
    $cart_total = $result->fetch_assoc()['total'] ?? 0;

    echo json_encode([
        "status" => "success",
        "updated_quantity" => $updated_quantity,
        "new_item_total" => $item_total,
        "new_cart_total" => number_format($cart_total, 2)
    ]);
}
?>
