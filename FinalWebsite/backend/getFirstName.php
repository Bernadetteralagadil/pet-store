<?php
header("Access-Control-Allow-Origin: *");  
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); 

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
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

$accountID = isset($_GET['accountID']) ? $_GET['accountID'] : null;

if (!$accountID) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Account ID is required."]);
    exit();
}

$stmt = $conn->prepare("SELECT firstName FROM account WHERE accountID = ?");
$stmt->bind_param("i", $accountID);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName);

if ($stmt->fetch()) {
    echo json_encode(["success" => true, "firstName" => $firstName]);
} else {
    echo json_encode(["success" => false, "message" => "No user found with that account ID."]);
}

$conn->close();
?>
