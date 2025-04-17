<?php
session_start();
require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $conn->query("DELETE FROM cart WHERE cart_id = $cart_id");

    $result = $conn->query("SELECT COUNT(*) AS count FROM cart WHERE user_id = " . $_SESSION['user_id']);
    $is_empty = $result->fetch_assoc()['count'] == 0;

    echo json_encode(["status" => "success", "cart_empty" => $is_empty]);
}
?>
