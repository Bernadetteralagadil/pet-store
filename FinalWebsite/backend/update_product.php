<?php
// Allow access from any origin (adjust for production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$productID = $data['productID'] ?? null;
$productName = $data['name'] ?? null;
$categories = $data['categories'] ?? null;
$subCategories = $data['sub_categories'] ?? null;
$description = $data['description'] ?? null;
$price = $data['price'] ?? null;
$stock = $data['stock'] ?? null;

// Validate input
if (!$productID || !$productName || !$categories || !$price || $stock === null) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Update product in the database
$stmt = $conn->prepare("UPDATE product SET name = ?, categories = ?, sub_categories = ?, description = ?, price = ?, stock = ? WHERE productID = ?");
$stmt->bind_param("ssssdis", $productName, $categories, $subCategories, $description, $price, $stock, $productID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Product updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "No changes made or product not found"]);
}

$stmt->close();
$conn->close();
?>
