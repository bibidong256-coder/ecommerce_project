<?php
session_start();
require "config/db.php";

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { die("Cart is empty"); }

$name    = $_POST['name'] ?? 'Guest';
$phone   = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$user_id = $_SESSION['user']['id'] ?? null; 

// 1. Calculate Total
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($products as $p) {
    $qty = is_array($cart[$p['id']]) ? $cart[$p['id']]['qty'] : $cart[$p['id']];
    $total += $qty * $p['price'];
}

// 2. Insert the Order (ONCE)
$sql = "INSERT INTO orders (user_id, customer_name, phone, address, total) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $name, $phone, $address, $total]);
$order_id = $conn->lastInsertId();

// 3. Insert the Items
$itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)";
$itemStmt = $conn->prepare($itemSql);

foreach ($products as $p) {
    $cartItem = $cart[$p['id']];
    $qty  = is_array($cartItem) ? $cartItem['qty'] : $cartItem;
    $size = is_array($cartItem) ? $cartItem['size'] : ''; // Capture the size!
    
    $itemStmt->execute([$order_id, $p['id'], $qty, $p['price'], $size]);
}

unset($_SESSION['cart']);
header("Location: orders.php?success=1"); // Redirect to see the result