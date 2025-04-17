<?php
session_start();
require_once __DIR__ . "/../config/database.php"; 

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['order_status'];

    $update_query = "UPDATE customer_orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully!'); window.location='manage_orders.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status.');</script>";
    }
}

// Handle order deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    $delete_query = "DELETE FROM customer_orders WHERE order_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order has been deleted.'); window.location='manage_orders.php';</script>";
    } else {
        echo "<script>alert('Failed to delete order.');</script>";
    }
}

// Fetch all orders
$query = "SELECT * FROM customer_orders ORDER BY order_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#007bff">
    <title>Admin Dashboard - Manage Orders</title>

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
            padding-bottom: 60px; 
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

<div class="container mt-4">
    <h2 class="text-center">Manage Orders</h2>
    
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td><?php echo htmlspecialchars($row['state']); ?></td>
                    <td><?php echo htmlspecialchars($row['zip']); ?></td>
                    <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                    <td><?php echo strtoupper($row['payment_method']); ?></td>
                    <td>
                        <form method="POST" class="d-flex update-form" onsubmit="return confirmUpdate(this);">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <select name="order_status" class="form-select me-2">
                                <option value="Pending" <?php echo ($row['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?php echo ($row['order_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="Shipped" <?php echo ($row['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?php echo ($row['order_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?php echo ($row['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary btn-sm">✅ Update</button>
                        </form>
                    </td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirmDelete(this);">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">🗑️ Delete</button>
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