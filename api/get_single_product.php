<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require "../config/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
    exit;
}

// Get main product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(["status" => "error", "message" => "Product not found"]);
    exit;
}

// Get additional images
$imgStmt = $conn->prepare(
    "SELECT image FROM product_images 
     WHERE product_id = ? 
     ORDER BY sort_order ASC"
);
$imgStmt->execute([$id]);
$extraImages = $imgStmt->fetchAll(PDO::FETCH_COLUMN); // returns plain array of image paths

$product['extra_images'] = $extraImages;

echo json_encode(["status" => "success", "data" => $product]);
?>