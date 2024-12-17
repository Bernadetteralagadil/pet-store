<?php
// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

// Get accountID from query parameter
$accountID = isset($_GET['accountID']) ? $_GET['accountID'] : null;

if (!$accountID) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Account ID is required"]);
    exit();
}

$stmt = $conn->prepare("SELECT cartID, description, price, quantity, totalPrice FROM addtocart WHERE accountID = ?");
$stmt->bind_param("i", $accountID);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

if ($cartItems) {
    echo json_encode(["success" => true, "items" => $cartItems]); // Fixed key to 'items'
} else {
    echo json_encode(["success" => false, "message" => "No items found for this account"]);
}


$conn->close();
?>
