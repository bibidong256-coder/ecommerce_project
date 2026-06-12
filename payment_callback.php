<?php
// payment_callback.php
// Flutterwave / Pesapal / DPO redirect here after payment
// URL example: payment_callback.php?tx_ref=KSD-xxx&status=successful&transaction_id=12345

session_start();
require_once 'config/db.php'; // your $conn

$status         = $_GET['status']         ?? $_GET['payment_status'] ?? 'failed';
$tx_ref         = $_GET['tx_ref']         ?? $_GET['OrderMerchantReference'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? $_GET['TransactionID'] ?? '';

$success = false;
$message = '';
$order   = null;

if ($tx_ref) {
    $stmt = $conn->prepare("SELECT * FROM orders_payment WHERE order_ref = ?");
    $stmt->execute([$tx_ref]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($order) {
    if (in_array(strtolower($status), ['successful','success','completed','paid'])) {

        // ── OPTIONAL: Verify with Flutterwave before trusting ──
        // $flw_key = 'YOUR_SECRET_KEY';
        // $verify = json_decode(file_get_contents("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
        //   false, stream_context_create(['http'=>['header'=>"Authorization: Bearer {$flw_key}\r\n"]])), true);
        // if ($verify['data']['status'] === 'successful' && $verify['data']['amount'] >= $order['amount']) { ... }

        $conn->prepare("UPDATE orders_payment SET status='success', provider_tx_id=? WHERE order_ref=?")
             ->execute([$transaction_id, $tx_ref]);
        $success = true;
        $message = "Payment of UGX " . number_format($order['amount']) . " received successfully!";

        // Clear cart from session
        unset($_SESSION['cart'], $_SESSION['pending_order_id'], $_SESSION['pending_amount']);

    } else {
        $conn->prepare("UPDATE orders_payment SET status='failed' WHERE order_ref=?")
             ->execute([$tx_ref]);
        $message = "Payment was not completed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $success ? 'Payment Successful' : 'Payment Failed' ?> — Kisken</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:#f4f7f6;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
.box{background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(8,129,120,.15);padding:48px 40px;max-width:480px;width:100%;text-align:center;}
.icon{font-size:3.5rem;margin-bottom:16px;}
h1{font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;margin-bottom:10px;}
.success h1{color:#065f46;} .fail h1{color:#991b1b;}
p{color:#555;font-size:.95rem;line-height:1.6;margin-bottom:20px;}
.ref{display:inline-block;padding:7px 18px;background:#f0fdf4;border-radius:20px;font-size:.82rem;font-family:monospace;font-weight:700;color:#065f46;margin-bottom:24px;}
.fail .ref{background:#fef2f2;color:#991b1b;}
.btn{display:inline-block;padding:13px 32px;border-radius:12px;font-weight:700;font-size:.95rem;text-decoration:none;transition:all .2s;}
.btn-primary{background:#088178;color:#fff;margin-right:10px;} .btn-primary:hover{background:#04534e;}
.btn-secondary{background:#ecf0f1;color:#555;} .btn-secondary:hover{background:#dfe6e9;}
</style>
</head>
<body>
<div class="box <?= $success ? 'success' : 'fail' ?>">
    <?php if ($success): ?>
    <div class="icon">🎉</div>
    <h1>Payment Successful!</h1>
    <p><?= htmlspecialchars($message) ?><br>Thank you for shopping at <strong>Kisken Trends Duuka</strong>. Your order is being processed.</p>
    <?php if ($tx_ref): ?><div class="ref">Ref: <?= htmlspecialchars($tx_ref) ?></div><?php endif; ?>
    <br>
    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    <a href="track-my-order.html" class="btn btn-secondary">Track Order</a>
    <?php else: ?>
    <div class="icon">😞</div>
    <h1>Payment Failed</h1>
    <p><?= htmlspecialchars($message ?: 'Something went wrong with your payment. No money has been deducted.') ?></p>
    <?php if ($tx_ref): ?><div class="ref">Ref: <?= htmlspecialchars($tx_ref) ?></div><?php endif; ?>
    <br>
    <a href="payment.php" class="btn btn-primary">Try Again</a>
    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    <?php endif; ?>
</div>
</body>
</html>
