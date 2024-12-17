<?php
// Set headers to allow cross-origin requests and specify content type as JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

// Create connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get the productID from the query string
$productID = isset($_GET['productID']) ? $_GET['productID'] : null;

if (!$productID) {
    echo json_encode(["success" => false, "message" => "Product ID is required"]);
    exit();
}

// SQL query to fetch product details by productID, including the BLOB data for thumbnail
$sql = "SELECT productID, thumbnail, categories, sub_categories, name, description, price, stock, status FROM product WHERE productID = ?";

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID); // "i" denotes the productID is an integer

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// After fetching the product from the database
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    // Ensure that thumbnail exists
    if ($product['thumbnail']) {
        // Encode thumbnail BLOB data as base64
        $product['thumbnail'] = base64_encode($product['thumbnail']);
    } else {
        $product['thumbnail'] = null;
    }

    // Return the product data in JSON format
    echo json_encode(["success" => true, "product" => $product]);
} else {
    // Return a message if no product is found
    echo json_encode(["success" => false, "message" => "Product not found"]);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
