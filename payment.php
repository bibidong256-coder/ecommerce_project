<?php
session_start();
require_once 'config/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { header("Location: login.php"); exit; }

// ── 1. DATA SELECTOR (Existing Order vs Fresh Cart) ──────────
$existing_order_id = $_GET['order_id'] ?? $_POST['target_order_id'] ?? null;
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total = 0;

if ($existing_order_id) {
    $stmt = $conn->prepare("SELECT total FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$existing_order_id, $user_id]);
    $order_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order_data) {
        $total = $order_data['total'];
        $stmt_items = $conn->prepare("
            SELECT oi.quantity as qty, oi.price, oi.size, p.name, p.image 
            FROM order_items oi JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$existing_order_id]);
        $cart_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cart_items as &$item) { $item['subtotal'] = $item['qty'] * $item['price']; }
    } else { header("Location: orders.php"); exit; }
} elseif (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $row) {
        $id = $row['id'];
        $itemData = $cart[$id];
        $qty = is_array($itemData) ? ($itemData['qty'] ?? 1) : (int)$itemData;
        $size = is_array($itemData) ? ($itemData['size'] ?? '—') : '—';
        $subtotal = $qty * $row['price'];
        $total += $subtotal;
        $cart_items[] = ['name'=>$row['name'],'image'=>$row['image'],'qty'=>$qty,'size'=>$size,'subtotal'=>$subtotal];
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cart.php'); exit; }
}

// ── 2. RESPONSE HANDLING ──────────
$response = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initiate_payment'])) {
    if ($existing_order_id) {
        $update = $conn->prepare("UPDATE orders SET status = 'processing' WHERE id = ?");
        $update->execute([$existing_order_id]);
    } else { unset($_SESSION['cart']); }
    $response = ['success' => true, 'message' => "Payment request sent! Please check your device to authorize the transaction."];
}

