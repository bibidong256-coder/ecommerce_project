<?php
session_start();
require "config/db.php";

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$order_id || !$user_id) {
    header("Location: orders.php");
    exit;
}

// 1. Get the items from the order
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

if ($items) {
    // 2. Clear current session cart and refill it
    $_SESSION['cart'] = [];
    foreach ($items as $item) {
        $_SESSION['cart'][$item['product_id']] = [
            'qty' => $item['quantity'],
            'size' => $item['size']
        ];
    }

    // 3. Delete the pending order so we don't have duplicates
    $conn->prepare("DELETE FROM orders WHERE id = ? AND status = 'pending_payment'")->execute([$order_id]);
    $conn->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);

    // 4. Send user back to cart to make edits
    header("Location: cart.php");
    exit;
}