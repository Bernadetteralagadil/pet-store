<?php
// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");  // Make sure DELETE is allowed
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method Not Allowed"]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get the cartID from the URL
$cartID = $_GET['cartID'] ?? null;
$accountID = $_GET['accountID'] ?? null;

if (!$cartID || !$accountID) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing cartID or accountID parameter"]);
    exit();
}

// Prepare the DELETE statement to remove the item from the cart
$stmt = $conn->prepare("DELETE FROM addtocart WHERE cartID = ? AND accountID = ?");
$stmt->bind_param("ii", $cartID, $accountID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Cart item deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete cart item"]);
}

$conn->close();
