<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/database.php"; // Include database connection

$latest_order_id = null;

// If user is logged in, fetch their latest order ID
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT order_id FROM customer_orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latest_order_id = $row['order_id'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Shopping Mall'; ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #343a40;
            padding: 15px;
        }
        .navbar-brand img {
            height: 50px;
        }
        .nav-link {
            color: white !important;
            font-size: 16px;
            margin-right: 15px;
        }
    </style>
</head>
<body>

    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">
                <img src="uploads/AB Shopping Mall.png" alt="Mall Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="dining.php">Dining</a></li>
                    <li class="nav-item"><a class="nav-link" href="offers.php">Offers</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>

                    <?php if (isset($_SESSION['user_id']) && $latest_order_id): ?>
                        <li class="nav-item"><a class="nav-link" href="order_details.php?order_id=<?= $latest_order_id; ?>">Order Details</a></li>
                    <?php endif; ?>
                </ul>
               <!-- Search Form -->
<form class="d-flex" action="search.php" method="GET">
    <input class="form-control me-2" type="search" name="query" placeholder="Search" required>
    <button class="btn btn-light" type="submit">Search</button>
</form>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="dropdown ms-3">
                        <button class="btn btn-warning dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['user_name']); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else : ?>
                    <a href="login.php" class="btn btn-warning ms-3">Login / Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
