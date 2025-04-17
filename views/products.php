<?php
session_start();
require_once __DIR__ . "/../config/database.php"; 

// Check if product_id is set
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    die("Invalid product ID.");
}

$product_id = $_GET['product_id'];
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$pageTitle = $product['name'];
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars('uploads/' . basename($product['image'])) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <h4 class="text-success">Price: $<?= number_format($product['price'], 2) ?></h4>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <button class="btn btn-warning add-to-cart" data-product-id="<?= htmlspecialchars($product['product_id']) ?>">
                Add to Cart
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 for Popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".add-to-cart").addEventListener("click", function(event) {
        event.preventDefault();
        let productId = this.getAttribute("data-product-id");

        fetch("add_to_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "product_id=" + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: data.message
                });
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>

<style>
/* General Page Styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
}
.footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: #222;
        color: white;
        text-align: center;
        padding: 15px 0;
    }
</style>