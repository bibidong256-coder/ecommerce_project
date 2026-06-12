<?php
session_start();
require "config/db.php";

// Protect admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all orders + counts
$orders         = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$total_orders   = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn() ?? 0;
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="admin-container">

    <!-- ═══ SIDEBAR — matches admin_dashboard.php ════════ -->
    <aside class="admin-nav">

        <div class="nav-brand">
            <div class="nav-brand-icon">
                <i class="fas fa-shopping-bag" aria-hidden="true"></i>
            </div>
            <div>
                <p class="nav-brand-name">Admin Panel</p>
                <p class="nav-brand-sub">Kisken Trends</p>
            </div>
        </div>

        <p class="nav-label">Main</p>
        <nav class="nav-links">
            <a href="admin_dashboard.php">
                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="admin_index.php">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span class="nav-text">Home</span>
            </a>
            <a href="admin_products.php">
                <span class="nav-icon"><i class="fas fa-shoe-prints"></i></span>
                <span class="nav-text">Products</span>
                <?php if ($total_products > 0): ?>
                    <span class="nav-badge"><?= $total_products ?></span>
                <?php endif; ?>
            </a>
            <a href="admin_orders.php" class="active">
                <span class="nav-icon"><i class="fas fa-box"></i></span>
                <span class="nav-text">Orders</span>
                <?php if ($pending_orders > 0): ?>
                    <span class="nav-badge danger"><?= $pending_orders ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <p class="nav-label">Content</p>
        <nav class="nav-links">
            <a href="blog_admin.php">
                <span class="nav-icon"><i class="fas fa-pencil-alt"></i></span>
                <span class="nav-text">Blog</span>
            </a>
            <a href="Admin_about.php">
                <span class="nav-icon"><i class="fas fa-info-circle"></i></span>
                <span class="nav-text">About</span>
            </a>
            <a href="admin_contact.php">
                <span class="nav-icon"><i class="fas fa-phone"></i></span>
                <span class="nav-text">Contact</span>
            </a>
        </nav>

        <div class="nav-footer">
            <div class="nav-user">
                <div class="nav-avatar">
                    <?= strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <p class="nav-user-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></p>
                    <p class="nav-user-role">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="nav-logout">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="nav-text">Logout</span>
            </a>
        </div>

    </aside>

    <!-- ═══ MAIN CONTENT ══════════════════════════════════ -->
    <div class="admin-content">

        <!-- Sticky header -->
        <div class="admin-header">
            <div>
                <h2>Customer Orders</h2>
                <p class="header-sub">View and manage all customer orders</p>
            </div>
            <span class="order-count-badge">
                <i class="fas fa-box" style="margin-right:6px;color:#088178;"></i>
                <?= $total_orders ?> Total Orders
            </span>
        </div>

        <!-- Stats bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(8,129,120,.1);color:#088178;">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-info"><h3>Total Orders</h3><p><?= $total_orders ?></p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(241,196,15,.1);color:#d4a017;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info"><h3>Pending</h3><p><?= $pending_orders ?></p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,152,219,.1);color:#3498db;">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-info">
                    <h3>Processing</h3>
                    <p><?= $conn->query("SELECT COUNT(*) FROM orders WHERE status='processing'")->fetchColumn() ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(46,204,113,.1);color:#2ecc71;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Delivered</h3>
                    <p><?= $conn->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn() ?></p>
                </div>
            </div>
        </div>

        <!-- Orders list -->
        <?php while ($order = $orders->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="order-card">
            <div class="order-header">
                <div class="order-meta">
                    <span class="order-id">
                        <i class="fas fa-hashtag" style="font-size:11px;color:#aaa;"></i>
                        Order <?= $order['id'] ?>
                    </span>
                    <p class="order-total">Shs <?= number_format($order['total']) ?></p>
                    <span class="status-pill <?= $order['status'] ?? 'pending' ?>">
                        <?= ucfirst($order['status'] ?? 'pending') ?>
                    </span>
                </div>

                <form method="POST" action="update_order_status.php" class="status-form">
                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                    <select name="status" class="status-select">
                        <option value="pending"    <?= ($order['status']==='pending')    ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($order['status']==='processing') ? 'selected' : '' ?>>Processing</option>
                        <option value="delivered"  <?= ($order['status']==='delivered')  ? 'selected' : '' ?>>Delivered</option>
                    </select>
                    <button class="btn-update" type="submit">
                        <i class="fas fa-check"></i> Update
                    </button>
                </form>
            </div>

            <div class="order-details">
                <h4><i class="fas fa-shopping-cart" style="margin-right:6px;color:#aaa;"></i>Items Ordered</h4>
                <?php
                    $items = $conn->prepare("
                        SELECT p.name, oi.quantity
                        FROM order_items oi
                        JOIN products p ON p.id = oi.product_id
                        WHERE oi.order_id = ?
                    ");
                    $items->execute([$order['id']]);
                ?>
                <ul class="item-list">
                    <?php while ($item = $items->fetch(PDO::FETCH_ASSOC)): ?>
                    <li>
                        <span class="item-name">
                            <i class="fas fa-circle" style="font-size:5px;color:#ccc;margin-right:8px;vertical-align:middle;"></i>
                            <?= htmlspecialchars($item['name']) ?>
                        </span>
                        <span class="item-qty">x<?= $item['quantity'] ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Roboto,sans-serif;background:#f4f7f6;color:#333}
.admin-container{display:flex}

/* ── Sidebar ── */
.admin-nav{
    width:230px;background:#0f0f0f;display:flex;flex-direction:column;
    position:fixed;height:100vh;z-index:1000;overflow-y:auto;overflow-x:hidden
}
.nav-brand{
    display:flex;align-items:center;gap:10px;
    padding:20px 18px 18px;border-bottom:1px solid rgba(255,255,255,.06);flex-shrink:0
}
.nav-brand-icon{
    width:34px;height:34px;background:#088178;border-radius:9px;
    display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;flex-shrink:0
}
.nav-brand-name{font-size:13px;font-weight:600;color:#fff;line-height:1.3}
.nav-brand-sub{font-size:10.5px;color:rgba(255,255,255,.28)}
.nav-label{
    font-size:10px;font-weight:600;color:rgba(255,255,255,.22);
    letter-spacing:.1em;text-transform:uppercase;padding:16px 18px 5px
}
.nav-links{padding:0 8px}
.nav-links a{
    display:flex;align-items:center;gap:10px;padding:9px 12px;
    border-radius:9px;text-decoration:none;color:rgba(255,255,255,.45);
    font-size:13.5px;margin-bottom:1px;transition:background .15s,color .15s;position:relative
}
.nav-links a:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85)}
.nav-links a.active{background:rgba(8,129,120,.2);color:#fff}
.nav-links a.active .nav-icon{color:#0bbfb4}
.nav-links a.active::before{
    content:'';position:absolute;left:0;top:20%;bottom:20%;
    width:3px;background:#088178;border-radius:0 3px 3px 0
}
.nav-icon{width:20px;text-align:center;font-size:14px;flex-shrink:0;color:inherit}
.nav-text{flex:1}
.nav-badge{
    margin-left:auto;background:#088178;color:#fff;
    font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;line-height:1.7;flex-shrink:0
}
.nav-badge.danger{background:rgba(231,76,60,.2);color:#e74c3c}
.nav-footer{margin-top:auto;border-top:1px solid rgba(255,255,255,.06);padding:12px 8px 10px;flex-shrink:0}
.nav-user{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:9px;margin-bottom:2px}
.nav-avatar{
    width:30px;height:30px;border-radius:50%;background:#088178;
    display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:600;color:#fff;flex-shrink:0
}
.nav-user-name{font-size:12.5px;font-weight:600;color:#fff;line-height:1.3}
.nav-user-role{font-size:10.5px;color:rgba(255,255,255,.28)}
.nav-logout{
    display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;
    text-decoration:none;color:rgba(231,76,60,.75);font-size:13.5px;transition:background .15s,color .15s
}
.nav-logout:hover{background:rgba(231,76,60,.1);color:#e74c3c}

/* ── Main ── */
.admin-content{margin-left:230px;padding:0 40px 40px;width:calc(100% - 230px)}

.admin-header{
    position:sticky;top:0;z-index:100;
    background:rgba(244,247,246,.95);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    padding:22px 0 18px;margin-bottom:28px;border-bottom:1px solid #eee;
    display:flex;justify-content:space-between;align-items:center
}
.admin-header h2{font-size:20px;font-weight:600;color:#1a1a1a}
.header-sub{font-size:13px;color:#888;margin-top:2px}
.order-count-badge{
    background:#fff;padding:9px 16px;border-radius:50px;
    font-weight:600;font-size:13px;color:#444;
    border:1px solid #eee;box-shadow:0 2px 5px rgba(0,0,0,.04)
}

/* ── Stats bar ── */
.stats-bar{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:16px;margin-bottom:28px
}
.stat-card{
    background:#fff;padding:20px;border-radius:12px;
    display:flex;align-items:center;gap:16px;border:1px solid #eee;
    box-shadow:0 2px 6px rgba(0,0,0,.02);transition:box-shadow .2s
}
.stat-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.06)}
.stat-icon{
    width:46px;height:46px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.2rem;flex-shrink:0
}
.stat-info h3{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:4px}
.stat-info p{font-size:1.4rem;font-weight:700;color:#1a1a1a}

/* ── Order cards ── */
.order-card{
    background:#fff;border-radius:12px;border:1px solid #eee;
    box-shadow:0 2px 8px rgba(0,0,0,.03);margin-bottom:20px;
    overflow:hidden;transition:box-shadow .2s
}
.order-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.07)}

.order-header{
    display:flex;justify-content:space-between;align-items:center;
    padding:18px 22px;border-bottom:1px solid #f5f5f5
}
.order-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.order-id{font-weight:700;font-size:15px;color:#1a1a1a}
.order-total{font-weight:700;color:#088178;font-size:15px}

/* ── Status pill ── */
.status-pill{
    padding:4px 12px;border-radius:20px;
    font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px
}
.status-pill.pending{background:#fff9e6;color:#d4a017}
.status-pill.processing{background:#e6f7ff;color:#3498db}
.status-pill.delivered{background:#eafff0;color:#2ecc71}
.status-pill.cancelled{background:#fff0f0;color:#e74c3c}

/* ── Status form ── */
.status-form{display:flex;gap:8px;align-items:center}
.status-select{
    padding:8px 12px;border:1px solid #dde1e7;border-radius:8px;
    font-size:13px;outline:none;background:#fff;
    transition:border-color .15s;cursor:pointer
}
.status-select:focus{border-color:#088178}
.btn-update{
    background:#088178;color:#fff;border:none;
    padding:8px 16px;border-radius:8px;font-size:13px;
    font-weight:600;cursor:pointer;
    display:flex;align-items:center;gap:6px;
    transition:background .2s
}
.btn-update:hover{background:#066b63}

/* ── Order details ── */
.order-details{padding:18px 22px;background:#fafcfb}
.order-details h4{
    margin-bottom:12px;font-size:11px;color:#aaa;
    text-transform:uppercase;letter-spacing:.5px;font-weight:600
}
.item-list{list-style:none;padding:0;margin:0}
.item-list li{
    display:flex;justify-content:space-between;align-items:center;
    padding:9px 0;border-bottom:1px solid #f0f0f0
}
.item-list li:last-child{border-bottom:none}
.item-name{font-weight:500;font-size:14px;color:#333}
.item-qty{
    background:#f0f0f0;color:#666;font-size:12px;
    font-weight:600;padding:3px 10px;border-radius:20px
}
</style>

</body>
</html>