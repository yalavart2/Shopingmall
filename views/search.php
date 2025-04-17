<?php
session_start();
require_once __DIR__ . "/../config/database.php"; // Include database connection

$pageTitle = "Search Results";

// Fetch the search query from the URL
$search_query = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch the products based on the search query
$sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = "%" . $search_query . "%"; // Wildcards for searching
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    die("Error fetching products: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Shopping Mall - Search'; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Sticky Footer CSS */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
    </style>
</head>
<body>

<!-- Include header.php here -->
<?php include __DIR__ . '/header.php'; ?>

<main>
    <!-- Search Results Section -->
    <div class="container mt-5">
        <h2 class="text-center">Search Results for: "<?= htmlspecialchars($search_query); ?>"</h2>

        <?php if (!empty($products)): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <img src="<?= !empty($product['image']) ? 'uploads/' . $product['image'] : 'uploads/default.png'; ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?= htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']); ?></p>
                                <p class="price">$<?= number_format($product['price'], 2); ?></p>
                                <a href="products.php?product_id=<?= $product['product_id']; ?>" 
                                   class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No products found for your search.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Include footer.php here -->
<?php include __DIR__ . '/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
