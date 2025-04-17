<?php
session_start();
require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Offers - Shopping Mall";
include __DIR__ . '/header.php';

$offers = [];
$errorMessage = '';

try {
    $query = "SELECT 
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

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }
} catch (Exception $e) {
    $errorMessage = "Unable to fetch offers. Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            font-family: 'Arial', sans-serif;
        }
        .container {
            padding-top: 50px;
        }
        .offer-card {
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
        }
        .offer-card:hover {
            transform: scale(1.05);
        }
        .offer-expiry {
            font-size: 14px;
            color: red;
        }
        .original-price {
            text-decoration: line-through;
            color: gray;
            font-size: 16px;
            margin-right: 10px;
        }
        .discounted-price {
            font-size: 18px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Exclusive Offers</h2>
    
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($offers)): ?>
            <?php foreach ($offers as $offer): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm offer-card">
                        <img src="<?= htmlspecialchars($offer['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($offer['product_name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($offer['product_name']) ?></h5>
                            <p class="card-text">
                                <span class="original-price">$<?= number_format($offer['original_price'], 2) ?></span>
                                <span class="discounted-price">$<?= number_format($offer['discounted_price'], 2) ?></span>
                            </p>
                            <p>Discount: <strong><?= $offer['discount'] ?>%</strong></p>
                            <p class="offer-expiry">Valid Until: <?= date("F j, Y", strtotime($offer['valid_until'])) ?></p>
                            <a href="add_offer_cart.php?product_id=<?= $offer['product_id'] ?>&discounted_price=<?= $offer['discounted_price'] ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No offers available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
