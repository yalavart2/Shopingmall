<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $dining_id = $_POST['dining_id'];
    $tables_required = $_POST['tables_required'];
    $reservation_date = $_POST['reservation_date'];

    $sql = "INSERT INTO reservations (customer_name, customer_email, dining_id, tables_required, reservation_date) 
            VALUES ('$customer_name', '$customer_email', '$dining_id', '$tables_required', '$reservation_date')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Booking successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
}
?>
