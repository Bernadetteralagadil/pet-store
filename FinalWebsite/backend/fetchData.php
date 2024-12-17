<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Query for total orders
$sqlOrders = "SELECT COUNT(*) AS totalOrders FROM orders";
$resultOrders = $conn->query($sqlOrders);
$totalOrders = $resultOrders->fetch_assoc()['totalOrders'] ?? 0;

// Query for total dog products
$sqlDogProducts = "SELECT COUNT(*) AS totalDogs FROM product WHERE categories = 'Dog'";
$resultDogProducts = $conn->query($sqlDogProducts);
$totalDogs = $resultDogProducts->fetch_assoc()['totalDogs'] ?? 0;

// Query for total cat products
$sqlCatProducts = "SELECT COUNT(*) AS totalCats FROM product WHERE categories = 'Cat'";
$resultCatProducts = $conn->query($sqlCatProducts);
$totalCats = $resultCatProducts->fetch_assoc()['totalCats'] ?? 0;

// Query for total users
$sqlUsers = "SELECT COUNT(*) AS totalUsers FROM account";
$resultUsers = $conn->query($sqlUsers);
$totalUsers = $resultUsers->fetch_assoc()['totalUsers'] ?? 0;

// Query for deliveries grouped by month (Check if status is 'delivered' and date is valid)
$sqlDeliveries = "
    SELECT MONTH(date) AS deliveryMonth, COUNT(*) AS deliveries
    FROM orders
    WHERE status = 'delivered' AND date IS NOT NULL
    GROUP BY MONTH(date)
    ORDER BY deliveryMonth";
$resultDeliveries = $conn->query($sqlDeliveries);

// Log the raw result of the query for debugging purposes
if ($resultDeliveries->num_rows > 0) {
    while ($row = $resultDeliveries->fetch_assoc()) {
        error_log("Month: " . $row['deliveryMonth'] . " Deliveries: " . $row['deliveries']);
    }
} else {
    error_log("No deliveries found.");
}


// After fetching the deliveries data, log it for debugging
$deliveriesData = array_fill(0, 12, 0); // Initialize array for all months (0-11)

while ($row = $resultDeliveries->fetch_assoc()) {
    $month = (int)$row['deliveryMonth'] - 1; // Convert to 0-indexed
    $deliveriesData[$month] = (int)$row['deliveries'];
}

// Debug the deliveries data array
error_log("Deliveries Data: " . json_encode($deliveriesData));



echo json_encode([
    "success" => true,
    "data" => [
        "totalOrders" => $totalOrders,
        "totalDogs" => $totalDogs,
        "totalCats" => $totalCats,
        "totalUsers" => $totalUsers,
        "deliveriesData" => $deliveriesData  // Make sure this is populated correctly
    ]
]);



$conn->close();
?>
