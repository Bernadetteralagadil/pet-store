<?php
// fetchOrder.php

// Allow CORS from any origin
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);  // End the request early for preflight checks
}

header('Content-Type: application/json');

// Database Connection
$host = "localhost";
$dbname = "petshop";
$user = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Query to fetch orders with firstName and lastName from the account table
$query = "
    SELECT o.orderID, o.accountID, o.description, o.price, o.quantity, o.totalPrice, o.status, 
           a.firstName, a.lastName
    FROM orders o
    JOIN account a ON o.accountID = a.accountID
";
$stmt = $conn->prepare($query);
$stmt->execute();

// Fetch all orders
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the orders as JSON
echo json_encode(['success' => true, 'orders' => $orders]);
?>
