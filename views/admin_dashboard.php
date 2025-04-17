<?php
session_start();
include dirname(__DIR__) . '/config/database.php';

// Restrict access to logged-in admins only
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== "Administrator") {
    header("Location: admin_login.php");
    exit;
}

// Fetch admin details
$admin_name = $_SESSION['admin_name'];

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shopping Mall</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #d5f5e3;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .dashboard-container {
            margin: 20px auto;
            max-width: 1100px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .products-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-card {
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #fff;
        }
        .product-card:hover {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
            border-radius: 5px;
            display: block; 
            margin-bottom: 10px; 
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
    <a href="admin_login.php" class="btn btn-danger btn-sm logout-btn">Logout</a>
</div>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <h2 class="text-center">Welcome, <?= htmlspecialchars($admin_name); ?></h2>
    <p class="text-center">Manage products in the shopping mall.</p>

    <!-- Product Listing -->
    <div class="products-container">
        <?php while ($row = $result->fetch_assoc()) { 
            // Ensure the image path is valid
            $imagePath = !empty($row['image']) ? $row['image'] : 'uploads/default.png'; 
            ?>
            <div class="product-card" data-id="<?= $row['product_id']; ?>" data-name="<?= htmlspecialchars($row['name']); ?>" 
                 data-category="<?= htmlspecialchars($row['category']); ?>" data-description="<?= htmlspecialchars($row['description']); ?>" 
                 data-price="<?= $row['price']; ?>" data-quantity="<?= $row['quantity']; ?>" 
                 data-image="<?= $imagePath; ?>">
                <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                <h5><?= htmlspecialchars($row['name']); ?></h5>
                <p><strong>Price:</strong> $<?= htmlspecialchars($row['price']); ?></p>
            </div>
        <?php } ?>
    </div>
</div>

<div class="footer">
    &copy; <?php echo date("Y"); ?> Shopping Mall Admin Panel. All Rights Reserved.
</div>

<!-- JavaScript -->
<script>
    document.querySelectorAll(".product-card").forEach(card => {
        card.addEventListener("click", function () {
            let productId = this.dataset.id;
            let productName = this.dataset.name;
            let productPrice = this.dataset.price;
            let productImage = this.dataset.image;
            let productDescription = this.dataset.description;

            Swal.fire({
                title: productName,
                text: productDescription,
                imageUrl: productImage,
                imageWidth: 200,
                imageHeight: 200,
                showCancelButton: true,
                confirmButtonText: "Update",
                cancelButtonText: "Delete",
                confirmButtonColor: "#007bff",
                cancelButtonColor: "#dc3545",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `update_product.php?product_id=${productId}`;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    deleteProduct(productId);
                }
            });
        });
    });

    function deleteProduct(productId) {
        Swal.fire({
            title: "Are you sure?",
            text: "This product will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Delete"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("delete_product.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Deleted!", data.message, "success").then(() => location.reload());
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch(error => {
                    Swal.fire("Error!", "Failed to delete product.", "error");
                    console.error("Error:", error);
                });
            }
        });
    }
</script>

</body>
</html>
