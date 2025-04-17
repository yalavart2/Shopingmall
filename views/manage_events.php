<?php
session_start();
require_once __DIR__ . "/../config/database.php"; 

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle adding a new event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $insert_query = "INSERT INTO events (event_name, event_date, location, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssss", $event_name, $event_date, $location, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Event added successfully!'); window.location='manage_events.php';</script>";
    } else {
        echo "<script>alert('Error adding event.');</script>";
    }
}

// Handle updating an event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $update_query = "UPDATE events SET event_name = ?, event_date = ?, location = ?, description = ? WHERE event_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $event_name, $event_date, $location, $description, $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully!'); window.location='manage_events.php';</script>";
    } else {
        echo "<script>alert('Error updating event.');</script>";
    }
}

// Handle deleting an event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];

    $delete_query = "DELETE FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location='manage_events.php';</script>";
    } else {
        echo "<script>alert('Error deleting event.');</script>";
    }
}

// Fetch all events
$query = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>

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
    <h2 class="text-center">Manage Events</h2>

    <!-- Add New Event Form -->
    <div class="card p-3 mt-4">
        <h4>Add New Event</h4>
        <form method="POST">
            <div class="mb-2">
                <label class="form-label">Event Name</label>
                <input type="text" name="event_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Event Date</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" name="add_event" class="btn btn-primary">➕ Add Event</button>
        </form>
    </div>

    <!-- Events Table -->
    <table class="table table-striped table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>Event ID</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Location</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['event_id'] ?></td>
                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                    <td><?= $row['event_date'] ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <!-- Update Form -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="event_id" value="<?= $row['event_id'] ?>">
                            <input type="text" name="event_name" value="<?= htmlspecialchars($row['event_name']) ?>" class="form-control d-inline w-50">
                            <input type="date" name="event_date" value="<?= $row['event_date'] ?>" class="form-control d-inline w-25">
                            <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" class="form-control d-inline w-50">
                            <input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>" class="form-control d-inline w-50">
                            <button type="submit" name="update_event" class="btn btn-warning btn-sm">✏️ Update</button>
                        </form>

                        <!-- Delete Form -->
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <input type="hidden" name="event_id" value="<?= $row['event_id'] ?>">
                            <button type="submit" name="delete_event" class="btn btn-danger btn-sm">🗑️ Delete</button>
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