$customer = $_SESSION['user_name'] ?? '';
$email = $_SESSION['user_email'] ?? '';
$phone = $_SESSION['user_phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | Kisken Trends</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #088178;
            --primary-dark: #04534e;
            --accent: #ffcc00; 
            --danger: #e40000; 
            --visa: #1a1f71;
            --dark: #1e293b;
            --light: #f8fafc;
        }

        body { font-family: 'Inter', system-ui, sans-serif; background: #f1f5f9; color: var(--dark); margin: 0; }
        .checkout-container { max-width: 1100px; margin: 40px auto; display: grid; grid-template-columns: 1fr 380px; gap: 30px; padding: 20px; }

        .glass-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header { padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .card-header h2 { margin: 0; font-size: 1.25rem; font-weight: 700; }

        .payment-tabs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 25px; background: #f8fafc; }
        .tab-btn { 
            background: #fff; border: 2px solid #e2e8f0; padding: 15px; border-radius: 12px; 
            cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;
        }
        .tab-btn img { height: 30px; object-fit: contain; }
        .tab-btn span { font-size: 12px; font-weight: 600; color: #64748b; }
        .tab-btn.active { border-color: var(--primary); background: #f0fdfa; transform: translateY(-3px); box-shadow: 0 4px 12px rgba(8,129,120,0.1); }
        .tab-btn.active span { color: var(--primary); }

        .form-section { padding: 25px; display: none; }
        .form-section.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #475569; }
        .input-group input { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 15px; box-sizing: border-box; }

        .summary-card { padding: 25px; }
        .summary-item { display: flex; gap: 15px; margin-bottom: 15px; }
        .summary-item img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; background: #f1f5f9; }
        .item-details h4 { margin: 0; font-size: 14px; }
        .item-details p { margin: 4px 0 0; font-size: 12px; color: #64748b; }

        .total-row { border-top: 1px solid #f1f5f9; margin-top: 20px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .total-row span { font-size: 1.25rem; font-weight: 800; color: var(--primary); }

        .pay-button { width: 100%; background: var(--primary); color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .pay-button:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .status-msg { padding: 30px 20px; border-radius: 10px; margin-bottom: 25px; text-align: center; }
        .status-msg.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        
        .action-btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; transition: 0.2s; }
        .btn-primary { background: #166534; color: #fff; }
        .btn-outline { border: 2px solid #166534; color: #166534; margin-left: 10px; }

        @media (max-width: 850px) { .checkout-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="main-content">
        <div style="margin-bottom: 20px;">
            <a href="orders.php" style="text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Return to order
            </a>
        </div>

        <?php if($response): ?>
            <div class="status-msg success">
                <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                <h3 style="margin: 0 0 10px 0;">Order Confirmed!</h3>
                <p style="margin-bottom: 25px; opacity: 0.9;"><?= $response['message'] ?></p>
                
                <div class="success-actions">
                    <a href="orders.php" class="action-btn btn-primary">
                        <i class="fas fa-receipt"></i> View My Orders
                    </a>
                    <a href="index.php" class="action-btn btn-outline">
                        Continue Shopping
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="glass-card" <?php if($response) echo 'style="display:none;"'; ?>>
            <div class="card-header">
                <h2>Secure Payment</h2>
                <div style="font-size: 12px; color: #64748b;"><i class="fas fa-shield-alt"></i> SSL Encrypted</div>
            </div>

            <div class="payment-tabs">
                <div class="tab-btn active" onclick="switchTab('mtn', this)">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/af/MTN_Logo.svg" alt="MTN">
                    <span>MTN Money</span>
                </div>
                <div class="tab-btn" onclick="switchTab('airtel', this)">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a8/Airtel_logo.svg" alt="Airtel">
                    <span>Airtel Money</span>
                </div>
                <div class="tab-btn" onclick="switchTab('card', this)">
                    <div style="display:flex; gap: 5px;">
                        <i class="fab fa-cc-visa" style="color: var(--visa); font-size: 20px;"></i>
                        <i class="fab fa-cc-mastercard" style="color: #eb001b; font-size: 20px;"></i>
                    </div>
                    <span>Card Payment</span>
                </div>
            </div>

            <form action="" method="POST" id="payment-form">
                <input type="hidden" name="initiate_payment" value="1">
                <input type="hidden" name="method" id="method-input" value="mtn">
                <input type="hidden" name="target_order_id" value="<?= htmlspecialchars($existing_order_id) ?>">

                <div id="momo-fields" class="form-section active">
                    <div class="input-group">
                        <label>Mobile Number</label>
                        <input type="tel" name="pay_phone" placeholder="077 / 070..." value="<?= $phone ?>">
                    </div>
                    <div class="input-group">
                        <label>Account Name</label>
                        <input type="text" name="pay_name" placeholder="Name on account" value="<?= $customer ?>">
                    </div>
                </div>

                <div id="card-fields" class="form-section">
                    <div class="input-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="xxxx xxxx xxxx xxxx">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM/YY">
                        </div>
                        <div class="input-group">
                            <label>CVV</label>
                            <input type="password" placeholder="***">
                        </div>
                    </div>
                </div>

                <div style="padding: 0 25px 25px;">
                    <div class="input-group">
                        <label>Email Address (For Digital Receipt)</label>
                        <input type="email" name="pay_email" placeholder="you@example.com" value="<?= $email ?>" required>
                    </div>
                    <button type="submit" class="pay-button">
                        Complete Payment — UGX <?= number_format($total) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="sidebar">
        <div class="glass-card summary-card">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.1rem;">Order Summary</h3>
            
            <?php foreach($cart_items as $item): ?>
            <div class="summary-item">
                <img src="shoes images/<?= htmlspecialchars($item['image']) ?>" alt="Product">
                <div class="item-details">
                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                    <p>Size: <?= $item['size'] ?> | Qty: <?= $item['qty'] ?></p>
                    <div style="font-weight: 700; margin-top: 5px; font-size: 13px;">UGX <?= number_format($item['subtotal']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="total-row">
                <div style="font-weight: 600;">Grand Total</div>
                <span>UGX <?= number_format($total) ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(method, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('method-input').value = method;
        const momo = document.getElementById('momo-fields');
        const card = document.getElementById('card-fields');
        if(method === 'card') {
            momo.classList.remove('active');
            card.classList.add('active');
        } else {
            card.classList.remove('active');
            momo.classList.add('active');
        }
    }
</script>
</body>
</html>