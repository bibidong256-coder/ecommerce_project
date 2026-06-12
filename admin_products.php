<?php
session_start();
require "config/db.php";

// Protect admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ─── Handle Add ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name        = trim($_POST['name']);
    $brand       = trim($_POST['brand']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $page        = intval($_POST['page']);
    $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)) . '-' . time();
    $image       = '';

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], "shoes images/$filename")) $image = $filename;
    }

    $stmt = $conn->prepare("INSERT INTO products (name,slug,brand,description,price,stock,image,page) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$name,$slug,$brand,$description,$price,$stock,$image,$page]);
    $newId = $conn->lastInsertId();

    if (!empty($_FILES['extra_images']['name'][0])) {
        $imgStmt = $conn->prepare("INSERT INTO product_images (product_id,image,sort_order) VALUES (?,?,?)");
        foreach ($_FILES['extra_images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['extra_images']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['extra_images']['name'][$i], PATHINFO_EXTENSION);
                $fn  = "product_{$newId}_extra" . ($i+1) . ".$ext";
                if (move_uploaded_file($tmp, "shoes images/$fn")) $imgStmt->execute([$newId,$fn,$i+1]);
            }
        }
    }
    header("Location: admin_products.php?success=added"); exit;
}

// ─── Handle Edit ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id = intval($_POST['id']);
    $name=$_POST['name']; $brand=$_POST['brand']; $description=$_POST['description'];
    $price=floatval($_POST['price']); $stock=intval($_POST['stock']); $page=intval($_POST['page']);

    $ex = $conn->prepare("SELECT image FROM products WHERE id=?"); $ex->execute([$id]);
    $image = $ex->fetchColumn();

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fn  = 'product_' . time() . ".$ext";
        if (move_uploaded_file($_FILES['image']['tmp_name'], "shoes images/$fn")) $image = $fn;
    }

    $conn->prepare("UPDATE products SET name=?,brand=?,description=?,price=?,stock=?,image=?,page=? WHERE id=?")
         ->execute([$name,$brand,$description,$price,$stock,$image,$page,$id]);

    if (!empty($_FILES['extra_images']['name'][0])) {
        $cnt = $conn->prepare("SELECT COUNT(*) FROM product_images WHERE product_id=?"); $cnt->execute([$id]);
        $ec  = $cnt->fetchColumn();
        $imgStmt = $conn->prepare("INSERT INTO product_images (product_id,image,sort_order) VALUES (?,?,?)");
        foreach ($_FILES['extra_images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['extra_images']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['extra_images']['name'][$i], PATHINFO_EXTENSION);
                $fn  = "product_{$id}_extra" . ($ec+$i+1) . '_' . time() . ".$ext";
                if (move_uploaded_file($tmp, "shoes images/$fn")) $imgStmt->execute([$id,$fn,$ec+$i+1]);
            }
        }
    }
    header("Location: admin_products.php?success=edited"); exit;
}

// ─── Handle Delete ────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$id]);
    $conn->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    header("Location: admin_products.php?success=deleted"); exit;
}

