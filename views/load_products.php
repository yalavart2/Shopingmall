<?php
require_once __DIR__ . "/../config/database.php";

// Fetch all products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo json_encode(['success' => false]);
    exit;
}

echo json_encode([
    'success' => true,
    'products' => $products
]);
