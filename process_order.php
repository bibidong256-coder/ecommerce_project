<?php
session_start();
require "config/db.php";

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login to place an order");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: shop.php");
    exit;
}

try {
    $conn->beginTransaction();

    // 2. Calculate Total
    $total = 0;
    $items_to_save = [];
    
    // Fetch product details to get current prices
    foreach ($cart as $id => $details) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        $qty = is_array($details) ? $details['qty'] : $details;
        $size = is_array($details) ? $details['size'] : '';
        $price = $product['price'];
        
        $total += ($price * $qty);
        $items_to_save[] = ['id' => $id, 'qty' => $qty, 'price' => $price, 'size' => $size];
    }

    // 3. Create the Order (Status: pending_payment)
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'pending_payment', NOW())");
    $stmt->execute([$user_id, $total]);
    $order_id = $conn->lastInsertId();

    // 4. Save Order Items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
    foreach ($items_to_save as $item) {
        $stmt->execute([$order_id, $item['id'], $item['qty'], $item['price'], $item['size']]);
    }
    // Ensure the column names here match your table exactly
$stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'pending_payment', NOW())");
    // 5. CLEAR THE CART
    unset($_SESSION['cart']);

    $conn->commit();
    
    // Redirect to the orders page where they can now see "Pay Now" or "Edit"
    header("Location: orders.php?success=Order placed successfully!");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    die("Error: " . $e->getMessage());
}