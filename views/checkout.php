<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user role
$sql = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'Customer') {
    header("Location: unauthorized.php");
    exit();
}

// Fetch cart items
$sql = "SELECT 
            c.cart_id,
            c.product_id,
            p.name AS product_name,
            p.image AS product_image,
            p.price AS default_price,
            c.discounted_price,
            c.quantity
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

// If cart is empty, redirect to cart page
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

$pageTitle = "Checkout";
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 rounded shadow">
            <h2 class="text-center">Checkout</h2>

            <form id="checkout-form">
                <div class="row">
                    <!-- Shipping Information -->
                    <div class="col-md-6">
                        <h4>Shipping Details</h4>
                        <div class="mb-3">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="mb-3">
                            <label for="state">State</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                        </div>
                        <div class="mb-3">
                            <label for="zip">ZIP Code</label>
                            <input type="text" class="form-control" id="zip" name="zip" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-md-6">
                        <h4>Order Summary</h4>
                        <ul class="list-group mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><?= htmlspecialchars($item['product_name']) ?> (Qty: <?= $item['quantity'] ?>)</span>
                                    <span>$<?= number_format(($item['discounted_price'] ?? $item['default_price']) * $item['quantity'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>$<?= number_format($total_price, 2) ?></strong>
                            </li>
                        </ul>

                        <!-- Payment Method -->
                        <h4>Payment Method</h4>
                        <select class="form-control mb-3" name="payment_method" id="payment_method" required>
                            <option value="cod">Cash on Delivery</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="paypal">PayPal</option>
                        </select>

                        <button type="submit" class="btn btn-primary w-100">Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Checkout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("checkout-form").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("process_checkout.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            Swal.fire("Success", "Your order has been placed!", "success");
            setTimeout(() => { window.location.href = "order_details.php?order_id=" + data.order_id; }, 2000);
        } else {
            Swal.fire("Error", data.message, "error");
        }
    })
    .catch(() => {
        Swal.fire("Error", "An unexpected error occurred!", "error");
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
