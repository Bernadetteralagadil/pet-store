<?php
// Allow access from any origin (you can restrict this to specific domains for production)
header("Access-Control-Allow-Origin: *"); // or specify your frontend URL, e.g. "http://127.0.0.1:5500"
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

// Handle OPTIONS preflight request (this is triggered by the browser before sending the actual request)
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
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Retrieve input values
$accountID = $data['accountID'] ?? null;
$description = $data['description'] ?? null;
$quantity = $data['quantity'] ?? null;

// Check for missing values
if (!$accountID || !$description || $quantity === null) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Check if quantity is 0 (delete the item)
if ($quantity === 0) {
    $stmt = $conn->prepare("DELETE FROM addtocart WHERE accountID = ? AND description = ?");
    $stmt->bind_param("is", $accountID, $description);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Cart item deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete cart item"]);
    }
} else {
    // Fetch the price of the item
    $priceQuery = $conn->prepare("SELECT price FROM addtocart WHERE accountID = ? AND description = ?");
    $priceQuery->bind_param("is", $accountID, $description);
    $priceQuery->execute();
    $result = $priceQuery->get_result();

    if ($row = $result->fetch_assoc()) {
        $price = $row['price'];
        $totalPrice = $price * $quantity;

        // Update the quantity and totalPrice
        $updateStmt = $conn->prepare("UPDATE addtocart SET quantity = ?, totalPrice = ? WHERE accountID = ? AND description = ?");
        $updateStmt->bind_param("idis", $quantity, $totalPrice, $accountID, $description);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Cart item updated successfully",
                "quantity" => $quantity,
                "totalPrice" => $totalPrice
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update cart item"]);
        }

        $updateStmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Item not found"]);
    }
    $priceQuery->close();
}

$conn->close();
?>
