<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['index'], $_POST['quantity'])) {
    $index = (int) $_POST['index'];
    $quantity = (int) $_POST['quantity'];

    if ($quantity < 1) {
        echo json_encode(["status" => "error", "message" => "Quantity must be at least 1"]);
        exit;
    }

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Item not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
