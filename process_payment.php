<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    die("Cart is empty");
}

// 1. Fix Undefined "method" - Use fallback if not selected
$method = $_POST['method'] ?? 'Unknown Method';
$user_id = $_SESSION['user']['id'];

// Get products from database based on cart IDs
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Calculate total with "quantity" safety check
$total = 0;
foreach ($products as $p) {
    $item = $cart[$p['id']];
    
    // If $item is an array, look for 'quantity'. If it's a number, use it directly.
    $qty = (is_array($item) && isset($item['quantity'])) ? $item['quantity'] : $item;
    
    // Convert to numbers to ensure math works
    $total += (int)$qty * (float)$p['price'];
}

// Save main order
// Save main order (WITHOUT payment_method or status)
$orderStmt = $conn->prepare("
    INSERT INTO orders (user_id, total)
    VALUES (?, ?)
");
$orderStmt->execute([$user_id, $total]);$orderStmt->execute([$user_id, $total, $method]);

$order_id = $conn->lastInsertId();

// 3. Save order items with "quantity" safety check
foreach ($products as $p) {
    $item = $cart[$p['id']];
    
    // Repeat safety check for quantity
    $qty = (is_array($item) && isset($item['quantity'])) ? $item['quantity'] : $item;

    $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ")->execute([
        $order_id, 
        $p['id'], 
        (int)$qty, 
        $p['price']
    ]);
}

// Clear cart after successful processing
unset($_SESSION['cart']);

echo "<h2>Payment successful via " . htmlspecialchars($method) . " 🎉</h2>";
echo "<p>Your order #$order_id has been placed successfully.</p>";
?>