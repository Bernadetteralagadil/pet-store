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

$orderID = $data['orderID'] ?? null;
$status = $data['status'] ?? null;
$deliveryDateTime = $data['deliveryDateTime'] ?? null;

// Validate input
if (!$orderID || !$status) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Prepare the SQL query to update the order status
if ($status === "delivered") {
    // If the status is 'DELIVERED', include the current date (without time)
    $deliveryDate = date("Y-m-d");  // Format: YYYY-MM-DD
    $stmt = $conn->prepare("UPDATE orders SET status = ?, date = ? WHERE orderID = ?");
    $stmt->bind_param("sss", $status, $deliveryDate, $orderID);
} else {
    // If status is not 'DELIVERED', just update the status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE orderID = ?");
    $stmt->bind_param("ss", $status, $orderID);
}

$stmt->execute();

// Check if any rows were affected
if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Order status updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "No changes made or order not found"]);
}

$stmt->close();
$conn->close();
?>
