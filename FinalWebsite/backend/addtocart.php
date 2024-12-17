<?php
// addtocart.php

// Allow CORS from any origin (or specify your frontend domain)
header('Access-Control-Allow-Origin: http://127.0.0.1:5500'); // You can adjust the origin if needed
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
if (!isset($data['accountID'], $data['description'], $data['price'], $data['quantity'], $data['totalPrice'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$accountID = $data['accountID'];
$description = $data['description'];
$price = $data['price'];
$quantity = $data['quantity'];
$totalPrice = $data['totalPrice'];

try {
    // Insert into cart table
    $stmt = $conn->prepare("INSERT INTO addtocart (accountID, description, price, quantity, totalPrice) 
                            VALUES (:accountID, :description, :price, :quantity, :totalPrice)");
    $stmt->bindParam(':accountID', $accountID);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':totalPrice', $totalPrice);

    $stmt->execute();

    // Return Success Response
    echo json_encode(['success' => true, 'message' => 'Item added to cart successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to add item: ' . $e->getMessage()]);
}
?>
