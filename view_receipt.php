<?php
session_start();
require "config/db.php";

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { header("Location: login.php"); exit; }

$order_id = $_GET['id'] ?? null;
if (!$order_id) { header("Location: orders.php"); exit; }

// Fetch order — make sure it belongs to this user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) { header("Location: orders.php"); exit; }

// Fetch order items joined with product name
$stmt = $conn->prepare("
    SELECT oi.*, p.name AS product_name, p.image AS product_image
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo htmlspecialchars($order['id']); ?> | Kisken Trends</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            color: #333;
            padding: 40px 20px;
        }

        .container { max-width: 700px; margin: 0 auto; }

        /* Navigation */
        .page-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .nav-btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }
        .back-btn { background: #088178; color: #fff; }
        .print-btn {
            background: #fff;
            color: #088178;
            border: 1px solid #088178;
            cursor: pointer;
        }
        .nav-btn:hover { opacity: 0.85; }

        /* Receipt Card */
        .receipt {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            overflow: hidden;
            border: 1px solid #eee;
        }

        /* Header */
        .receipt-header {
            background: #088178;
            color: #fff;
            padding: 30px 30px 25px;
            text-align: center;
        }
        .receipt-header .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .receipt-header .receipt-title {
            font-size: 13px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Status Banner */
        .status-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-delivered  { background: #d4edda; color: #155724; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed  { background: #d4edda; color: #155724; }
        .status-pending    { background: #fff3cd; color: #856404; }
        .status-pending_payment { background: #fff3cd; color: #856404; }

        /* Order Meta */
        .order-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border-bottom: 1px solid #eee;
        }
        .meta-item {
            padding: 18px 25px;
            border-right: 1px solid #eee;
        }
        .meta-item:nth-child(even) { border-right: none; }
        .meta-item:nth-child(3),
        .meta-item:nth-child(4) { border-top: 1px solid #eee; }
        .meta-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        /* Items Table */
        .items-section { padding: 25px; }
        .items-section h3 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 15px;
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .item-row:last-child { border-bottom: none; }

        .item-img {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            object-fit: cover;
            background: #f4f4f4;
            flex-shrink: 0;
        }
        .item-img-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            flex-shrink: 0;
        }

        .item-details { flex: 1; }
        .item-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        .item-meta {
            font-size: 12px;
            color: #999;
        }

        .item-price {
            text-align: right;
        }
        .item-unit-price {
            font-size: 13px;
            color: #999;
        }
        .item-total-price {
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }

        /* Totals */
        .totals-section {
            border-top: 2px dashed #eee;
            padding: 20px 25px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .total-row.grand {
            font-size: 18px;
            font-weight: 700;
            color: #088178;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }

        /* Footer */
        .receipt-footer {
            background: #f8f9fa;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #aaa;
        }

        /* Print Styles */
        @media print {
            body { background: #fff; padding: 0; }
            .page-navigation { display: none; }
            .receipt { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="page-navigation">
        <a href="orders.php" class="nav-btn back-btn">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <button class="nav-btn print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <div class="receipt">

        <!-- Header -->
        <div class="receipt-header">
            <div class="brand">👟 Kisken Trends</div>
            <div class="receipt-title">Order Receipt</div>
        </div>

        <!-- Status Banner -->
        <?php
            $statusClass = 'status-' . htmlspecialchars($order['status']);
            $statusIcon  = in_array($order['status'], ['delivered', 'completed']) ? 'fa-check-circle' : 'fa-clock';
        ?>
        <div class="status-banner <?php echo $statusClass; ?>">
            <i class="fas <?php echo $statusIcon; ?>"></i>
            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $order['status']))); ?>
        </div>

        <!-- Order Meta -->
        <div class="order-meta">
            <div class="meta-item">
                <div class="meta-label">Order Number</div>
                <div class="meta-value">#<?php echo htmlspecialchars($order['id']); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Date Placed</div>
                <div class="meta-value"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
            </div>
            <?php if (!empty($order['customer_name'])): ?>
            <div class="meta-item">
                <div class="meta-label">Customer</div>
                <div class="meta-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($order['phone'])): ?>
            <div class="meta-item">
                <div class="meta-label">Phone</div>
                <div class="meta-value"><?php echo htmlspecialchars($order['phone']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($order['address'])): ?>
            <div class="meta-item" style="grid-column: 1 / -1; border-right: none;">
                <div class="meta-label">Delivery Address</div>
                <div class="meta-value"><?php echo htmlspecialchars($order['address']); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Items -->
        <div class="items-section">
            <h3>Items Ordered</h3>

            <?php foreach ($items as $item): ?>
                <div class="item-row">
                    <!-- Product Image -->
                    <?php if (!empty($item['product_image'])): ?>
                        <img src="<?php echo htmlspecialchars($item['product_image']); ?>"
                             alt="<?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?>"
                             class="item-img">
                    <?php else: ?>
                        <div class="item-img-placeholder">
                            <i class="fas fa-shoe-prints"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Details -->
                    <div class="item-details">
                        <div class="item-name">
                            <?php echo htmlspecialchars($item['product_name'] ?? 'Product #' . $item['product_id']); ?>
                        </div>
                        <div class="item-meta">
                            Qty: <?php echo (int)$item['quantity']; ?>
                            <?php if (!empty($item['size'])): ?>
                                &nbsp;·&nbsp; Size: <?php echo htmlspecialchars($item['size']); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="item-price">
                        <div class="item-unit-price">
                            UGX <?php echo number_format($item['price']); ?> each
                        </div>
                        <div class="item-total-price">
                            UGX <?php echo number_format($item['price'] * $item['quantity']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <?php
                $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
            ?>
            <div class="total-row">
                <span>Subtotal</span>
                <span>UGX <?php echo number_format($subtotal); ?></span>
            </div>
            <div class="total-row">
                <span>Delivery</span>
                <span style="color: #088178;">Free</span>
            </div>
            <div class="total-row grand">
                <span>Grand Total</span>
                <span>UGX <?php echo number_format($order['total']); ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            Thank you for shopping with Kisken Trends! 🙏<br>
            For support, contact us on WhatsApp or visit our store.
        </div>

    </div>
</div>

</body>
</html>