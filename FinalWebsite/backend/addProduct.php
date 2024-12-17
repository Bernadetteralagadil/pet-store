<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

// Check if all required fields are provided
if (
    isset($_POST['product_name']) &&
    isset($_POST['product_category']) &&
    isset($_POST['product_description']) &&
    isset($_POST['price']) &&
    isset($_POST['stock'])
) {
    $productName = $_POST['product_name'];
    $productCategory = $_POST['product_category'];
    $productSubCategory = isset($_POST['product_sub_category']) ? $_POST['product_sub_category'] : '';
    $productDescription = $_POST['product_description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $status = $stock > 0 ? 'In Stock' : 'Out of Stock';

    // Handle product image upload
    $thumbnailPath = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $fileName = uniqid() . '-' . basename($_FILES['product_image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
            $thumbnailPath = $uploadFile; // Store the file path in the database
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to upload image"]);
            exit();
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO product 
        (thumbnail, categories, sub_categories, name, description, price, stock, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssis",
        $thumbnailPath,
        $productCategory,
        $productSubCategory,
        $productName,
        $productDescription,
        $price,
        $stock,
        $status
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to add product: " . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "All required fields must be provided"]);
}

$conn->close();
?>
