<?php
session_start();
require "config/db.php";

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    die("Cart is empty");
}

// 1. Collect Form Data
$name    = $_POST['name'];
$phone   = $_POST['phone'];
$address = $_POST['address'];
$user_id = $_SESSION['user']['id'] ?? null; // Get user ID from session

// 2. Fetch product details to calculate a secure total
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($products as $p) {
    $qty = $cart[$p['id']];
    $total += $qty * $p['price'];
}

try {
    // START TRANSACTION (Keeps data safe)
    $conn->beginTransaction();

    // 3. INSERT INTO ORDERS (Run this ONLY ONCE)
    $orderSql = "INSERT INTO orders (user_id, customer_name, phone, address, total) VALUES (?, ?, ?, ?, ?)";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->execute([$user_id, $name, $phone, $address, $total]);

    // Get the ID of the order we just created
    $order_id = $conn->lastInsertId();

    // 4. INSERT ORDER ITEMS (Loop through products here)
    $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemSql);

    foreach ($products as $p) {
        $qty = $cart[$p['id']];
        $itemStmt->execute([$order_id, $p['id'], $qty, $p['price']]);
    }

    // Commit the changes to the database
    $conn->commit();

    // 5. Clear cart and show success
    unset($_SESSION['cart']);
    echo "<h2>Order placed successfully 🎉</h2>";

} catch (Exception $e) {
    // If anything goes wrong, undo everything
    $conn->rollBack();
    echo "Failed to place order: " . $e->getMessage();
}