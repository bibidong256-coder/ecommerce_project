<?php
session_start();
require "config/db.php";

// Security check — only admins can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Make sure an ID was passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_products.php");
    exit;
}

$id = intval($_GET['id']);

// Optional: delete the image file from disk too
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product && $product['image']) {
    $imagePath = __DIR__ . DIRECTORY_SEPARATOR . "shoes images" . DIRECTORY_SEPARATOR . $product['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // remove the image file from the server
    }
}

// Delete from database
$conn->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

header("Location: admin_products.php?success=deleted");
exit;