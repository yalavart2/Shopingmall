<?php
require_once __DIR__ . '/../config/database.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = intval($_POST['event_id']);
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $num_participants = intval($_POST['num_participants']);

    // Validate input
    if (empty($customer_name) || empty($customer_email) || $num_participants < 1) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields correctly."]);
        exit;
    }

    // Insert booking into database
    $query = "INSERT INTO bookings (event_id, customer_name, customer_email, num_participants) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("issi", $event_id, $customer_name, $customer_email, $num_participants);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Booking successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database execution error: " . $stmt->error]);
    }

    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}
?>
