<?php
// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->firstName, $data->lastName, $data->gender, $data->birthDate, $data->email, $data->password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO account (firstName, lastName, gender, birthdate, email, password) VALUES (?, ?, ?, ?, ?, ?)");
$hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
$stmt->bind_param("ssssss", $data->firstName, $data->lastName, $data->gender, $data->birthDate, $data->email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Account created successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to create account."]);
}

$conn->close();
?>
