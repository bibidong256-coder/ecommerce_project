<?php
session_start();
require "config/db.php";

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "Cart is empty";
    exit;
}

// STEP 1: get ONLY product IDs
$ids = array_keys($cart);

// STEP 2: create placeholders for PDO
$placeholders = implode(',', array_fill(0, count($ids), '?'));

// STEP 3: safe query
$sql = "SELECT * FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->execute($ids);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<h2>Your Cart</h2>

<?php foreach ($products as $row) { 
    $qty = $cart[$row['id']];
    $subtotal = $row['price'] * $qty;
    $total += $subtotal;
?>

<div>
    <h3><?= $row['name'] ?></h3>
    <p>Price: <?= $row['price'] ?></p>
    <p>Quantity: <?= $qty ?></p>
    <p>Subtotal: <?= $subtotal ?></p>
</div>

<hr>

<?php } ?>

<h2>Total: <?= $total ?></h2>