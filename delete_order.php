<?php
session_start();
require "config/db.php";

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if ($order_id && $user_id) {
    // 1. Double check the order belongs to the user and is still pending
    $check = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'pending_payment'");
    $check->execute([$order_id, $user_id]);

    if ($check->fetch()) {
        // 2. Delete the items first (Foreign Key constraint)
        $stmt1 = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt1->execute([$order_id]);

        // 3. Delete the main order
        $stmt2 = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt2->execute([$order_id]);

        header("Location: orders.php?msg=Order Deleted");
        exit;
    }
}

header("Location: orders.php");
exit;