// ─── Fetch data ───────────────────────────────────────────
$products        = $conn->query("SELECT * FROM products ORDER BY page ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
$total_products  = count($products);
$total_stock     = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;
$pending_orders  = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn() ?? 0;

// ─── Option C: fetch the actual low-stock products ────────
$low_stock_items = $conn->query(
    "SELECT id, name, brand, stock FROM products WHERE stock <= 5 ORDER BY stock ASC"
)->fetchAll(PDO::FETCH_ASSOC);
$low_stock_count = count($low_stock_items);

$pageStyles = [
    1  => ['bg'=>'#e8f5f4','color'=>'#088178','icon'=>'🏪','name'=>'Main Shop'],
    2  => ['bg'=>'#fff3e0','color'=>'#e67e22','icon'=>'📦','name'=>"More_shope"],
    3  => ['bg'=>'#fce4ec','color'=>'#c0392b','icon'=>'🛍️','name'=>"Men's Shoes"],
    4  => ['bg'=>'#e8eaf6','color'=>'#3949ab','icon'=>'🎯','name'=>'Women shoes'],
    5  => ['bg'=>'#f3e5f5','color'=>'#8e24aa','icon'=>'⭐','name'=>'Kids shoes'],
    6  => ['bg'=>'#e0f7fa','color'=>'#00838f','icon'=>'🔥','name'=>'Men_boots'],
    7  => ['bg'=>'#fff8e1','color'=>'#f9a825','icon'=>'💎','name'=>'Sneakers'],
    8  => ['bg'=>'#e8f5e9','color'=>'#2e7d32','icon'=>'🚀','name'=>'Gentals shoes'],
    9  => ['bg'=>'#fce4ec','color'=>'#ad1457','icon'=>'🎁','name'=>'flates'],
    10 => ['bg'=>'#e3f2fd','color'=>'#1565c0','icon'=>'👑','name'=>'Heels'],
    11 => ['bg'=>'#fbe9e7','color'=>'#d84315','icon'=>'⚡','name'=>'Women_boots'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="admin-container">

    <!-- ═══ SIDEBAR ════ -->
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
            <a href="admin_products.php" class="active">
                <span class="nav-icon"><i class="fas fa-shoe-prints"></i></span>
                <span class="nav-text">Products</span>
                <?php if($total_products > 0): ?>
                    <span class="nav-badge"><?= $total_products ?></span>
                <?php endif; ?>
            </a>
            <a href="admin_orders.php">
                <span class="nav-icon"><i class="fas fa-box"></i></span>
                <span class="nav-text">Orders</span>
                <?php if($pending_orders > 0): ?>
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

        <div class="admin-header">
            <div>
                <h2>Manage Products</h2>
                <p class="header-sub">Add, edit, or remove products from your store</p>
            </div>
            <button class="btn-add" onclick="openModal()">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <?php $msg = match($_GET['success']) {
                'added'   => '✅ Product added successfully!',
                'edited'  => '✅ Product updated successfully!',
                'deleted' => '🗑️ Product deleted.',
                default   => '✅ Done!'
            }; ?>
            <div class="alert-success"><?= $msg ?></div>
        <?php endif; ?>

        <!-- ══ STATS ══ -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(8,129,120,.1);color:#088178;">
                    <i class="fas fa-shoe-prints"></i>
                </div>
                <div class="stat-info"><h3>Total Products</h3><p><?= $total_products ?></p></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,152,219,.1);color:#3498db;">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info"><h3>Total Stock</h3><p><?= number_format($total_stock) ?></p></div>
            </div>

            <!-- ── Low Stock card — clickable, expands list ── -->
            <div class="stat-card low-stock-card <?= $low_stock_count > 0 ? 'has-issues' : '' ?>"
                 onclick="toggleLowStock()" style="<?= $low_stock_count > 0 ? 'cursor:pointer;' : '' ?>">
                <div class="stat-icon" style="background:rgba(231,76,60,.1);color:#e74c3c;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info" style="flex:1;">
                    <h3>Low Stock (≤ 5 pairs)</h3>
                    <?php if ($low_stock_count === 0): ?>
                        <p style="color:#27ae60;font-size:1rem;">✅ All good</p>
                    <?php else: ?>
                        <p>
                            <?= $low_stock_count ?> product<?= $low_stock_count > 1 ? 's' : '' ?>
                            <span class="low-stock-toggle-hint">
                                <i class="fas fa-chevron-down" id="lowStockChevron"></i>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Low Stock expanded list ── -->
        <?php if ($low_stock_count > 0): ?>
        <div class="low-stock-panel" id="lowStockPanel">
            <div class="low-stock-panel-header">
                <i class="fas fa-exclamation-triangle" style="color:#e74c3c;"></i>
                Products Running Low — restock soon
            </div>
            <table class="low-stock-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Pairs Left</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($low_stock_items as $item): ?>
                    <tr>
                        <td class="text-muted">#<?= $item['id'] ?></td>
                        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                        <td class="text-muted"><?= htmlspecialchars($item['brand']) ?></td>
                        <td>
                            <?php if ((int)$item['stock'] === 0): ?>
                                <span class="stock-badge out">OUT OF STOCK</span>
                            <?php else: ?>
                                <span class="stock-badge low"><?= $item['stock'] ?> left</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                // find the full product row so we can pass it to openEditModal
                                $fullRow = array_filter($products, fn($p) => $p['id'] == $item['id']);
                                $fullRow = reset($fullRow);
                                $pJson   = htmlspecialchars(json_encode($fullRow), ENT_QUOTES, 'UTF-8');
                            ?>
                            <button class="btn-edit" onclick='openEditModal(<?= $pJson ?>)'>
                                <i class="fas fa-pen"></i> Edit Stock
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- ══ FILTER + TABLE ══ -->
        <div class="filter-bar">
            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search by name or brand..." oninput="filterTable()">
            </div>
            <select id="pageFilter" onchange="filterTable()">
                <option value="">All Pages</option>
                <?php
                    $pages = array_unique(array_column($products, 'page'));
                    sort($pages);
                    foreach ($pages as $pg):
                        $lbl = isset($pageStyles[$pg]) ? $pageStyles[$pg]['icon'].' '.$pageStyles[$pg]['name'] : 'Page '.$pg;
                ?>
                    <option value="<?= $pg ?>"><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="admin-table" id="productsTable">
            <thead>
                <tr>
                    <th>ID</th><th>Image</th><th>Name</th><th>Brand</th>
                    <th>Price</th><th>Stock</th><th>Page</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p):
                $pg = max(1, (int)($p['page'] ?? 1));
                if (isset($pageStyles[$pg])) {
                    ['bg'=>$bg,'color'=>$color,'icon'=>$icon,'name'=>$pname] = $pageStyles[$pg];
                } else {
                    $hue=$pg*47%360; $bg="hsl($hue,60%,92%)"; $color="hsl($hue,60%,30%)"; $icon='📄'; $pname='Page '.$pg;
                }
                $pJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
            ?>
                <tr data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                    data-brand="<?= strtolower(htmlspecialchars($p['brand'])) ?>"
                    data-page="<?= $pg ?>">
                    <td class="text-muted">#<?= $p['id'] ?></td>
                    <td>
                        <img src="shoes images/<?= htmlspecialchars($p['image']) ?>"
                             class="product-img-thumb" alt=""
                             onerror="this.src='https://via.placeholder.com/52x52?text=?'">
                    </td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td class="text-muted"><?= htmlspecialchars($p['brand']) ?></td>
                    <td class="price">Shs <?= number_format($p['price']) ?></td>
                    <td>
                        <span class="<?= (int)$p['stock'] <= 5 ? 'stock-low' : 'stock-ok' ?>">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="page-badge" style="background:<?= $bg ?>;color:<?= $color ?>;">
                            <?= $icon ?> <?= $pname ?>
                        </span>
                    </td>
                    <td class="actions">
                        <button class="btn-edit" onclick='openEditModal(<?= $pJson ?>)'>
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button class="btn-view" onclick="window.open('product-details.html?id=<?= $p['id'] ?>','_blank')" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a class="btn-delete" href="admin_products.php?delete=<?= $p['id'] ?>"
                           onclick="return confirm('Delete «<?= htmlspecialchars($p['name']) ?>»?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<!-- ═══ MODAL ════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Product</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="productForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id"     id="formId"     value="">
            <div class="form-grid">
                <div class="form-col">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" id="fName" required placeholder="e.g. Air Max Classic">
                    </div>
                    <div class="form-group">
                        <label>Brand *</label>
                        <input type="text" name="brand" id="fBrand" required placeholder="e.g. Nike">
                    </div>
                    <div class="form-group">
                        <label>Price (UGX) *</label>
                        <input type="number" name="price" id="fPrice" required min="0" placeholder="e.g. 85000">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" id="fStock" required min="0" placeholder="e.g. 20">
                    </div>
                    <div class="form-group">
                        <label>Shop Page *</label>
                        <select name="page" id="fPage" required>
                            <?php foreach ($pageStyles as $num => $ps): ?>
                                <option value="<?= $num ?>"><?= $ps['icon'] ?> <?= $ps['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="fDesc" rows="4" placeholder="Describe the product..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Main Image</label>
                        <div class="upload-area" onclick="document.getElementById('fImage').click()">
                            <img id="mainPreview" src="" alt="" style="display:none;max-height:80px;border-radius:6px;">
                            <span id="uploadHint"><i class="fas fa-cloud-upload-alt"></i> Click to upload</span>
                        </div>
                        <input type="file" name="image" id="fImage" accept="image/*" style="display:none" onchange="previewMain(this)">
                    </div>
                    <div class="form-group">
                        <label>Extra Images <span style="color:#999;font-weight:400">(up to 3)</span></label>
                        <div class="upload-area" onclick="document.getElementById('fExtras').click()">
                            <div id="extraPreviews" class="extra-previews"></div>
                            <span id="extraHint"><i class="fas fa-images"></i> Click to upload multiple</span>
                        </div>
                        <input type="file" name="extra_images[]" id="fExtras" accept="image/*" multiple style="display:none" onchange="previewExtras(this)">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save" id="saveBtn">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </form>
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

/* ── Stats ── */
.stats-bar{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px}
.stat-card{
    background:#fff;padding:20px;border-radius:12px;
    display:flex;align-items:center;gap:16px;border:1px solid #eee;
    box-shadow:0 2px 6px rgba(0,0,0,.02);transition:box-shadow .2s,border-color .2s
}
.stat-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.06)}
.stat-card.has-issues{border-color:#fca5a5;}
.stat-card.has-issues:hover{box-shadow:0 4px 14px rgba(231,76,60,.12);border-color:#e74c3c;}
.stat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
.stat-info{flex:1}
.stat-info h3{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:5px}
.stat-info p{font-size:1.5rem;font-weight:700;color:#1a1a1a}
.low-stock-toggle-hint{margin-left:8px;font-size:1rem;color:#e74c3c;vertical-align:middle}
#lowStockChevron{transition:transform .25s ease}

/* ── Low Stock Panel ── */
.low-stock-panel{
    background:#fff;border:1.5px solid #fca5a5;border-radius:12px;
    margin-bottom:24px;overflow:hidden;
    animation:slideDown .22s ease;
}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.low-stock-panel-header{
    background:#fef2f2;padding:12px 20px;
    font-size:13px;font-weight:600;color:#e74c3c;
    display:flex;align-items:center;gap:8px;border-bottom:1px solid #fca5a5
}
.low-stock-table{width:100%;border-collapse:collapse;font-size:.87em}
.low-stock-table th{
    padding:10px 16px;background:#fff9f9;color:#999;
    font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;
    border-bottom:1px solid #fee2e2;text-align:left
}
.low-stock-table td{padding:11px 16px;border-bottom:1px solid #fff0f0;vertical-align:middle}
.low-stock-table tbody tr:last-child td{border-bottom:none}
.low-stock-table tbody tr:hover{background:#fff9f9}
.stock-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700}
.stock-badge.low{background:#fef9c3;color:#b45309}
.stock-badge.out{background:#fef2f2;color:#e74c3c;letter-spacing:.5px}

/* ── Alert ── */
.alert-success{
    padding:12px 18px;background:#e8f5f4;color:#088178;
    border:1px solid #b2dfdb;border-radius:8px;font-size:14px;font-weight:600;margin-bottom:20px
}

/* ── Filter ── */
.filter-bar{display:flex;gap:12px;margin-bottom:20px}
.search-wrap{flex:1;position:relative}
.search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:13px}
.search-wrap input{
    width:100%;padding:10px 14px 10px 36px;border:1px solid #dde1e7;
    border-radius:8px;font-size:14px;outline:none;transition:border-color .15s
}
.search-wrap input:focus{border-color:#088178}
.filter-bar select{
    padding:10px 14px;border:1px solid #dde1e7;border-radius:8px;
    font-size:14px;outline:none;background:#fff;transition:border-color .15s
}
.filter-bar select:focus{border-color:#088178}

/* ── Table ── */
.admin-table{
    width:100%;border-collapse:collapse;background:#fff;font-size:.88em;
    box-shadow:0 4px 12px rgba(0,0,0,.04);border-radius:12px;overflow:hidden
}
.admin-table thead tr{background:#088178;color:#fff;text-align:left}
.admin-table th,.admin-table td{padding:14px 16px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
.admin-table tbody tr:hover{background:#f9fffe}
.product-img-thumb{width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid #eee;display:block}
.text-muted{color:#999;font-size:.85em}
.price{font-weight:700;color:#088178}
.page-badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
.stock-low{background:#fef2f2;color:#e74c3c;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700}
.stock-ok{background:#f0fdf4;color:#27ae60;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700}

/* ── Buttons ── */
.actions{display:flex;gap:6px;align-items:center}
.btn-add{
    background:#088178;color:#fff;border:none;padding:10px 20px;border-radius:8px;
    font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:7px;transition:background .2s
}
.btn-add:hover{background:#066b63}
.btn-edit{
    background:#fff8e6;color:#b8860b;border:1px solid #f0d080;
    padding:6px 11px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;
    display:flex;align-items:center;gap:5px;transition:background .2s
}
.btn-edit:hover{background:#ffeea0}
.btn-view{
    background:#e8f4fd;color:#2980b9;border:1px solid #b8d8f0;
    padding:6px 10px;border-radius:6px;font-size:13px;cursor:pointer;transition:background .2s
}
.btn-view:hover{background:#cce8f8}
.btn-delete{
    background:#fef2f2;color:#e74c3c;border:1px solid #fcc;
    text-decoration:none;padding:6px 10px;border-radius:6px;font-size:13px;cursor:pointer;transition:background .2s
}
.btn-delete:hover{background:#fee}

/* ── Modal ── */
.modal-overlay{
    display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);
    z-index:9999;align-items:center;justify-content:center;padding:20px
}
.modal-overlay.open{display:flex}
.modal-box{
    background:#fff;border-radius:14px;width:100%;max-width:780px;
    max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);
    animation:slideUp .22s ease
}
@keyframes slideUp{from{transform:translateY(24px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{
    display:flex;justify-content:space-between;align-items:center;
    padding:20px 28px;border-bottom:1px solid #eee;
    position:sticky;top:0;background:#fff;z-index:1;border-radius:14px 14px 0 0
}
.modal-header h3{font-size:17px;color:#1a1a1a}
.modal-close{background:none;border:none;font-size:22px;cursor:pointer;color:#aaa;transition:color .15s}
.modal-close:hover{color:#e74c3c}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 28px;padding:24px 28px 0}
@media(max-width:600px){.form-grid{grid-template-columns:1fr}}
.form-group{margin-bottom:16px}
.form-group label{
    display:block;font-size:11.5px;font-weight:600;color:#555;
    margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px
}
.form-group input,.form-group select,.form-group textarea{
    width:100%;padding:10px 12px;border:1px solid #dde1e7;border-radius:8px;
    font-size:14px;outline:none;transition:border-color .15s,box-shadow .15s;font-family:inherit
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{
    border-color:#088178;box-shadow:0 0 0 3px rgba(8,129,120,.1)
}
.form-group textarea{resize:vertical}
.upload-area{
    border:2px dashed #dde1e7;border-radius:8px;padding:16px;text-align:center;
    cursor:pointer;transition:border-color .15s,background .15s;min-height:60px;
    display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:8px
}
.upload-area:hover{border-color:#088178;background:#f0faf9}
.upload-area i{font-size:1.3rem;color:#088178}
#uploadHint,#extraHint{font-size:13px;color:#888}
.extra-previews{display:flex;gap:8px;flex-wrap:wrap}
.extra-previews img{width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #eee}
.modal-footer{
    display:flex;justify-content:flex-end;gap:12px;
    padding:20px 28px;border-top:1px solid #eee;margin-top:8px
}
.btn-cancel{
    background:#f4f7f6;color:#555;border:1px solid #dde1e7;
    padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s
}
.btn-cancel:hover{background:#eee}
.btn-save{
    background:#088178;color:#fff;border:none;padding:10px 24px;border-radius:8px;
    font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:7px;transition:background .15s
}
.btn-save:hover{background:#066b63}
</style>

<script>
// ── Low Stock panel toggle ──────────────────────────────
let lowStockOpen = false;
const panel    = document.getElementById('lowStockPanel');
const chevron  = document.getElementById('lowStockChevron');

function toggleLowStock() {
    if (!panel) return;
    lowStockOpen = !lowStockOpen;
    panel.style.display = lowStockOpen ? 'block' : 'none';
    if (chevron) chevron.style.transform = lowStockOpen ? 'rotate(180deg)' : 'rotate(0)';
}

// Start collapsed
if (panel) panel.style.display = 'none';

// ── Product modal ───────────────────────────────────────
function openModal() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('formAction').value = 'add';
    document.getElementById('formId').value = '';
    document.getElementById('saveBtn').innerHTML = '<i class="fas fa-plus"></i> Add Product';
    document.getElementById('productForm').reset();
    resetPreviews();
    document.getElementById('modalOverlay').classList.add('open');
}

function openEditModal(p) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formId').value = p.id;
    document.getElementById('saveBtn').innerHTML = '<i class="fas fa-save"></i> Save Changes';
    document.getElementById('fName').value  = p.name        || '';
    document.getElementById('fBrand').value = p.brand       || '';
    document.getElementById('fDesc').value  = p.description || '';
    document.getElementById('fPrice').value = p.price       || '';
    document.getElementById('fStock').value = p.stock       || '';
    document.getElementById('fPage').value  = p.page        || 1;
    resetPreviews();
    if (p.image) {
        const prev = document.getElementById('mainPreview');
        prev.src = 'shoes images/' + p.image;
        prev.style.display = 'block';
        document.getElementById('uploadHint').style.display = 'none';
    }
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal(e) {
    if (e && e.target !== document.getElementById('modalOverlay')) return;
    document.getElementById('modalOverlay').classList.remove('open');
}

function previewMain(input) {
    if (!input.files[0]) return;
    const prev = document.getElementById('mainPreview');
    prev.src = URL.createObjectURL(input.files[0]);
    prev.style.display = 'block';
    document.getElementById('uploadHint').style.display = 'none';
}

function previewExtras(input) {
    const container = document.getElementById('extraPreviews');
    const hint = document.getElementById('extraHint');
    container.innerHTML = '';
    Array.from(input.files).slice(0,3).forEach(f => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(f);
        container.appendChild(img);
    });
    hint.style.display = container.children.length ? 'none' : 'inline';
}

function resetPreviews() {
    const prev = document.getElementById('mainPreview');
    prev.src = ''; prev.style.display = 'none';
    document.getElementById('uploadHint').style.display = 'inline';
    document.getElementById('extraPreviews').innerHTML = '';
    document.getElementById('extraHint').style.display = 'inline';
}

function filterTable() {
    const s = document.getElementById('searchInput').value.toLowerCase();
    const pg = document.getElementById('pageFilter').value;
    document.querySelectorAll('#productsTable tbody tr').forEach(r => {
        const ms = (r.dataset.name||'').includes(s)||(r.dataset.brand||'').includes(s);
        const mp = pg===''||r.dataset.page===pg;
        r.style.display = ms&&mp ? '' : 'none';
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('modalOverlay').classList.remove('open');
});
</script>

</body>
</html>