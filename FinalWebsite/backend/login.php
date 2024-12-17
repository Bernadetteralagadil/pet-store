<?php
header("Access-Control-Allow-Origin: *");  // Allow all domains (you can replace '*' with specific domain if needed)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow GET, POST, and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Allow these headers

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Respond successfully to the preflight request
    http_response_code(200);
    exit();
}

// Your existing login logic
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

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email, $data->password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit();
}

$email = $data->email;
$password = $data->password;

$stmt = $conn->prepare("SELECT accountID, email, password FROM account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($accountID, $dbEmail, $dbPassword);

if ($stmt->fetch()) {
    if (password_verify($password, $dbPassword)) {
        // Only return success status and accountID without a message
        echo json_encode(["success" => true, "accountID" => $accountID]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No user found with that email."]);
}

$conn->close();
?>
