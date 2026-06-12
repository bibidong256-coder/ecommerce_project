<?php
// admin_index.php — Homepage Manager with image upload support
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once './config/db.php';

// ══════════════════════════════════════════════════════
// SHARED UPLOAD HELPER
function handleImageUpload(string $fieldName): ?string {
    if (empty($_FILES[$fieldName]['name'])) return null;
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) return null;

    $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
    $maxBytes = 5 * 1024 * 1024;
    $tmpPath  = $_FILES[$fieldName]['tmp_name'];
    $mime     = mime_content_type($tmpPath);

    if (!in_array($mime, $allowed)) return null;
    if ($_FILES[$fieldName]['size'] > $maxBytes) return null;

    $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    $dir     = __DIR__ . '/shoes images/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = $dir . $filename;

    if (!move_uploaded_file($tmpPath, $dest)) return null;

    return 'shoes images/uploads/' . $filename;
}

// ══════════════════════════════════════════════════════
// AJAX endpoint
// ══════════════════════════════════════════════════════
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    try {
        $uploadedPath = handleImageUpload('image_file');
        $imagePath    = $uploadedPath ?? trim($_POST['image_path'] ?? '');

        if ($action === 'save_hero') {
            $conn->prepare("UPDATE hero SET badge_text=?,heading=?,subheading=?,description=?,button_text=?,button_url=?,image_path=? WHERE id=1")
                 ->execute([$_POST['badge_text'],$_POST['heading'],$_POST['subheading'],
                            $_POST['description'],$_POST['button_text'],$_POST['button_url'],
                            $imagePath]);
            echo json_encode(['ok'=>true,'msg'=>'Hero section updated!','image_path'=>$imagePath]);
        }
        elseif ($action === 'save_tags') {
            $conn->exec("DELETE FROM trending_tags");
            foreach ($_POST['tags'] as $i => $tag) {
                if (trim($tag) === '') continue;
                $conn->prepare("INSERT INTO trending_tags (tag_name, sort_order) VALUES (?,?)")
                     ->execute([trim($tag), $i+1]);
            }
            echo json_encode(['ok'=>true,'msg'=>'Trending tags updated!']);
        }
        elseif ($action === 'save_product') {
            $id   = (int)($_POST['product_id'] ?? 0);
            $data = [$_POST['title'],$imagePath,(int)$_POST['rating_stars'],
                     (int)$_POST['rating_count'],(float)$_POST['price'],
                     $_POST['product_url'],(int)$_POST['sort_order']];
            if ($id > 0) {
                $conn->prepare("UPDATE featured_products SET title=?,image_path=?,rating_stars=?,rating_count=?,price=?,product_url=?,sort_order=? WHERE id=?")
                     ->execute([...$data,$id]);
            } else {
                $conn->prepare("INSERT INTO featured_products (title,image_path,rating_stars,rating_count,price,product_url,sort_order) VALUES (?,?,?,?,?,?,?)")
                     ->execute($data);
            }
            echo json_encode(['ok'=>true,'msg'=>'Product saved!','image_path'=>$imagePath]);
        }
        elseif ($action === 'delete_product') {
            $conn->prepare("DELETE FROM featured_products WHERE id=?")->execute([(int)$_POST['product_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Product deleted.']);
        }
        elseif ($action === 'toggle_product') {
            $conn->prepare("UPDATE featured_products SET is_active = NOT is_active WHERE id=?")->execute([(int)$_POST['product_id']]);
            $row = $conn->prepare("SELECT is_active FROM featured_products WHERE id=?");
            $row->execute([(int)$_POST['product_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Visibility toggled.','state'=>(bool)$row->fetchColumn()]);
        }
        elseif ($action === 'save_category') {
            $id   = (int)($_POST['cat_id'] ?? 0);
            $data = [$_POST['label'],$imagePath,$_POST['link_url'],
                     (int)$_POST['is_highlight'],$_POST['section'],(int)$_POST['sort_order']];
            if ($id > 0) {
                $conn->prepare("UPDATE home_categories SET label=?,image_path=?,link_url=?,is_highlight=?,section=?,sort_order=? WHERE id=?")
                     ->execute([...$data,$id]);
            } else {
                $conn->prepare("INSERT INTO home_categories (label,image_path,link_url,is_highlight,section,sort_order) VALUES (?,?,?,?,?,?)")
                     ->execute($data);
            }
            echo json_encode(['ok'=>true,'msg'=>'Category saved!','image_path'=>$imagePath]);
        }
        elseif ($action === 'delete_category') {
            $conn->prepare("DELETE FROM home_categories WHERE id=?")->execute([(int)$_POST['cat_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Category deleted.']);
        }
        elseif ($action === 'save_featurebox') {
            $id   = (int)($_POST['fb_id'] ?? 0);
            $data = [$_POST['title'],$imagePath,(int)$_POST['sort_order']];
            if ($id > 0) {
                $conn->prepare("UPDATE feature_boxes SET title=?,image_path=?,sort_order=? WHERE id=?")
                     ->execute([...$data,$id]);
            } else {
                $conn->prepare("INSERT INTO feature_boxes (title,image_path,sort_order) VALUES (?,?,?)")
                     ->execute($data);
            }
            echo json_encode(['ok'=>true,'msg'=>'Feature box saved!','image_path'=>$imagePath]);
        }
        elseif ($action === 'delete_featurebox') {
            $conn->prepare("DELETE FROM feature_boxes WHERE id=?")->execute([(int)$_POST['fb_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Feature box deleted.']);
        }
        elseif ($action === 'save_mainbanner') {
            $conn->prepare("UPDATE main_banner SET badge_text=?,heading=?,button_text=?,button_url=? WHERE id=1")
                 ->execute([$_POST['badge_text'],$_POST['heading'],$_POST['button_text'],$_POST['button_url']]);
            echo json_encode(['ok'=>true,'msg'=>'Main banner updated!']);
        }
        elseif ($action === 'save_smallbanner') {
            $id   = (int)($_POST['sb_id'] ?? 0);
            $data = [$_POST['badge_text'],$_POST['heading'],$_POST['description'],
                     $_POST['button_text'],$_POST['button_url'],
                     (int)$_POST['style_variant'],(int)$_POST['sort_order']];
            if ($id > 0) {
                $conn->prepare("UPDATE small_banners SET badge_text=?,heading=?,description=?,button_text=?,button_url=?,style_variant=?,sort_order=? WHERE id=?")
                     ->execute([...$data,$id]);
            } else {
                $conn->prepare("INSERT INTO small_banners (badge_text,heading,description,button_text,button_url,style_variant,sort_order) VALUES (?,?,?,?,?,?,?)")
                     ->execute($data);
            }
            echo json_encode(['ok'=>true,'msg'=>'Small banner saved!']);
        }
        elseif ($action === 'delete_smallbanner') {
            $conn->prepare("DELETE FROM small_banners WHERE id=?")->execute([(int)$_POST['sb_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Small banner deleted.']);
        }
        elseif ($action === 'save_seasonal') {
            $id   = (int)($_POST['sea_id'] ?? 0);
            $data = [$_POST['heading'],$_POST['subheading'],$_POST['style_class'],(int)$_POST['sort_order']];
            if ($id > 0) {
                $conn->prepare("UPDATE seasonal_banners SET heading=?,subheading=?,style_class=?,sort_order=? WHERE id=?")
                     ->execute([...$data,$id]);
            } else {
                $conn->prepare("INSERT INTO seasonal_banners (heading,subheading,style_class,sort_order) VALUES (?,?,?,?)")
                     ->execute($data);
            }
            echo json_encode(['ok'=>true,'msg'=>'Seasonal banner saved!']);
        }
        elseif ($action === 'delete_seasonal') {
            $conn->prepare("DELETE FROM seasonal_banners WHERE id=?")->execute([(int)$_POST['sea_id']]);
            echo json_encode(['ok'=>true,'msg'=>'Seasonal banner deleted.']);
        }
        elseif ($action === 'save_newsletter') {
            $conn->prepare("UPDATE newsletter_settings SET badge_text=?,heading=?,placeholder_text=?,button_text=? WHERE id=1")
                 ->execute([$_POST['badge_text'],$_POST['heading'],$_POST['placeholder_text'],$_POST['button_text']]);
            echo json_encode(['ok'=>true,'msg'=>'Newsletter settings updated!']);
        }
        else {
            echo json_encode(['ok'=>false,'msg'=>'Unknown action.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok'=>false,'msg'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

// ══════════════════════════════════════════════════════
// Normal page load — fetch data
// ══════════════════════════════════════════════════════
$hero       = $conn->query("SELECT * FROM hero LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$tags       = $conn->query("SELECT * FROM trending_tags ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$products   = $conn->query("SELECT * FROM featured_products ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$categories = $conn->query("SELECT * FROM home_categories WHERE section='category' ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$trends     = $conn->query("SELECT * FROM home_categories WHERE section='trend' ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$fboxes     = $conn->query("SELECT * FROM feature_boxes ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mainBanner = $conn->query("SELECT * FROM main_banner LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$smBanners  = $conn->query("SELECT * FROM small_banners ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$seasonal   = $conn->query("SELECT * FROM seasonal_banners ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$newsletter = $conn->query("SELECT * FROM newsletter_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nlCount    = $conn->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active=1")->fetchColumn();
$nlSubs     = $conn->query("SELECT email, subscribed_at FROM newsletter_subscribers WHERE is_active=1 ORDER BY subscribed_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Homepage Manager — Kisken Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
    --p:#088178;--dk:#04534e;--ac:#e8f5f4;--bg:#f4f7f6;--bd:#dde3e8;
    --r:12px;--sh:0 2px 8px rgba(0,0,0,.03);
    --sidebar-w:230px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Roboto,sans-serif;background:var(--bg);color:#333;display:flex;min-height:100vh;}

/* ══════════════════════════════════════════
   SIDEBAR — matches admin_about.php style
   ══════════════════════════════════════════ */
.admin-nav{
    width:var(--sidebar-w);
    background:#0f0f0f;
    display:flex;
    flex-direction:column;
    position:fixed;
    height:100vh;
    z-index:1000;
    overflow-y:auto;
    overflow-x:hidden;
}

/* Brand */
.nav-brand{
    display:flex;align-items:center;gap:10px;
    padding:20px 18px 18px;
    border-bottom:1px solid rgba(255,255,255,.06);
    flex-shrink:0;
}
.nav-brand-icon{
    width:34px;height:34px;
    background:var(--p);border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:15px;flex-shrink:0;
}
.nav-brand-name{font-size:13px;font-weight:600;color:#fff;line-height:1.3;}
.nav-brand-sub{font-size:10.5px;color:rgba(255,255,255,.28);}

/* Section labels */
.nav-label{
    font-size:10px;font-weight:600;
    color:rgba(255,255,255,.22);
    letter-spacing:.1em;text-transform:uppercase;
    padding:16px 18px 5px;
}

/* Nav links */
.nav-links{padding:0 8px;}
.nav-links a{
    display:flex;align-items:center;gap:10px;
    padding:9px 12px;border-radius:9px;
    text-decoration:none;
    color:rgba(255,255,255,.45);
    font-size:13.5px;
    margin-bottom:1px;
    transition:background .15s,color .15s;
    position:relative;
}
.nav-links a:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85);}
.nav-links a.active{background:rgba(8,129,120,.2);color:#fff;}
.nav-links a.active .nav-icon{color:#0bbfb4;}
.nav-links a.active::before{
    content:'';position:absolute;left:0;top:20%;bottom:20%;
    width:3px;background:var(--p);border-radius:0 3px 3px 0;
}
.nav-icon{width:20px;text-align:center;font-size:14px;flex-shrink:0;color:inherit;}
.nav-text{flex:1;}
.nav-badge{
    margin-left:auto;
    background:var(--p);color:#fff;
    font-size:10px;font-weight:700;
    padding:2px 8px;border-radius:20px;
    line-height:1.7;flex-shrink:0;
}
.nav-badge.danger{background:rgba(231,76,60,.2);color:#e74c3c;}

/* Footer */
.nav-footer{
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,.06);
    padding:12px 8px 10px;
    flex-shrink:0;
}
.nav-user{
    display:flex;align-items:center;gap:10px;
    padding:8px 12px;border-radius:9px;margin-bottom:2px;
}
.nav-avatar{
    width:30px;height:30px;border-radius:50%;
    background:var(--p);
    display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:600;color:#fff;flex-shrink:0;
}
.nav-user-name{font-size:12.5px;font-weight:600;color:#fff;line-height:1.3;}
.nav-user-role{font-size:10.5px;color:rgba(255,255,255,.28);}
.nav-logout{
    display:flex;align-items:center;gap:10px;
    padding:9px 12px;border-radius:9px;
    text-decoration:none;
    color:rgba(231,76,60,.75);
    font-size:13.5px;
    transition:background .15s,color .15s;
}
.nav-logout:hover{background:rgba(231,76,60,.1);color:#e74c3c;}
.nav-preview{
    display:flex;align-items:center;gap:10px;
    padding:9px 12px;border-radius:9px;
    text-decoration:none;
    color:rgba(255,255,255,.45);
    font-size:13.5px;
    transition:background .15s,color .15s;
}
.nav-preview:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85);}

/* ══════════════════════════════════════════
   MAIN CONTENT
   ══════════════════════════════════════════ */
.main{margin-left:var(--sidebar-w);flex:1;padding:0 32px 48px;min-width:0;}

/* Fixed header block: title + tab bar */
.main-header{
    position:fixed;
    top:0;
    left:var(--sidebar-w);
    right:0;
    z-index:500;
    background:rgba(244,247,246,.97);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
    border-bottom:1px solid var(--bd);
    padding:0 32px;
}
.topbar{
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 0 10px;flex-wrap:wrap;gap:10px;
}
.topbar h2{font-size:1.3rem;font-weight:800;color:var(--dk);}

/* Section tab bar */
.section-tabs{
    display:flex;gap:4px;
    padding-bottom:12px;
    overflow-x:auto;
    scrollbar-width:none;
}
.section-tabs::-webkit-scrollbar{display:none;}
.section-tabs a{
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 14px;
    border-radius:8px;
    border:1.5px solid var(--bd);
    background:#fff;
    color:#666;
    font-size:.78rem;font-weight:600;
    text-decoration:none;
    white-space:nowrap;
    transition:all .18s;
    flex-shrink:0;
}
.section-tabs a i{font-size:.75rem;}
.section-tabs a:hover{background:var(--dk);color:#fff;border-color:var(--dk);}
.section-tabs a.active{background:var(--p);color:#fff;border-color:var(--p);}

/* Push content below the fixed header */
.main-content-offset{height:110px;}

/* Toast */
#toast{
    position:fixed;top:20px;right:24px;z-index:9999;
    display:none;padding:13px 22px;border-radius:10px;
    font-size:.9rem;font-weight:700;
    box-shadow:0 4px 20px rgba(0,0,0,.15);
    transition:opacity .3s;
}
#toast.ok{background:var(--p);color:#fff;}
#toast.err{background:#e74c3c;color:#fff;}

/* Card */
.card{
    background:#fff;border-radius:var(--r);
    box-shadow:var(--sh);padding:24px;
    margin-bottom:24px;scroll-margin-top:120px;
    border:1px solid #eee;
    transition:box-shadow .2s;
}
.card:hover{box-shadow:0 4px 16px rgba(0,0,0,.07);}
.card h2{font-size:1.05rem;font-weight:800;color:var(--dk);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--ac);}

/* Form */
.fg{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:14px;}
.f{display:flex;flex-direction:column;gap:5px;}
.f label{font-size:.8rem;font-weight:600;color:#555;}
.f input[type=text],.f input[type=number],.f input[type=url],
.f textarea,.f select{
    padding:9px 13px;border:1.5px solid var(--bd);
    border-radius:8px;font-size:.88rem;outline:none;
    font-family:inherit;transition:border-color .2s;
}
.f input:focus,.f textarea:focus,.f select:focus{border-color:var(--p);}
.f textarea{resize:vertical;min-height:65px;}
.full{grid-column:1/-1;}

/* Image Upload Widget */
.img-upload-widget{display:flex;flex-direction:column;gap:6px;}
.img-upload-widget .current-img{
    width:64px;height:64px;object-fit:cover;border-radius:8px;
    border:2px solid var(--bd);display:none;
}
.img-upload-widget .current-img.show{display:block;}
.img-upload-widget .upload-row{display:flex;gap:6px;align-items:center;flex-wrap:wrap;}
.img-upload-widget input[type=text]{flex:1;min-width:120px;}
.img-upload-widget .or-divider{font-size:.72rem;color:#aaa;font-weight:700;white-space:nowrap;}
.file-pick-btn{
    display:inline-flex;align-items:center;gap:5px;
    padding:7px 12px;background:var(--ac);color:var(--dk);
    border:1.5px solid var(--p);border-radius:8px;
    font-size:.78rem;font-weight:700;cursor:pointer;
    transition:background .18s;white-space:nowrap;
}
.file-pick-btn:hover{background:#c8ebe8;}
.file-pick-btn input[type=file]{display:none;}
.upload-hint{font-size:.72rem;color:#aaa;}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:5px;padding:9px 20px;border:none;border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;}
.bp{background:var(--p);color:#fff;} .bp:hover{background:var(--dk);}
.bd2{background:#e74c3c;color:#fff;} .bd2:hover{background:#c0392b;}
.bs{background:#ecf0f1;color:#555;} .bs:hover{background:#dfe6e9;}
.sm{padding:5px 12px;font-size:.78rem;}
.btn:disabled{opacity:.6;cursor:not-allowed;}

/* Table */
table{width:100%;border-collapse:collapse;font-size:.85rem;}
th{background:var(--ac);color:var(--dk);font-weight:700;padding:9px 12px;text-align:left;}
td{padding:9px 12px;border-bottom:1px solid var(--bd);vertical-align:middle;}
tr:hover td{background:#fafcfc;}
.badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:.73rem;font-weight:700;}
.bg{background:#d5f5e3;color:#1e8449;} .bgr{background:#ecf0f1;color:#7f8c8d;}
img.th{width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--bd);}

/* Edit panel */
.ep{display:none;margin-top:18px;padding-top:18px;border-top:2px solid var(--ac);}

/* Responsive */
@media(max-width:720px){
    .admin-nav{width:60px;}
    .nav-brand-name,.nav-brand-sub,.nav-text,.nav-label,
    .nav-user-name,.nav-user-role{display:none;}
    .nav-brand{padding:14px 0;justify-content:center;}
    .nav-brand-icon{margin:0;}
    .nav-links a{padding:12px;justify-content:center;}
    .nav-links a.active::before{display:none;}
    .nav-footer{align-items:center;padding:10px 0;}
    .nav-user{justify-content:center;}
    .nav-logout,.nav-preview{padding:12px;justify-content:center;}
    .main{margin-left:60px;padding:0 12px 40px;}
    .main-header{left:60px;padding:0 12px;}
    .main-content-offset{height:120px;}
}
</style>
</head>
<body>

<div id="toast"></div>

<!-- ══════════════════════════════════════════
     SIDEBAR — dark style matching admin_about
     ══════════════════════════════════════════ -->
<aside class="admin-nav">

    <div class="nav-brand">
        <div class="nav-brand-icon">
            <i class="fas fa-home" aria-hidden="true"></i>
        </div>
        <div>
            <p class="nav-brand-name">Homepage Mgr</p>
            <p class="nav-brand-sub">Kisken Admin</p>
        </div>
    </div>

    <p class="nav-label">Main</p>
    <nav class="nav-links">
        <a href="admin_dashboard.php">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="admin_products.php">
            <span class="nav-icon"><i class="fas fa-shoe-prints"></i></span>
            <span class="nav-text">Products</span>
            <?php if ($total_products > 0): ?>
                <span class="nav-badge"><?= $total_products ?></span>
            <?php endif; ?>
        </a>
        <a href="admin_orders.php">
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
        <a href="index.php" target="_blank" class="nav-preview">
            <span class="nav-icon"><i class="fas fa-eye"></i></span>
            <span class="nav-text">View Homepage</span>
        </a>
        <a href="logout.php" class="nav-logout">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
            <span class="nav-text">Logout</span>
        </a>
    </div>

</aside>

<!-- ══════════════════════════════════════════
     MAIN CONTENT
     ══════════════════════════════════════════ -->
<main class="main">
    <!-- Fixed header: title + section tab bar -->
    <div class="main-header">
        <div class="topbar"><h2>Homepage Manager</h2></div>
        <nav class="section-tabs" id="section-nav">
            <a href="#hero"><i class="fas fa-image"></i><span>Hero</span></a>
            <a href="#tags"><i class="fas fa-tags"></i><span>Trending Tags</span></a>
            <a href="#products"><i class="fas fa-shoe-prints"></i><span>Products</span></a>
            <a href="#categories"><i class="fas fa-folder-open"></i><span>Categories</span></a>
            <a href="#fboxes"><i class="fas fa-star"></i><span>Feature Boxes</span></a>
            <a href="#mbanner"><i class="fas fa-bullhorn"></i><span>Main Banner</span></a>
            <a href="#sbanners"><i class="fas fa-rectangle-ad"></i><span>Small Banners</span></a>
            <a href="#seasonal"><i class="fas fa-leaf"></i><span>Seasonal</span></a>
            <a href="#newsletter"><i class="fas fa-envelope"></i><span>Newsletter</span></a>
        </nav>
    </div>
    <!-- spacer so content starts below fixed header -->
    <div class="main-content-offset"></div>

    <!-- ── HERO ── -->
    <div id="hero" class="card">
        <h2>🖼️ Hero Section</h2>
        <form data-ajax enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_hero">
        <div class="fg">
            <div class="f"><label>Badge Text</label><input type="text" name="badge_text" value="<?= htmlspecialchars($hero['badge_text']) ?>"></div>
            <div class="f"><label>Heading</label><input type="text" name="heading" value="<?= htmlspecialchars($hero['heading']) ?>"></div>
            <div class="f"><label>Big Subheading</label><input type="text" name="subheading" value="<?= htmlspecialchars($hero['subheading']) ?>"></div>
            <div class="f"><label>Button Text</label><input type="text" name="button_text" value="<?= htmlspecialchars($hero['button_text']) ?>"></div>
            <div class="f"><label>Button URL</label><input type="text" name="button_url" value="<?= htmlspecialchars($hero['button_url']) ?>"></div>
            <div class="f full">
                <label>Hero Image</label>
                <?php imgWidget('hero-img', $hero['image_path']); ?>
            </div>
            <div class="f full"><label>Description</label><textarea name="description"><?= htmlspecialchars($hero['description']) ?></textarea></div>
        </div>
        <br><button type="submit" class="btn bp">💾 Save Hero</button>
        </form>
    </div>

    <!-- ── TRENDING TAGS ── -->
    <div id="tags" class="card">
        <h2>🏷️ Trending Tags</h2>
        <form data-ajax>
        <input type="hidden" name="action" value="save_tags">
        <div class="fg">
            <?php foreach ($tags as $i => $tag): ?>
            <div class="f"><label>Tag <?= $i+1 ?></label><input type="text" name="tags[]" value="<?= htmlspecialchars($tag['tag_name']) ?>"></div>
            <?php endforeach; ?>
            <div class="f"><label>+ New Tag</label><input type="text" name="tags[]" value="" placeholder="Add tag…"></div>
        </div>
        <br><button type="submit" class="btn bp">💾 Save Tags</button>
        </form>
    </div>

    <!-- ── PRODUCTS ── -->
    <div id="products" class="card">
        <h2>👟 Featured / Viral Products</h2>
        <table>
            <tr><th>Img</th><th>Title</th><th>Price</th><th>Stars</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($products as $p): ?>
            <tr id="prod-row-<?= $p['id'] ?>">
                <td><img class="th" src="<?= htmlspecialchars($p['image_path']) ?>" onerror="this.src='https://placehold.co/44'" id="prod-img-<?= $p['id'] ?>"></td>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td>shs<?= number_format($p['price']) ?></td>
                <td><?= $p['rating_stars'] ?>★ (<?= $p['rating_count'] ?>)</td>
                <td><span class="badge <?= $p['is_active'] ? 'bg' : 'bgr' ?>" id="prod-status-<?= $p['id'] ?>"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td style="display:flex;gap:5px;flex-wrap:wrap;">
                    <button class="btn bs sm" onclick="ep('pf');fillP(<?= htmlspecialchars(json_encode($p)) ?>)">✏️ Edit</button>
                    <button class="btn sm" style="background:#f39c12;color:#fff;" onclick="toggleProduct(<?= $p['id'] ?>, this)"><?= $p['is_active'] ? '🙈' : '👁️' ?></button>
                    <button class="btn bd2 sm" onclick="deleteProduct(<?= $p['id'] ?>, this)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br><button class="btn bp sm" onclick="ep('pf');fillP(null)">➕ Add Product</button>
        <div id="pf" class="ep">
            <form data-ajax enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_product">
            <input type="hidden" name="product_id" id="p-id">
            <div class="fg">
                <div class="f"><label>Title</label><input type="text" name="title" id="p-name" required></div>
                <div class="f full">
                    <label>Product Image</label>
                    <?php imgWidget('p-img-widget', ''); ?>
                </div>
                <div class="f"><label>Price (UGX)</label><input type="number" name="price" id="p-price"></div>
                <div class="f"><label>Product URL</label><input type="text" name="product_url" id="p-url"></div>
                <div class="f"><label>Stars (1-5)</label><input type="number" name="rating_stars" id="p-stars" min="1" max="5" value="5"></div>
                <div class="f"><label>Rating Count</label><input type="number" name="rating_count" id="p-rc" value="0"></div>
                <div class="f"><label>Sort Order</label><input type="number" name="sort_order" id="p-sort" value="1"></div>
            </div>
            <br><button type="submit" class="btn bp">💾 Save</button>
            <button type="button" class="btn bs" onclick="ep('pf',true)">Cancel</button>
            </form>
        </div>
    </div>

    <!-- ── CATEGORIES ── -->
    <div id="categories" class="card">
        <h2>📂 Categories &amp; Trends</h2>
        <table>
            <tr><th>Img</th><th>Label</th><th>Section</th><th>Hot?</th><th>Actions</th></tr>
            <?php foreach (array_merge($categories, $trends) as $c): ?>
            <tr id="cat-row-<?= $c['id'] ?>">
                <td><img class="th" src="<?= htmlspecialchars($c['image_path']) ?>" onerror="this.src='https://placehold.co/44'" id="cat-img-<?= $c['id'] ?>"></td>
                <td><?= htmlspecialchars($c['label']) ?></td>
                <td><span class="badge <?= $c['section']==='category'?'bg':'bgr' ?>"><?= $c['section'] ?></span></td>
                <td><?= $c['is_highlight'] ? '🔥' : '—' ?></td>
                <td style="display:flex;gap:5px;">
                    <button class="btn bs sm" onclick="ep('cf');fillC(<?= htmlspecialchars(json_encode($c)) ?>)">✏️</button>
                    <button class="btn bd2 sm" onclick="deleteCategory(<?= $c['id'] ?>, this)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br><button class="btn bp sm" onclick="ep('cf');fillC(null)">➕ Add</button>
        <div id="cf" class="ep">
            <form data-ajax enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_category">
            <input type="hidden" name="cat_id" id="c-id">
            <div class="fg">
                <div class="f"><label>Label</label><input type="text" name="label" id="c-lbl" required></div>
                <div class="f full">
                    <label>Category Image</label>
                    <?php imgWidget('c-img-widget', ''); ?>
                </div>
                <div class="f"><label>Link URL</label><input type="text" name="link_url" id="c-url"></div>
                <div class="f"><label>Section</label><select name="section" id="c-sec"><option value="category">Category</option><option value="trend">Trend</option></select></div>
                <div class="f"><label>Sort Order</label><input type="number" name="sort_order" id="c-sort" value="1"></div>
                <div class="f"><label>HOT badge?</label><select name="is_highlight" id="c-hot"><option value="0">No</option><option value="1">Yes 🔥</option></select></div>
            </div>
            <br><button type="submit" class="btn bp">💾 Save</button>
            <button type="button" class="btn bs" onclick="ep('cf',true)">Cancel</button>
            </form>
        </div>
    </div>

    <!-- ── FEATURE BOXES ── -->
    <div id="fboxes" class="card">
        <h2>⭐ Feature Boxes</h2>
        <table>
            <tr><th>Img</th><th>Title</th><th>Order</th><th>Actions</th></tr>
            <?php foreach ($fboxes as $fb): ?>
            <tr id="fb-row-<?= $fb['id'] ?>">
                <td><img class="th" src="<?= htmlspecialchars($fb['image_path']) ?>" onerror="this.src='https://placehold.co/44'" id="fb-img-<?= $fb['id'] ?>"></td>
                <td><?= htmlspecialchars($fb['title']) ?></td>
                <td><?= $fb['sort_order'] ?></td>
                <td style="display:flex;gap:5px;">
                    <button class="btn bs sm" onclick="ep('ff');fillF(<?= htmlspecialchars(json_encode($fb)) ?>)">✏️</button>
                    <button class="btn bd2 sm" onclick="deleteFeatureBox(<?= $fb['id'] ?>, this)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br><button class="btn bp sm" onclick="ep('ff');fillF(null)">➕ Add</button>
        <div id="ff" class="ep">
            <form data-ajax enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_featurebox">
            <input type="hidden" name="fb_id" id="f-id">
            <div class="fg">
                <div class="f"><label>Title</label><input type="text" name="title" id="f-ttl" required></div>
                <div class="f full">
                    <label>Feature Image</label>
                    <?php imgWidget('f-img-widget', ''); ?>
                </div>
                <div class="f"><label>Sort Order</label><input type="number" name="sort_order" id="f-sort" value="1"></div>
            </div>
            <br><button type="submit" class="btn bp">💾 Save</button>
            <button type="button" class="btn bs" onclick="ep('ff',true)">Cancel</button>
            </form>
        </div>
    </div>

    <!-- ── MAIN BANNER ── -->
    <div id="mbanner" class="card">
        <h2>📢 Main Banner</h2>
        <form data-ajax>
        <input type="hidden" name="action" value="save_mainbanner">
        <div class="fg">
            <div class="f"><label>Badge Text</label><input type="text" name="badge_text" value="<?= htmlspecialchars($mainBanner['badge_text']) ?>"></div>
            <div class="f"><label>Button Text</label><input type="text" name="button_text" value="<?= htmlspecialchars($mainBanner['button_text']) ?>"></div>
            <div class="f"><label>Button URL</label><input type="text" name="button_url" value="<?= htmlspecialchars($mainBanner['button_url']) ?>"></div>
            <div class="f full"><label>Heading (HTML ok, e.g. Up to &lt;span&gt;70% off&lt;/span&gt;)</label><input type="text" name="heading" value="<?= htmlspecialchars($mainBanner['heading']) ?>"></div>
        </div>
        <br><button type="submit" class="btn bp">💾 Save</button>
        </form>
    </div>

    <!-- ── SMALL BANNERS ── -->
    <div id="sbanners" class="card">
        <h2>🪧 Small Banners</h2>
        <table>
            <tr><th>Badge</th><th>Heading</th><th>Style</th><th>Actions</th></tr>
            <?php foreach ($smBanners as $sb): ?>
            <tr id="sb-row-<?= $sb['id'] ?>">
                <td><?= htmlspecialchars($sb['badge_text']) ?></td>
                <td><?= htmlspecialchars($sb['heading']) ?></td>
                <td>Variant <?= $sb['style_variant'] ?></td>
                <td style="display:flex;gap:5px;">
                    <button class="btn bs sm" onclick="ep('sbf');fillSb(<?= htmlspecialchars(json_encode($sb)) ?>)">✏️</button>
                    <button class="btn bd2 sm" onclick="deleteSmBanner(<?= $sb['id'] ?>, this)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br><button class="btn bp sm" onclick="ep('sbf');fillSb(null)">➕ Add</button>
        <div id="sbf" class="ep">
            <form data-ajax>
            <input type="hidden" name="action" value="save_smallbanner">
            <input type="hidden" name="sb_id" id="sb-id">
            <div class="fg">
                <div class="f"><label>Badge</label><input type="text" name="badge_text" id="sb-badge"></div>
                <div class="f"><label>Heading</label><input type="text" name="heading" id="sb-h"></div>
                <div class="f full"><label>Description</label><textarea name="description" id="sb-desc"></textarea></div>
                <div class="f"><label>Button Text</label><input type="text" name="button_text" id="sb-btn"></div>
                <div class="f"><label>Button URL</label><input type="text" name="button_url" id="sb-url"></div>
                <div class="f"><label>Style</label><select name="style_variant" id="sb-style"><option value="1">1 — Teal</option><option value="2">2 — Orange</option></select></div>
                <div class="f"><label>Sort Order</label><input type="number" name="sort_order" id="sb-sort" value="1"></div>
            </div>
            <br><button type="submit" class="btn bp">💾 Save</button>
            <button type="button" class="btn bs" onclick="ep('sbf',true)">Cancel</button>
            </form>
        </div>
    </div>

    <!-- ── SEASONAL BANNERS ── -->
    <div id="seasonal" class="card">
        <h2>🌿 Seasonal Banners</h2>
        <table>
            <tr><th>Heading</th><th>Subheading</th><th>Style</th><th>Actions</th></tr>
            <?php foreach ($seasonal as $s): ?>
            <tr id="sea-row-<?= $s['id'] ?>">
                <td><?= htmlspecialchars($s['heading']) ?></td>
                <td><?= htmlspecialchars($s['subheading']) ?></td>
                <td><?= $s['style_class'] ?: 'default' ?></td>
                <td style="display:flex;gap:5px;">
                    <button class="btn bs sm" onclick="ep('sef');fillSe(<?= htmlspecialchars(json_encode($s)) ?>)">✏️</button>
                    <button class="btn bd2 sm" onclick="deleteSeasonal(<?= $s['id'] ?>, this)">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br><button class="btn bp sm" onclick="ep('sef');fillSe(null)">➕ Add</button>
        <div id="sef" class="ep">
            <form data-ajax>
            <input type="hidden" name="action" value="save_seasonal">
            <input type="hidden" name="sea_id" id="se-id">
            <div class="fg">
                <div class="f"><label>Heading</label><input type="text" name="heading" id="se-h" required></div>
                <div class="f"><label>Subheading</label><input type="text" name="subheading" id="se-sh"></div>
                <div class="f"><label>Style Class</label>
                    <select name="style_class" id="se-sc">
                        <option value="">Default</option>
                        <option value="banner-boxa">banner-boxa</option>
                        <option value="banner-boxb">banner-boxb</option>
                    </select>
                </div>
                <div class="f"><label>Sort Order</label><input type="number" name="sort_order" id="se-sort" value="1"></div>
            </div>
            <br><button type="submit" class="btn bp">💾 Save</button>
            <button type="button" class="btn bs" onclick="ep('sef',true)">Cancel</button>
            </form>
        </div>
    </div>

    <!-- ── NEWSLETTER ── -->
    <div id="newsletter" class="card">
        <h2>📧 Newsletter</h2>
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <h3 style="font-size:.9rem;font-weight:700;color:var(--dk);margin-bottom:12px;">Section Text</h3>
                <form data-ajax>
                <input type="hidden" name="action" value="save_newsletter">
                <div class="fg">
                    <div class="f full"><label>Badge Text</label><input type="text" name="badge_text" value="<?= htmlspecialchars($newsletter['badge_text']) ?>"></div>
                    <div class="f full"><label>Heading (HTML ok)</label><textarea name="heading"><?= htmlspecialchars($newsletter['heading']) ?></textarea></div>
                    <div class="f"><label>Input Placeholder</label><input type="text" name="placeholder_text" value="<?= htmlspecialchars($newsletter['placeholder_text']) ?>"></div>
                    <div class="f"><label>Button Text</label><input type="text" name="button_text" value="<?= htmlspecialchars($newsletter['button_text']) ?>"></div>
                </div>
                <br><button type="submit" class="btn bp">💾 Save</button>
                </form>
            </div>
            <div style="flex:1;min-width:260px;">
                <h3 style="font-size:.9rem;font-weight:700;color:var(--dk);margin-bottom:12px;">Subscribers — <?= $nlCount ?> active</h3>
                <div style="max-height:280px;overflow-y:auto;">
                <table>
                    <tr><th>Email</th><th>Date</th></tr>
                    <?php foreach ($nlSubs as $sub): ?>
                    <tr>
                        <td><?= htmlspecialchars($sub['email']) ?></td>
                        <td style="font-size:.75rem;color:#777;"><?= date('d M Y', strtotime($sub['subscribed_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
// ── PHP helper: renders the image upload widget ──────
function imgWidget(string $widgetId, string $currentPath): void {
    $safe      = htmlspecialchars($currentPath, ENT_QUOTES);
    $showClass = $safe ? ' show' : '';
    echo <<<HTML
<div class="img-upload-widget" id="{$widgetId}">
    <img class="current-img{$showClass}"
         src="{$safe}" alt="current"
         onerror="this.classList.remove('show')"
         id="{$widgetId}-preview">
    <div class="upload-row">
        <input type="text" name="image_path" id="{$widgetId}-path"
               value="{$safe}" placeholder="shoes images/example.jpg"
               oninput="syncPreview('{$widgetId}')">
        <span class="or-divider">OR</span>
        <label class="file-pick-btn">
            📁 Choose File
            <input type="file" name="image_file" accept="image/*"
                   onchange="handleFilePick(this, '{$widgetId}')">
        </label>
    </div>
    <span class="upload-hint">JPG · PNG · WEBP · GIF · Max 5 MB</span>
</div>
HTML;
}
?>

<script>
// ── Image widget helpers ──────────────────────────────
function syncPreview(widgetId) {
    const path    = document.getElementById(widgetId + '-path').value.trim();
    const preview = document.getElementById(widgetId + '-preview');
    if (path) { preview.src = path; preview.classList.add('show'); }
    else       { preview.classList.remove('show'); }
}
function handleFilePick(input, widgetId) {
    if (!input.files[0]) return;
    const preview = document.getElementById(widgetId + '-preview');
    preview.src   = URL.createObjectURL(input.files[0]);
    preview.classList.add('show');
    document.getElementById(widgetId + '-path').value = '';
}
function updateThumb(selector, newPath) {
    const el = document.querySelector(selector);
    if (el && newPath) el.src = newPath;
}

// ── Core AJAX helper ──────────────────────────────────
function ajaxPost(formData, onSuccess) {
    return fetch(location.pathname, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.msg, data.ok);
        if (data.ok && onSuccess) onSuccess(data);
        return data;
    })
    .catch(() => showToast('Network error — please try again.', false));
}

// ── Toast ─────────────────────────────────────────────
let toastTimer;
function showToast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = (ok ? '✅ ' : '❌ ') + msg;
    t.className = ok ? 'ok' : 'err';
    t.style.display = 'block';
    t.style.opacity = '1';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        t.style.opacity = '0';
        setTimeout(() => t.style.display = 'none', 300);
    }, 3500);
}

// ── Intercept [data-ajax] form submits ────────────────
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form.hasAttribute('data-ajax')) return;
    e.preventDefault();

    const btn  = form.querySelector('[type=submit]');
    const orig = btn.textContent;
    btn.disabled    = true;
    btn.textContent = 'Saving…';

    ajaxPost(new FormData(form), data => {
        if (data.image_path) {
            const action = form.querySelector('[name=action]')?.value;
            if (action === 'save_hero') updateThumb('#hero-img-preview', data.image_path);
            if (action === 'save_product') {
                const id = form.querySelector('[name=product_id]')?.value;
                if (id) updateThumb('#prod-img-' + id, data.image_path);
                const fi = form.querySelector('input[type=file]');
                if (fi) fi.value = '';
            }
            if (action === 'save_category') {
                const id = form.querySelector('[name=cat_id]')?.value;
                if (id) updateThumb('#cat-img-' + id, data.image_path);
            }
            if (action === 'save_featurebox') {
                const id = form.querySelector('[name=fb_id]')?.value;
                if (id) updateThumb('#fb-img-' + id, data.image_path);
            }
        }
    }).finally(() => { btn.disabled = false; btn.textContent = orig; });
});

// ── Toggle / Delete ───────────────────────────────────
function toggleProduct(id, btn) {
    const fd = new FormData();
    fd.append('action', 'toggle_product');
    fd.append('product_id', id);
    btn.disabled = true;
    ajaxPost(fd, data => {
        const active = data.state;
        const badge  = document.getElementById('prod-status-' + id);
        if (badge) { badge.textContent = active ? 'Active' : 'Hidden'; badge.className = 'badge ' + (active ? 'bg' : 'bgr'); }
        btn.textContent = active ? '🙈' : '👁️';
    }).finally(() => btn.disabled = false);
}
function deleteProduct(id, btn) {
    if (!confirm('Delete this product?')) return;
    const fd = new FormData(); fd.append('action','delete_product'); fd.append('product_id',id);
    btn.disabled = true;
    ajaxPost(fd, () => document.getElementById('prod-row-' + id)?.remove()).finally(() => btn.disabled = false);
}
function deleteCategory(id, btn) {
    if (!confirm('Delete this category?')) return;
    const fd = new FormData(); fd.append('action','delete_category'); fd.append('cat_id',id);
    btn.disabled = true;
    ajaxPost(fd, () => document.getElementById('cat-row-' + id)?.remove()).finally(() => btn.disabled = false);
}
function deleteFeatureBox(id, btn) {
    if (!confirm('Delete this feature box?')) return;
    const fd = new FormData(); fd.append('action','delete_featurebox'); fd.append('fb_id',id);
    btn.disabled = true;
    ajaxPost(fd, () => document.getElementById('fb-row-' + id)?.remove()).finally(() => btn.disabled = false);
}
function deleteSmBanner(id, btn) {
    if (!confirm('Delete this banner?')) return;
    const fd = new FormData(); fd.append('action','delete_smallbanner'); fd.append('sb_id',id);
    btn.disabled = true;
    ajaxPost(fd, () => document.getElementById('sb-row-' + id)?.remove()).finally(() => btn.disabled = false);
}
function deleteSeasonal(id, btn) {
    if (!confirm('Delete this seasonal banner?')) return;
    const fd = new FormData(); fd.append('action','delete_seasonal'); fd.append('sea_id',id);
    btn.disabled = true;
    ajaxPost(fd, () => document.getElementById('sea-row-' + id)?.remove()).finally(() => btn.disabled = false);
}

// ── Edit panel toggle ─────────────────────────────────
function ep(id, hide) {
    const el = document.getElementById(id);
    el.style.display = (hide || el.style.display === 'block') ? 'none' : 'block';
    if (el.style.display === 'block') el.scrollIntoView({behavior:'smooth', block:'nearest'});
}

// ── Fill form helpers ─────────────────────────────────
function fillP(p) {
    document.getElementById('p-id').value    = p ? p.id : '';
    document.getElementById('p-name').value  = p ? p.title : '';
    document.getElementById('p-price').value = p ? p.price : '';
    document.getElementById('p-url').value   = p ? p.product_url : '';
    document.getElementById('p-stars').value = p ? p.rating_stars : 5;
    document.getElementById('p-rc').value    = p ? p.rating_count : 0;
    document.getElementById('p-sort').value  = p ? p.sort_order : 1;
    setWidgetPath('p-img-widget', p ? p.image_path : '');
}
function fillC(c) {
    document.getElementById('c-id').value   = c ? c.id : '';
    document.getElementById('c-lbl').value  = c ? c.label : '';
    document.getElementById('c-url').value  = c ? c.link_url : '#';
    document.getElementById('c-sec').value  = c ? c.section : 'category';
    document.getElementById('c-sort').value = c ? c.sort_order : 1;
    document.getElementById('c-hot').value  = c ? c.is_highlight : 0;
    setWidgetPath('c-img-widget', c ? c.image_path : '');
}
function fillF(fb) {
    document.getElementById('f-id').value   = fb ? fb.id : '';
    document.getElementById('f-ttl').value  = fb ? fb.title : '';
    document.getElementById('f-sort').value = fb ? fb.sort_order : 1;
    setWidgetPath('f-img-widget', fb ? fb.image_path : '');
}
function fillSb(sb) {
    document.getElementById('sb-id').value    = sb ? sb.id : '';
    document.getElementById('sb-badge').value = sb ? sb.badge_text : '';
    document.getElementById('sb-h').value     = sb ? sb.heading : '';
    document.getElementById('sb-desc').value  = sb ? sb.description : '';
    document.getElementById('sb-btn').value   = sb ? sb.button_text : '';
    document.getElementById('sb-url').value   = sb ? sb.button_url : '#';
    document.getElementById('sb-style').value = sb ? sb.style_variant : 1;
    document.getElementById('sb-sort').value  = sb ? sb.sort_order : 1;
}
function fillSe(s) {
    document.getElementById('se-id').value   = s ? s.id : '';
    document.getElementById('se-h').value    = s ? s.heading : '';
    document.getElementById('se-sh').value   = s ? s.subheading : '';
    document.getElementById('se-sc').value   = s ? s.style_class : '';
    document.getElementById('se-sort').value = s ? s.sort_order : 1;
}
function setWidgetPath(widgetId, path) {
    const pathInput = document.getElementById(widgetId + '-path');
    const preview   = document.getElementById(widgetId + '-preview');
    if (!pathInput) return;
    pathInput.value = path || '';
    if (path) { preview.src = path; preview.classList.add('show'); }
    else       { preview.classList.remove('show'); preview.src = ''; }
    const fileInput = pathInput.closest('form')?.querySelector('input[type=file]');
    if (fileInput) fileInput.value = '';
}

// ── Active sidebar link on scroll ─────────────────────
const sections = document.querySelectorAll('.card[id]');
const navLinks  = document.querySelectorAll('.section-tabs a');
const observer  = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting)
            navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#'+e.target.id));
    });
}, {threshold: 0.3});
sections.forEach(s => observer.observe(s));
</script>
</body>
</html>