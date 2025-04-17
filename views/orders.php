<?php
include 'database.php';
session_start();
$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->query("INSERT INTO orders (user_id, total_price) VALUES ((SELECT id FROM users WHERE email='$user'), (SELECT SUM(products.price * cart.quantity) FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = (SELECT id FROM users WHERE email='$user')))");
    $conn->query("DELETE FROM cart WHERE user_id = (SELECT id FROM users WHERE email='$user')");
    echo "Order placed successfully!";
}

$order_history = $conn->query("SELECT * FROM orders WHERE user_id = (SELECT id FROM users WHERE email='$user')");
?>
<h2>Your Orders</h2>
<ul>
    <?php while ($order = $order_history->fetch_assoc()): ?>
        <li>Order #<?= $order['id'] ?> - Total: $<?= $order['total_price'] ?></li>
    <?php endwhile; ?>
</ul>
<form method="post">
    <button type="submit">Place Order</button>
</form>

