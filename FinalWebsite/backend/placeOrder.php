<?php
// placeOrder.php

// Allow CORS from any origin (or specify your frontend domain)
header('Access-Control-Allow-Origin: http://127.0.0.1:5500'); // Adjust if needed
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');  // Allow specific methods
header('Access-Control-Allow-Headers: Content-Type');  // Allow specific headers

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

// Read Input JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validate Required Fields
if (!isset($data['accountID'], $data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$accountID = $data['accountID'];
$items = $data['items']; // Items array

// Insert each item into the orders table
foreach ($items as $item) {
    try {
        // Prepare SQL to insert order data
        $stmt = $conn->prepare("INSERT INTO orders (accountID, description, price, quantity, totalPrice, status) 
                                VALUES (:accountID, :description, :price, :quantity, :totalPrice, :status)");

        $stmt->bindParam(':accountID', $accountID);
        $stmt->bindParam(':description', $item['description']);
        $stmt->bindParam(':price', $item['price']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':totalPrice', $item['totalPrice']);
        $stmt->bindParam(':status', $status);

        $status = 'Processing'; // Set the order status to Processing
        $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
        exit();
    }
}

// Delete the items from the addtocart table for the accountID after successful order placement
try {
    $stmt = $conn->prepare("DELETE FROM addtocart WHERE accountID = :accountID");
    $stmt->bindParam(':accountID', $accountID);
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to clear cart: ' . $e->getMessage()]);
    exit();
}

// Return Success Response
echo json_encode(['success' => true, 'message' => 'Order placed successfully and cart cleared']);
?>
