<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "../config/db.php";

// Read the ?page= param from the URL (defaults to 1 if not provided)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Validate: page must be a positive number
if ($page < 1) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid page number"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE page = ? ORDER BY id DESC");
    $stmt->execute([$page]);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        // Still return 200 but with empty array + a message
        echo json_encode([
            "status" => "empty",
            "message" => "No products found for page $page",
            "data" => []
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "page" => $page,
        "count" => count($products),
        "data" => $products
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>