<?php
// Database configuration
$host = "localhost";
$user = "root"; // Default XAMPP MySQL user
$pass = ""; // Default XAMPP MySQL password (empty)
$dbname = "shopping_mall"; // Change to your actual database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
