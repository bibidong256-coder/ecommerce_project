<?php
session_start();
require "config/db.php";

// Protect admin — must be BEFORE any HTML
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    die("<div class='access-denied'>Access denied. Please login as admin.</div>");
}

// All queries BEFORE HTML output
// Revenue = 1% of total from delivered orders only
$revenue        = $conn->query("SELECT SUM(total * 0.01) as total FROM orders WHERE status = 'delivered'")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$total_orders   = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='pending'")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
$recent_orders  = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

// Orders per day (last 30 days)
$chart_data = $conn->query("
    SELECT DATE(created_at) as day, COUNT(*) as count
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY day ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Revenue per day (last 30 days) — 1% of delivered orders only
$revenue_data = $conn->query("
    SELECT DATE(created_at) as day, SUM(total * 0.01) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND status = 'delivered'
    GROUP BY DATE(created_at)
    ORDER BY day ASC
")->fetchAll(PDO::FETCH_ASSOC);

$chart_labels  = json_encode(array_column($chart_data,   'day'));
$chart_orders  = json_encode(array_column($chart_data,   'count'));
$chart_revenue = json_encode(array_column($revenue_data, 'revenue'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="admin-container">

    <!-- ─── Sidebar ─────────────────────────────────────────── -->
    <aside class="admin-nav" id="adminNav">

        <!-- Brand -->
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <i class="fas fa-shopping-bag" aria-hidden="true"></i>
            </div>
            <div class="nav-brand-text">
                <p class="nav-brand-name">Admin Panel</p>
                <p class="nav-brand-sub">Kisken Trends</p>
            </div>
        </div>

        <!-- Main nav -->
        <p class="nav-label">Main</p>
        <nav class="nav-links">
            <a href="admin_dashboard.php" class="active">
                <span class="nav-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="admin_index.php">
                <span class="nav-icon"><i class="fas fa-home" aria-hidden="true"></i></span>
                <span class="nav-text">Home</span>
            </a>
            <a href="admin_products.php">
                <span class="nav-icon"><i class="fas fa-shoe-prints" aria-hidden="true"></i></span>
                <span class="nav-text">Products</span>
                <?php if($total_products > 0): ?>
                    <span class="nav-badge"><?= $total_products ?></span>
                <?php endif; ?>
            </a>
            <a href="admin_orders.php">
                <span class="nav-icon"><i class="fas fa-box" aria-hidden="true"></i></span>
                <span class="nav-text">Orders</span>
                <?php if($pending_orders > 0): ?>
                    <span class="nav-badge danger"><?= $pending_orders ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <!-- Content nav -->
        <p class="nav-label">Content</p>
        <nav class="nav-links">
            <a href="blog_admin.php">
                <span class="nav-icon"><i class="fas fa-pencil-alt" aria-hidden="true"></i></span>
                <span class="nav-text">Blog</span>
            </a>
            <a href="Admin_about.php">
                <span class="nav-icon"><i class="fas fa-info-circle" aria-hidden="true"></i></span>
                <span class="nav-text">About</span>
            </a>
            <a href="admin_contact.php">
                <span class="nav-icon"><i class="fas fa-phone" aria-hidden="true"></i></span>
                <span class="nav-text">Contact</span>
            </a>
        </nav>

        <!-- Footer: user + logout -->
        <div class="nav-footer">
            <div class="nav-user">
                <div class="nav-avatar">
                    <?= strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="nav-user-info">
                    <p class="nav-user-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></p>
                    <p class="nav-user-role">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="nav-logout">
                <span class="nav-icon"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></span>
                <span class="nav-text">Logout</span>
            </a>
        </div>

    </aside>

    <!-- ─── Main content ─────────────────────────────────────── -->
    <div class="admin-main">

        <!-- Sticky header -->
        <div class="header-flex">
            <div>
                <h2>Dashboard Overview</h2>
                <p class="header-sub">Welcome back, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></p>
            </div>
            <span class="date-badge">
                <i class="fas fa-calendar-alt" style="margin-right:6px;color:#088178;"></i>
                <?php echo date('F j, Y'); ?>
            </span>
        </div>

        <!-- Stat cards -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(39,174,96,0.1);color:#27ae60;">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Revenue (1%)</h3>
                    <p>Shs <?php echo number_format($revenue); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,152,219,0.1);color:#3498db;">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?php echo $total_orders; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(155,89,182,0.1);color:#9b59b6;">
                    <i class="fas fa-shoe-prints"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Products</h3>
                    <p><?php echo $total_products; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(231,76,60,0.1);color:#e74c3c;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Pending Orders</h3>
                    <p><?php echo $pending_orders; ?></p>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="content-card chart-container">
            <div class="card-header">
                <h3>Order & Revenue Trends</h3>
                <span class="card-badge">Last 30 days</span>
            </div>
            <!-- Legend -->
            <div class="chart-legend">
                <span class="legend-dot" style="background:#088178;"></span> Orders &nbsp;&nbsp;
                <span class="legend-dot" style="background:#e67e22;"></span> Revenue (Shs) — 1% of delivered
            </div>
            <canvas id="salesChart" style="max-height:280px;"></canvas>
        </div>

        <!-- Recent orders table -->
        <div class="content-card">
            <div class="card-header">
                <h3>Recent Orders</h3>
                <a href="admin_orders.php" class="card-link">View all <i class="fas fa-arrow-right"></i></a>
            </div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $recent_orders->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td class="text-muted">#<?= $order['id'] ?></td>
                        <td class="bold">Shs <?= number_format($order['total']) ?></td>
                        <td>
                            <span class="status-pill <?= htmlspecialchars($order['status'] ?? 'pending') ?>">
                                <?= htmlspecialchars($order['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('M d, H:i', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /admin-main -->
</div><!-- /admin-container -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= $chart_labels ?>,
        datasets: [
            {
                label: 'Orders',
                data: <?= $chart_orders ?>,
                borderColor: '#088178',
                backgroundColor: 'rgba(8,129,120,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#088178',
                pointRadius: 4,
                yAxisID: 'y',
            },
            {
                label: 'Revenue (Shs)',
                data: <?= $chart_revenue ?>,
                borderColor: '#e67e22',
                backgroundColor: 'rgba(230,126,34,0.06)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#e67e22',
                pointRadius: 4,
                yAxisID: 'y2',
                borderDash: [5, 3],
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label === 'Revenue (Shs)') {
                            return ' Revenue: Shs ' + Number(context.raw).toLocaleString();
                        }
                        return ' Orders: ' + context.raw;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                position: 'left',
                grid: { color: '#f0f0f0' },
                title: { display: true, text: 'Orders' }
            },
            y2: {
                beginAtZero: true,
                position: 'right',
                grid: { drawOnChartArea: false },
                title: { display: true, text: 'Revenue (Shs)' },
                ticks: {
                    callback: val => 'Shs ' + val.toLocaleString()
                }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>

<style>
/* ─── Reset ──────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background: #f4f7f6;
    color: #333;
}

/* ─── Layout ─────────────────────────────────────────────── */
.admin-container { display: flex; }

/* ═══════════════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════════════ */
.admin-nav {
    width: 230px;
    background: #0f0f0f;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
}

/* ─── Brand ──────────────────────────────────────────────── */
.nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 18px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}
.nav-brand-icon {
    width: 34px; height: 34px;
    background: #088178;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px; flex-shrink: 0;
}
.nav-brand-name {
    font-size: 13px; font-weight: 600;
    color: #fff; line-height: 1.3;
}
.nav-brand-sub {
    font-size: 10.5px;
    color: rgba(255,255,255,0.28);
}

/* ─── Section labels ─────────────────────────────────────── */
.nav-label {
    font-size: 10px;
    font-weight: 600;
    color: rgba(255,255,255,0.22);
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 16px 18px 5px;
}

/* ─── Nav links ──────────────────────────────────────────── */
.nav-links { padding: 0 8px; }

.nav-links a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 9px;
    text-decoration: none;
    color: rgba(255,255,255,0.45);
    font-size: 13.5px;
    margin-bottom: 1px;
    transition: background .15s, color .15s;
    position: relative;
}
.nav-links a:hover {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.85);
}
.nav-links a.active {
    background: rgba(8,129,120,0.2);
    color: #fff;
}
.nav-links a.active .nav-icon { color: #0bbfb4; }

.nav-icon {
    width: 20px;
    text-align: center;
    font-size: 14px;
    flex-shrink: 0;
    color: inherit;
}
.nav-text { flex: 1; }

/* ─── Badges ─────────────────────────────────────────────── */
.nav-badge {
    margin-left: auto;
    background: #088178;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    line-height: 1.7;
    flex-shrink: 0;
}
.nav-badge.danger {
    background: rgba(231,76,60,0.2);
    color: #e74c3c;
}

/* ─── Active left accent ─────────────────────────────────── */
.nav-links a.active::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 3px;
    background: #088178;
    border-radius: 0 3px 3px 0;
}

/* ─── Footer ─────────────────────────────────────────────── */
.nav-footer {
    margin-top: auto;
    border-top: 1px solid rgba(255,255,255,0.06);
    padding: 12px 8px 10px;
    flex-shrink: 0;
}
.nav-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 9px;
    margin-bottom: 2px;
}
.nav-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: #088178;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600;
    color: #fff; flex-shrink: 0;
}
.nav-user-name {
    font-size: 12.5px; font-weight: 600;
    color: #fff; line-height: 1.3;
}
.nav-user-role {
    font-size: 10.5px;
    color: rgba(255,255,255,0.28);
}
.nav-logout {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 9px;
    text-decoration: none;
    color: rgba(231,76,60,0.75);
    font-size: 13.5px;
    transition: background .15s, color .15s;
}
.nav-logout:hover {
    background: rgba(231,76,60,0.1);
    color: #e74c3c;
}

/* ═══════════════════════════════════════════════════════════
   MAIN CONTENT
═══════════════════════════════════════════════════════════ */
.admin-main {
    margin-left: 230px;
    padding: 0 40px 40px;
    width: calc(100% - 230px);
}

/* ─── Sticky header ──────────────────────────────────────── */
.header-flex {
    position: sticky;
    top: 0;
    z-index: 999;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(244,247,246,0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 22px 0 18px;
    margin-bottom: 28px;
    border-bottom: 1px solid #eee;
}
.header-flex h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 2px;
}
.header-sub {
    font-size: 13px;
    color: #888;
    margin: 0;
}
.date-badge {
    background: #fff;
    padding: 9px 16px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
    color: #444;
    border: 1px solid #eee;
    box-shadow: 0 2px 5px rgba(0,0,0,0.04);
}

/* ─── Stat cards ─────────────────────────────────────────── */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.stat-card {
    background: #fff;
    padding: 22px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid #eee;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    transition: box-shadow .2s;
}
.stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.stat-info h3 {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    margin-bottom: 5px;
}
.stat-info p {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
}

/* ─── Content cards ──────────────────────────────────────── */
.content-card {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #eee;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    margin-bottom: 24px;
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.card-header h3 {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a1a;
}
.card-badge {
    background: #f0fdf4;
    color: #088178;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #b2dfdb;
}
.card-link {
    font-size: 13px;
    color: #088178;
    text-decoration: none;
    font-weight: 600;
    display: flex; align-items: center; gap: 5px;
}
.card-link:hover { text-decoration: underline; }

/* ─── Chart legend ───────────────────────────────────────── */
.chart-legend {
    font-size: 12px;
    color: #666;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.legend-dot {
    display: inline-block;
    width: 10px; height: 10px;
    border-radius: 50%;
}

/* ─── Table ──────────────────────────────────────────────── */
.modern-table {
    width: 100%;
    border-collapse: collapse;
}
.modern-table th {
    text-align: left;
    background: #f9f9f9;
    padding: 13px 15px;
    color: #888;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.modern-table td {
    padding: 14px 15px;
    border-bottom: 1px solid #f5f5f5;
    font-size: 14px;
}
.modern-table tbody tr:hover { background: #fafffe; }
.bold { font-weight: 700; color: #1a1a1a; }
.text-muted { color: #999; font-size: 13px; }

/* ─── Status pills ───────────────────────────────────────── */
.status-pill {
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.status-pill.pending    { background: #fff9e6; color: #d4a017; }
.status-pill.delivered  { background: #eafff0; color: #2ecc71; }
.status-pill.processing { background: #e6f7ff; color: #3498db; }
.status-pill.cancelled  { background: #fff0f0; color: #e74c3c; }

/* ─── Access denied ──────────────────────────────────────── */
.access-denied {
    padding: 2rem;
    color: #e74c3c;
    font-size: 1.1rem;
    text-align: center;
}
</style>

</body>
</html>