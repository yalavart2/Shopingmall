<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$sql = "SELECT * FROM customer_orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

// If order not found, redirect
if (!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch ordered items
$sql = "SELECT oi.product_id, p.name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

$pageTitle = "Order Details";
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 rounded shadow">
            <h2 class="text-center">Order Details</h2>

            <div class="mb-3">
                <strong>Order ID:</strong> #<?= $order['order_id'] ?><br>
                <strong>Order Date:</strong> <?= $order['order_date'] ?><br>
                <strong>Total Price:</strong> $<?= number_format($order['total_price'], 2) ?><br>
                <strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?><br>
                <strong>Status:</strong> <span class="badge bg-info"><?= $order['order_status'] ?></span>
            </div>

            <h4>Shipping Details</h4>
            <p>
                <strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?><br>
                <strong>Address:</strong> <?= htmlspecialchars($order['address']) ?><br>
                <strong>City:</strong> <?= htmlspecialchars($order['city']) ?><br>
                <strong>State:</strong> <?= htmlspecialchars($order['state']) ?><br>
                <strong>ZIP Code:</strong> <?= htmlspecialchars($order['zip']) ?><br>
                <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
            </p>

            <h4>Ordered Items</h4>
            <ul class="list-group">
                <?php while ($item = $items_result->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($item['name']) ?> (Qty: <?= $item['quantity'] ?>)</span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>

            <div class="mt-4 text-center">
                <a href="home.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
