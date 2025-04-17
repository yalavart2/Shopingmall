<?php
session_start();
require_once __DIR__ . "/../config/database.php";

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // 0 for guest users

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

// Store cart items
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$pageTitle = "Shopping Cart";
include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Sticky Footer CSS */
        html, body {
            height: 100%;
            margin: 0;
        }

        #content-container {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        footer {
            margin-top: auto; /* Push footer to the bottom */
        }

        .footer {
            font-size: 16px;
        }

        .footer-section {
            margin-bottom: 15px;
        }

        .footer-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer-section ul {
            padding: 0;
        }

        .footer-section ul li {
            list-style: none;
            margin: 5px 0;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }

        .footer-section ul li a:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }

        .footer-divider {
            background-color: rgba(255, 255, 255, 0.2);
            height: 1px;
            width: 100%;
            margin: 15px 0;
        }

        .footer-text {
            font-size: 14px;
            margin-top: 5px;
        }

        .social-icons img {
            width: 35px;
            height: 35px;
            margin: 0 10px;
            transition: transform 0.3s ease-in-out;
        }

        .social-icons img:hover {
            transform: scale(1.1);
        }

        .input-group input {
            border-radius: 5px 0 0 5px;
        }

        .input-group button {
            border-radius: 0 5px 5px 0;
        }

        @media (max-width: 768px) {
            .footer-section {
                text-align: center;
                margin-bottom: 20px;
            }

            .social-icons {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container mt-4" id="content-container">
    <h2 class="text-center">Your Shopping Cart</h2>
    <div id="cart-container" class="row">
        <div class="col-md-12">
            <?php if (!empty($cart_items)): ?>
                <div id="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div id="cart-item-<?= $item['cart_id'] ?>" class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?= htmlspecialchars('uploads/' . basename($item['product_image'])) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                                        <p class="card-text">
                                            <?php if ($item['discounted_price'] !== null): ?>
                                                <span class="text-muted text-decoration-line-through">$<?= number_format($item['default_price'], 2) ?></span>
                                                <span class="text-success fw-bold">$<?= number_format($item['discounted_price'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold">$<?= number_format($item['default_price'], 2) ?></span>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p class="card-text"><strong>Quantity:</strong> 
                                            <input type="number" class="form-control quantity-input" data-cart-id="<?= $item['cart_id'] ?>" value="<?= $item['quantity'] ?>" min="1" style="width: 80px; display: inline-block;">
                                        </p>

                                        <p class="card-text">
                                            <strong>Total:</strong> 
                                            <span class="total-price" data-cart-id="<?= $item['cart_id'] ?>" data-price="<?= ($item['discounted_price'] !== null) ? $item['discounted_price'] : $item['default_price'] ?>">
                                                <?php 
                                                    $price = ($item['discounted_price'] !== null) ? $item['discounted_price'] : $item['default_price'];
                                                    $total = $price * $item['quantity'];
                                                    echo "$" . number_format($total, 2);
                                                ?>
                                            </span>
                                        </p>

                                        <button class="btn btn-danger btn-sm remove-item-btn" data-cart-id="<?= $item['cart_id'] ?>">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Display Total Price -->
                <div id="cart-summary" class="text-center mt-4">
                    <h4>Total: <span id="cart-total">$<?= number_format(array_sum(array_map(fn($item) => ($item['discounted_price'] ?? $item['default_price']) * $item['quantity'], $cart_items)), 2) ?></span></h4>
                </div>

                <!-- Proceed to Buy -->
                <div id="checkout-container" class="text-center mt-4">
                    <a id="checkout-btn" href="checkout.php" class="btn btn-success btn-lg">Proceed to Buy</a>
                </div>

            <?php else: ?>
                <p class="text-center">Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Update quantity and total dynamically
document.querySelectorAll(".quantity-input").forEach(input => {
    input.addEventListener("input", function() {
        let cartId = this.getAttribute("data-cart-id");
        let newQuantity = parseInt(this.value);

        if (newQuantity < 1 || isNaN(newQuantity)) {
            this.value = 1;
            newQuantity = 1;
        }

        // Send request to update cart in the database
        fetch("update_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "cart_id=" + cartId + "&quantity=" + newQuantity
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // Update the input field with the correct quantity
                input.value = data.updated_quantity;

                // Update the total price for the item
                let priceElement = document.querySelector(".total-price[data-cart-id='" + cartId + "']");
                priceElement.innerText = "$" + data.new_item_total;

                // Update the overall cart total
                document.querySelector("#cart-total").innerText = "$" + data.new_cart_total;
            } else {
                alert("Failed to update quantity.");
            }
        })
        .catch(error => console.error("Error:", error));
    });
});

// Remove item from cart without refresh
document.querySelectorAll(".remove-item-btn").forEach(button => {
    button.addEventListener("click", function() {
        let cartId = this.getAttribute("data-cart-id");
        let cartItem = document.querySelector("#cart-item-" + cartId);

        fetch("remove_from_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "cart_id=" + cartId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                cartItem.remove();
                
                // Update total dynamically
                document.querySelector("#cart-total").innerText = "$" + data.new_cart_total;

                // If the cart is empty, update the UI
                if (data.cart_empty) {
                    document.querySelector("#cart-container").innerHTML = "<p class='text-center'>Your cart is empty.</p>";
                    document.querySelector("#checkout-btn").style.display = "none";
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>

</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>