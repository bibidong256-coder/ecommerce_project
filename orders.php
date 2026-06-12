<?php
session_start();
require "config/db.php";

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { header("Location: login.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders | Kisken Trends</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; color: #333; margin: 0; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .page-navigation { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .nav-btn { text-decoration: none; padding: 10px 18px; border-radius: 6px; font-size: 14px; font-weight: 600; transition: 0.3s; }
        .back-btn { background: #088178; color: #fff; }
        .logout-btn { background: #fff; color: #e74c3c; border: 1px solid #ffcdd2; }

        .order-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; border: 1px solid #eee; }
        .order-header { background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .order-body { padding: 20px; }

        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .pending_payment, .pending { background: #fff3cd; color: #856404; }
        .success, .completed { background: #d4edda; color: #155724; }

        .total-amount { font-size: 18px; font-weight: 700; color: #088178; }

        /* Button Group */
        .btn-group { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
        .action-btn { flex: 1; text-align: center; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; transition: 0.2s; min-width: 100px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }

        .pay-btn { background: #088178; color: white; border: none; }
        .edit-btn { background: #f0f0f0; color: #444; border: 1px solid #ddd; }
        .delete-btn { background: #fff; color: #e74c3c; border: 1px solid #e74c3c; flex: 0 0 auto; padding: 12px 16px; }
        .receipt-btn { background: #f0f0f0; color: #444; border: 1px solid #ddd; }

        .action-btn:hover { opacity: 0.8; transform: translateY(-1px); }
        .delete-btn:hover { background: #fff5f5; }

        /* Disabled state for completed orders */
        .action-btn.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: not-allowed;
            transform: none;
        }

        .empty-state { text-align: center; padding: 80px 20px; background: #fff; border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="page-navigation">
        <a href="shop.php" class="nav-btn back-btn"><i class="fas fa-arrow-left"></i> Back to Shop</a>
        <a href="logout.php" class="nav-btn logout-btn">Logout</a>
    </div>

    <h2 style="margin-bottom: 25px;">Order History</h2>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <?php $isPending = in_array($order['status'], ['pending_payment', 'pending', null, '']); ?>
            <div class="order-card">
                <div class="order-header">
                    <span><strong>Order #<?php echo htmlspecialchars($order['id']); ?></strong></span>
                    <?php $displayStatus = $order['status'] ?: 'pending'; ?>
                    <span class="status-badge <?php echo htmlspecialchars($displayStatus); ?>">
                        <?php echo htmlspecialchars(str_replace('_', ' ', $displayStatus)); ?>
                    </span>
                </div>
                <div class="order-body">
                    <p style="color: #777; font-size: 13px;">Placed on: <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                    <div style="margin: 15px 0;">
                        <span style="font-size: 14px; color: #666;">Grand Total:</span><br>
                        <span class="total-amount">UGX <?php echo number_format($order['total']); ?></span>
                    </div>

                    <div class="btn-group">
                        <!-- Pay Now Button -->
                        <a href="<?php echo $isPending ? 'payment.php?order_id=' . urlencode($order['id']) : '#'; ?>"
                           class="action-btn pay-btn <?php echo !$isPending ? 'disabled' : ''; ?>">
                            <i class="fas fa-wallet"></i> Pay Now
                        </a>

                        <!-- Edit Button -->
                        <a href="<?php echo $isPending ? 'edit_order.php?id=' . urlencode($order['id']) : '#'; ?>"
                           class="action-btn edit-btn <?php echo !$isPending ? 'disabled' : ''; ?>"
                           <?php if ($isPending): ?>
                               onclick="return confirm('This will move items back to your cart. Proceed?')"
                           <?php endif; ?>>
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <!-- Delete (pending) or View Receipt (completed) -->
                        <?php if ($isPending): ?>
                            <a href="delete_order.php?id=<?php echo urlencode($order['id']); ?>"
                               class="action-btn delete-btn"
                               onclick="return confirm('Are you sure you want to cancel and delete this order completely?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        <?php else: ?>
                            <a href="view_receipt.php?id=<?php echo urlencode($order['id']); ?>"
                               class="action-btn receipt-btn">
                                <i class="fas fa-file-invoice"></i> Receipt
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open" style="font-size: 40px; color: #ccc; margin-bottom: 20px; display: block;"></i>
            <h3>No orders found</h3>
            <p>Ready to get some new kicks?</p>
            <a href="shop.php" class="nav-btn back-btn" style="display:inline-block; margin-top:20px;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>