<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Function to fetch product data from the database
function fetchProducts($conn) {
    $query = "SELECT productID, thumbnail, categories, sub_categories, name, description, price, stock, status FROM product";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Convert the BLOB to Base64
        if ($row['thumbnail']) {
            $row['thumbnail'] = base64_encode($row['thumbnail']); // Convert the BLOB to Base64 string
        }
        $products[] = $row;
    }
    return $products;
}

// Fetch the product data
$products = fetchProducts($conn);

// Send the data as JSON
if (count($products) > 0) {
    echo json_encode(["success" => true, "products" => $products]);
} else {
    echo json_encode(["success" => false, "message" => "No products found."]);
}

// Close database connection
$conn->close();
?>
