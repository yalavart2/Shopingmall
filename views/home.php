<?php
session_start();
require_once __DIR__ . "/../config/database.php";

$pageTitle = "Shopping Mall - Home";

// Fetch products from the database
$query = "SELECT * FROM products LIMIT 6";
$result = $conn->query($query);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    die("Error fetching products: " . $conn->error);
}

// Fetch offers with product details
$sql = "SELECT 
            p.product_id, 
            p.name AS product_name, 
            p.image AS product_image, 
            p.price AS original_price, 
            o.offer_name, 
            o.discount, 
            o.valid_until,
            (p.price - (p.price * o.discount / 100)) AS discounted_price
        FROM product_offers po
        JOIN products p ON po.product_id = p.product_id
        JOIN offers o ON po.offer_id = o.offer_id
        ORDER BY o.valid_until ASC";

$offers = $conn->query($sql);

// Include header
include __DIR__ . '/header.php';
?>

<style>
/* General Page Styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #76d7c4;
}

/* Hero Section */
.hero-section {
    background: url('mall-banner.jpg') no-repeat center center/cover;
    color: black;
    text-align: center;
    padding: 80px 20px;
}

.hero-section h1 {
    font-size: 3rem;
    font-weight: bold;
}

.hero-section p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.btn-custom {
    font-size: 1.1rem;
    padding: 10px 20px;
}

/* Product Section */
.container {
    margin-top: 50px;
}
.btnn{
    margin-top: 10px;
    margin-bottom: 15px;
    background-color: #3498db;
    color: white;
}

/* Product Card Styling */
.product-card {
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    transition: 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: scale(1.05);
}

/* Ensure all product images have the same size */
.product-image {
    width: 100%;
    height: 250px;  /* Adjust the height as needed */
    object-fit: cover; /* Ensures the image covers the entire area without distortion */
}

/* Ensure equal card height */
.card-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Price Styling */
.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}
</style>

<!-- Hero Section --> 
<div class="hero-section">
    <h1>Your One-Stop Shopping Destination</h1>
    <p>Explore top brands, exciting events, and amazing offers</p>
    <a href="offers.php" class="btn btn-success btn-custom">View Offers</a>
</div>

<!-- Featured Offers -->
<div class="container mt-4">
    <h2 class="text-center">Exclusive Deals & Discounts</h2>
    <p class="text-center">Explore the best offers available at our shopping mall!</p>
    <div class="row">
        <?php if ($offers->num_rows > 0): ?>
            <?php while ($row = $offers->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card offer-card">
                        <img src="<?= htmlspecialchars($row['product_image']) ?>" class="card-img-top product-image" alt="Offer Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                            <p class="card-text">
                                <span class="original-price text-muted" style="text-decoration: line-through; font-size: 1rem;">
                                    $<?= number_format($row['original_price'], 2) ?>
                                </span>
                                <span class="discounted-price text-danger fw-bold" style="font-size: 1.3rem;">
                                    $<?= number_format($row['discounted_price'], 2) ?>
                                </span>
                            </p>
                            <p>Discount: <strong><?= $row['discount'] ?>%</strong></p>
                            <p class="offer-expiry">Valid Until: <?= date("F j, Y", strtotime($row['valid_until'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No offers available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Available Products -->
<div class="container mt-5">
    <h2 class="text-center">Available Products</h2>

    <div class="text-center">
        <button id="loadMoreBtn" class="btnn btn-primary">Load All Products</button>
    </div>

    <div class="row" id="product-list">
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $product) { 
                // Ensure the image path is valid
                $imagePath = !empty($product['image']) ? 'uploads/' . $product['image'] : 'uploads/default.png';
            ?>
                <div class="col-md-4 mb-4 product-card">
                    <div class="card">
                        <img src="<?= htmlspecialchars($imagePath); ?>" 
                             class="card-img-top product-image" 
                             alt="<?= htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']); ?></p>
                            <p class="price">$<?= number_format($product['price'], 2); ?></p>
                            <a href="products.php?product_id=<?= htmlspecialchars($product['product_id']); ?>" 
                               class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php else : ?>
            <p class="text-center">No products available at the moment.</p>
        <?php endif; ?>
    </div>
    <div id="more-products" class="row"></div>
</div>

<script>
document.getElementById('loadMoreBtn').addEventListener('click', function() {
    // Disable the button to prevent multiple clicks
    this.disabled = true;

    // Fetch all products via AJAX
    fetch('load_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let moreProducts = document.getElementById('more-products');
                data.products.forEach(product => {
                    let productCard = `
                        <div class="col-md-4 mb-4">
                            <div class="card product-card">
                                <img src="uploads/${product.image}" class="card-img-top product-image" alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text">${product.description}</p>
                                    <p class="price">$${product.price}</p>
                                    <a href="products.php?product_id=${product.product_id}" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    `;
                    moreProducts.innerHTML += productCard;
                });
                moreProducts.style.display = 'block'; // Show all products
            } else {
                alert('Failed to load products');
            }
        })
        .catch(error => {
            alert('An error occurred while loading products.');
            console.error(error);
        });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
