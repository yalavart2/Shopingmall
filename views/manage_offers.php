<?php
session_start();
require_once __DIR__ . "/../config/database.php"; // Ensure the correct path

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all products
$products_query = "SELECT product_id, name FROM products ORDER BY name ASC";
$products_result = $conn->query($products_query);

// Handle adding a new offer with product assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_offer'])) {
    $offer_name = $_POST['offer_name'];
    $discount = $_POST['discount'];
    $valid_until = $_POST['valid_until'];
    $product_id = $_POST['product_id'];

    // Insert offer into the 'offers' table
    $insert_offer_query = "INSERT INTO offers (offer_name, discount, valid_until) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_offer_query);
    $stmt->bind_param("sds", $offer_name, $discount, $valid_until);

    if ($stmt->execute()) {
        $offer_id = $stmt->insert_id;

        // Link the offer to a product
        $insert_product_offer_query = "INSERT INTO product_offers (product_id, offer_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_product_offer_query);
        $stmt->bind_param("si", $product_id, $offer_id);
        $stmt->execute();

        echo "<script>alert('Offer added successfully!'); window.location='manage_offers.php';</script>";
    } else {
        echo "<script>alert('Error adding offer.');</script>";
    }
}

// Handle updating an offer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_offer'])) {
    $offer_id = $_POST['offer_id'];
    $offer_name = $_POST['offer_name'];
    $discount = $_POST['discount'];
    $valid_until = $_POST['valid_until'];

    $update_query = "UPDATE offers SET offer_name = ?, discount = ?, valid_until = ? WHERE offer_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sdsi", $offer_name, $discount, $valid_until, $offer_id);

    if ($stmt->execute()) {
        echo "<script>alert('Offer updated successfully!'); window.location='manage_offers.php';</script>";
    } else {
        echo "<script>alert('Error updating offer.');</script>";
    }
}

// Handle deleting an offer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_offer'])) {
    $offer_id = $_POST['offer_id'];

    $delete_query = "DELETE FROM offers WHERE offer_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $offer_id);

    if ($stmt->execute()) {
        echo "<script>alert('Offer deleted successfully!'); window.location='manage_offers.php';</script>";
    } else {
        echo "<script>alert('Error deleting offer.');</script>";
    }
}

// Fetch all offers with their assigned products
$query = "
    SELECT o.offer_id, o.offer_name, o.discount, o.valid_until, 
           p.name AS product_name, po.product_id, p.price,
           (p.price - (p.price * o.discount / 100)) AS discounted_price
    FROM offers o
    LEFT JOIN product_offers po ON o.offer_id = po.offer_id
    LEFT JOIN products p ON po.product_id = p.product_id
    ORDER BY o.valid_until DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #d5f5e3;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
            margin-top: 30px;
            padding-bottom: 60px; /* Ensures space for footer */
        }
        .table {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .navbar {
            background: #007bff;
            padding: 10px;
        }
        .navbar a {
            color: white;
            font-size: 32px;
            text-decoration: none;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .sub-menu {
            background: #d7dbdd;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .sub-menu a {
            color: #007bff;
            margin: 0 15px;
            font-size: 16px;
            text-decoration: none;
            font-weight: bold;
        }
        .sub-menu a:hover {
            text-decoration: underline;
        }
        .footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
            position: relative;
        }
        .logout-btn {
            float: right;
            margin-right: 20px;
            margin-top: -5px;
            font-size: 14px;
            padding: 5px 10px;
            color: white;  
            background-color: #d7dbdd;
            border: none;  
            border-radius: 5px;  
        }

        .logout-btn:hover {
            background-color: #CD5C5C;  
            text-decoration: none;  
        }
    </style>

</head>
<body>

<!-- Navigation Bar -->
<div class="navbar text-center">
    <a href="admin_dashboard.php">Admin Dashboard</a>
</div>

<!-- Sub Menu -->
<div class="sub-menu">
    <a href="admin_dashboard.php">Home</a>
    <a href="add_product.php">Add Product</a>
    <a href="manage_orders.php">Manage Orders</a>
    <a href="manage_offers.php">Manage Offers</a>
    <a href="manage_events.php">Manage Events</a>
    <a href="manage_dining.php">Manage Dining</a>
    <a href="logout.php" class="btn btn-danger btn-sm logout-btn">Logout</a>
</div>

<div class="container">
    <h2 class="text-center">Manage Offers</h2>

    <!-- Add New Offer Form -->
    <div class="card p-3 mt-4">
        <h4>Add New Offer</h4>
        <form method="POST">
            <div class="mb-2">
                <label class="form-label">Offer Name</label>
                <input type="text" name="offer_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Discount (%)</label>
                <input type="number" step="0.1" name="discount" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Valid Until</label>
                <input type="date" name="valid_until" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Select Product</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    <?php while ($product = $products_result->fetch_assoc()) { ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" name="add_offer" class="btn btn-primary">➕ Add Offer</button>
        </form>
    </div>

    <!-- Offers Table -->
    <table class="table table-striped table-bordered mt-4">
            <thead class="table-dark">
            <tr>
                <th>Offer ID</th>
                <th>Offer Name</th>
                <th>Discount (%)</th>
                <th>Valid Until</th>
                <th>Product</th>
                <th>Previous Price</th>
                <th>Discounted Price</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['offer_id'] ?></td>
                    <td><?= htmlspecialchars($row['offer_name']) ?></td>
                    <td><?= number_format($row['discount'], 2) ?>%</td>
                    <td><?= $row['valid_until'] ?></td>
                    <td><?= htmlspecialchars($row['product_name'] ?: 'No Product Assigned') ?></td>
                    <td>£<?= number_format($row['price'], 2) ?></td>
                    <td>£<?= number_format($row['discounted_price'], 2) ?></td>
                    <td>
                        <!-- Update Form -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="offer_id" value="<?= $row['offer_id'] ?>">
                            <input type="text" name="offer_name" value="<?= htmlspecialchars($row['offer_name']) ?>" class="form-control d-inline w-50">
                            <input type="number" step="0.1" name="discount" value="<?= $row['discount'] ?>" class="form-control d-inline w-25">
                            <input type="date" name="valid_until" value="<?= $row['valid_until'] ?>" class="form-control d-inline w-25">
                            <button type="submit" name="update_offer" class="btn btn-warning btn-sm">✏️ Update</button>
                        </form>

                        <!-- Delete Form -->
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this offer?');">
                            <input type="hidden" name="offer_id" value="<?= $row['offer_id'] ?>">
                            <button type="submit" name="delete_offer" class="btn btn-danger btn-sm">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

</div>

<div class="footer">
    &copy; <?php echo date("Y"); ?> Shopping Mall Admin Panel. All Rights Reserved.
</div>

</body>
</html>
