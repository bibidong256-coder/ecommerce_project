<?php
// ── admin_dashboard.php ─────────────────────────────
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$active_tab  = $_GET['tab'] ?? 'messages';
$success_msg = '';
$error_msg   = '';

// ══════════════════════════════════════════════════════
//  MESSAGES ACTIONS
// ══════════════════════════════════════════════════════
if (isset($_GET['mark_read']))   { $conn->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([(int)$_GET['mark_read']]);   header('Location: admin_dashboard.php?tab=messages'); exit; }
if (isset($_GET['mark_unread'])) { $conn->prepare("UPDATE contact_messages SET is_read=0 WHERE id=?")->execute([(int)$_GET['mark_unread']]); header('Location: admin_dashboard.php?tab=messages'); exit; }
if (isset($_GET['delete_msg']))  { $conn->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_GET['delete_msg']]);            header('Location: admin_dashboard.php?tab=messages'); exit; }

// ══════════════════════════════════════════════════════
//  SUBSCRIBER ACTIONS
// ══════════════════════════════════════════════════════
if (isset($_GET['delete_sub']))  { $conn->prepare("DELETE FROM newsletter_subscribers WHERE id=?")->execute([(int)$_GET['delete_sub']]);                        header('Location: admin_dashboard.php?tab=subscribers'); exit; }
if (isset($_GET['toggle_sub']))  { $conn->prepare("UPDATE newsletter_subscribers SET is_active=NOT is_active WHERE id=?")->execute([(int)$_GET['toggle_sub']]); header('Location: admin_dashboard.php?tab=subscribers'); exit; }

// ══════════════════════════════════════════════════════
//  CATEGORY ACTIONS
// ══════════════════════════════════════════════════════

// Save (add or edit) category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $cat_id    = (int)($_POST['cat_id'] ?? 0);
    $name      = trim($_POST['cat_name']   ?? '');
    $slug      = trim($_POST['cat_slug']   ?? '');
    $image     = trim($_POST['cat_image']  ?? '');
    $parent    = $_POST['cat_parent'] !== '' ? (int)$_POST['cat_parent'] : null;
    $order     = (int)($_POST['cat_order'] ?? 0);
    $status    = $_POST['cat_status'] ?? 'active';

    // Auto-generate slug if empty
    if (!$slug && $name) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    }

    if (!$name) {
        $error_msg  = 'Category name is required.';
        $active_tab = 'categories';
    } else {
        try {
            if ($cat_id > 0) {
                $conn->prepare("UPDATE categories SET name=?,slug=?,image=?,parent_id=?,sort_order=?,status=? WHERE id=?")
                     ->execute([$name, $slug, $image, $parent, $order, $status, $cat_id]);
                $success_msg = "Category \"$name\" updated!";
            } else {
                $conn->prepare("INSERT INTO categories (name,slug,image,parent_id,sort_order,status) VALUES (?,?,?,?,?,?)")
                     ->execute([$name, $slug, $image, $parent, $order, $status]);
                $success_msg = "Category \"$name\" added!";
            }
        } catch (PDOException $e) {
            $error_msg = 'Slug already exists or database error. Try a different slug.';
        }
        $active_tab = 'categories';
    }
}

// Delete category
if (isset($_GET['delete_cat'])) {
    $id = (int)$_GET['delete_cat'];
    // Move children to no parent before deleting
    $conn->prepare("UPDATE categories SET parent_id=NULL WHERE parent_id=?")->execute([$id]);
    $conn->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    $success_msg = 'Category deleted.';
    $active_tab  = 'categories';
    header('Location: admin_dashboard.php?tab=categories&success=deleted');
    exit;
}

// Toggle category status
if (isset($_GET['toggle_cat'])) {
    $conn->prepare("UPDATE categories SET status = IF(status='active','inactive','active') WHERE id=?")->execute([(int)$_GET['toggle_cat']]);
    header('Location: admin_dashboard.php?tab=categories'); exit;
}

// ══════════════════════════════════════════════════════
//  CONTACT PAGE SETTINGS
// ══════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $fields = ['page_title','page_subtitle','address','phone1','phone2','email','hours','map_embed_url'];
    $stmt   = $conn->prepare("INSERT INTO contact_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    foreach ($fields as $f) { $stmt->execute([$f, trim($_POST[$f] ?? '')]); }
    $success_msg = 'Contact page settings saved!';
    $active_tab  = 'contact_page';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_staff'])) {
    $id    = (int)($_POST['staff_id']    ?? 0);
    $name  = trim($_POST['staff_name']   ?? '');
    $role  = trim($_POST['staff_role']   ?? '');
    $phone = trim($_POST['staff_phone']  ?? '');
    $email = trim($_POST['staff_email']  ?? '');
    $photo = trim($_POST['staff_photo']  ?? '');
    $order = (int)($_POST['staff_order'] ?? 0);
    if ($id > 0) {
        $conn->prepare("UPDATE contact_staff SET name=?,role=?,phone=?,email=?,photo=?,sort_order=? WHERE id=?")->execute([$name,$role,$phone,$email,$photo,$order,$id]);
    } else {
        $conn->prepare("INSERT INTO contact_staff (name,role,phone,email,photo,sort_order) VALUES (?,?,?,?,?,?)")->execute([$name,$role,$phone,$email,$photo,$order]);
    }
    $success_msg = 'Staff member saved!';
    $active_tab  = 'contact_page';
}

if (isset($_GET['delete_staff']))  { $conn->prepare("DELETE FROM contact_staff WHERE id=?")->execute([(int)$_GET['delete_staff']]);                           header('Location: admin_dashboard.php?tab=contact_page'); exit; }
if (isset($_GET['toggle_staff']))  { $conn->prepare("UPDATE contact_staff SET is_active=NOT is_active WHERE id=?")->execute([(int)$_GET['toggle_staff']]);    header('Location: admin_dashboard.php?tab=contact_page'); exit; }

// ══════════════════════════════════════════════════════
//  FETCH ALL DATA
// ══════════════════════════════════════════════════════
$search = trim($_GET['search'] ?? '');
if ($search) {
    $like = "%$search%";
    $s = $conn->prepare("SELECT * FROM contact_messages WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ? ORDER BY submitted_at DESC");
    $s->execute([$like,$like,$like,$like]);
    $messages = $s->fetchAll(PDO::FETCH_ASSOC);
} else {
    $messages = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$subscribers = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$unread      = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
$total_subs  = count($subscribers);
$total_msgs  = $conn->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

// Categories — fetch with parent name
$all_cats = $conn->query(
    "SELECT c.*, p.name AS parent_name
     FROM categories c
     LEFT JOIN categories p ON c.parent_id = p.id
     ORDER BY c.parent_id IS NULL DESC, c.parent_id, c.sort_order"
)->fetchAll(PDO::FETCH_ASSOC);

// Parent categories only (for the dropdown)
$parent_cats = $conn->query("SELECT id, name FROM categories WHERE parent_id IS NULL AND status='active' ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$total_cats  = count($all_cats);

// Contact page settings
$rows = $conn->query("SELECT setting_key, setting_value FROM contact_settings")->fetchAll(PDO::FETCH_ASSOC);
$cs   = [];
foreach ($rows as $row) { $cs[$row['setting_key']] = $row['setting_value']; }
$staff_list = $conn->query("SELECT * FROM contact_staff ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$admin_name = $_SESSION['user']['name'] ?? $_SESSION['user']['email'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--primary:#088178;--dark:#04534e;--accent:#e8f5f4;--bg:#f4f6f8;--white:#fff;--text:#1a1a2e;--muted:#6b7280;--border:#e5e7eb;--radius:12px}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
        .layout{display:flex;min-height:100vh}

        /* ── Sidebar ── */
        .sidebar{width:240px;background:linear-gradient(180deg,#04534e 0%,#088178 100%);display:flex;flex-direction:column;padding:30px 0;flex-shrink:0;position:fixed;top:0;left:0;height:100vh;z-index:100;overflow-y:auto}
        .sidebar .brand{text-align:center;padding:0 20px 30px;border-bottom:1px solid rgba(255,255,255,.15);margin-bottom:20px}
        .sidebar .brand h2{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;color:#fff;margin-top:8px}
        .sidebar .brand p{font-size:.75rem;color:rgba(255,255,255,.6);margin-top:3px}
        .sidebar nav a{display:flex;align-items:center;gap:12px;padding:13px 24px;color:rgba(255,255,255,.75);text-decoration:none;font-size:.9rem;font-weight:500;transition:background .2s,color .2s}
        .sidebar nav a:hover,.sidebar nav a.active{background:rgba(255,255,255,.12);color:#fff}
        .sidebar nav a .badge{margin-left:auto;background:#ef4444;color:#fff;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:20px}
        .sidebar .logout{margin-top:auto;padding:20px 24px 0;border-top:1px solid rgba(255,255,255,.15)}
        .sidebar .logout a{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.65);text-decoration:none;font-size:.88rem;padding:10px 0;transition:color .2s}
        .sidebar .logout a:hover{color:#fff}

        /* ── Main ── */
        .main{margin-left:240px;flex:1;padding:36px}
        .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px}
        .topbar h1{font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800}
        .topbar span{font-size:.88rem;color:var(--muted)}

        /* ── Alerts ── */
        .alert{padding:12px 18px;border-radius:10px;margin-bottom:24px;font-size:.9rem;font-weight:600;display:flex;align-items:center;gap:10px}
        .alert.success{background:#e8f5f4;color:#04534e;border:1.5px solid #088178}
        .alert.error{background:#fef2f2;color:#dc2626;border:1.5px solid #fca5a5}

        /* ── Stats ── */
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:20px;margin-bottom:36px}
        .stat-card{background:var(--white);border-radius:var(--radius);padding:22px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 10px rgba(0,0,0,.05);border:1.5px solid var(--border)}
        .stat-card .icon{width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
        .stat-card .icon.green{background:var(--accent);color:var(--primary)}
        .stat-card .icon.red{background:#fef2f2;color:#ef4444}
        .stat-card .icon.blue{background:#eff6ff;color:#3b82f6}
        .stat-card .icon.purple{background:#f5f3ff;color:#7c3aed}
        .stat-card h3{font-size:1.6rem;font-weight:700;line-height:1}
        .stat-card p{font-size:.8rem;color:var(--muted);margin-top:4px}

        /* ── Card ── */
        .card{background:var(--white);border-radius:var(--radius);box-shadow:0 2px 10px rgba(0,0,0,.05);border:1.5px solid var(--border);overflow:hidden;margin-bottom:28px}
        .card-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1.5px solid var(--border);flex-wrap:wrap;gap:8px}
        .card-header h2{font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:700}
        .card-header span{font-size:.82rem;color:var(--muted)}

        /* ── Table ── */
        table{width:100%;border-collapse:collapse}
        thead tr{background:#f9fafb}
        thead th{text-align:left;padding:12px 18px;font-size:.76rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);border-bottom:1.5px solid var(--border)}
        tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:#fafafa}
        tbody tr.unread{background:#f0fdf9}
        tbody td{padding:13px 18px;font-size:.87rem;vertical-align:middle}
        .badge-unread{display:inline-block;background:#088178;color:#fff;font-size:.66rem;font-weight:700;padding:2px 7px;border-radius:20px;margin-left:5px;vertical-align:middle}
        .msg-text{max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--muted);font-size:.82rem;cursor:pointer}
        .msg-text:hover{color:var(--primary);text-decoration:underline}

        /* ── Search bar ── */
        .search-bar{display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap}
        .search-bar input{flex:1;padding:10px 15px;border:2px solid var(--border);border-radius:var(--radius);font-size:.88rem;font-family:inherit;outline:none;transition:border-color .2s;min-width:200px}
        .search-bar input:focus{border-color:var(--primary)}
        .search-bar button{padding:10px 18px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius);font-size:.86rem;font-weight:600;font-family:inherit;cursor:pointer;transition:background .2s}
        .search-bar button:hover{background:var(--dark)}
        .search-bar a{padding:10px 14px;background:#f3f4f6;color:var(--muted);border-radius:var(--radius);font-size:.86rem;font-weight:600;text-decoration:none}

        /* ── Buttons ── */
        .actions{display:flex;gap:6px;flex-wrap:wrap}
        .btn-sm{display:inline-flex;align-items:center;gap:5px;padding:6px 11px;border-radius:7px;font-size:.75rem;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;border:none;transition:opacity .2s,transform .15s;white-space:nowrap}
        .btn-sm:hover{opacity:.85;transform:translateY(-1px)}
        .btn-read{background:var(--accent);color:var(--primary)}
        .btn-unread{background:#fffbeb;color:#d97706}
        .btn-delete{background:#fef2f2;color:#dc2626}
        .btn-toggle{background:#eff6ff;color:#3b82f6}
        .btn-edit{background:#f5f3ff;color:#7c3aed}
        .btn-add{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius);font-size:.86rem;font-weight:700;font-family:inherit;cursor:pointer;text-decoration:none;transition:background .2s}
        .btn-add:hover{background:var(--dark)}

        /* ── Status badge ── */
        .status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700}
        .status-badge.active{background:#e8f5f4;color:#088178}
        .status-badge.inactive{background:#f3f4f6;color:#6b7280}

        /* ── Category tree indent ── */
        .cat-child td:first-child{padding-left:36px}
        .cat-child .cat-name::before{content:'└ ';color:#aaa;font-size:.8rem}
        .parent-row{background:#fafff9 !important}
        .parent-row td{font-weight:600}

        /* ── Settings form ── */
        .settings-form{padding:26px}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px}
        .form-group{display:flex;flex-direction:column;gap:6px}
        .form-group.full{grid-column:1/-1}
        .form-group label{font-size:.82rem;font-weight:700;color:#444}
        .form-group input,.form-group textarea,.form-group select{padding:10px 13px;border:2px solid var(--border);border-radius:9px;font-size:.88rem;font-family:inherit;outline:none;transition:border-color .2s;color:var(--text)}
        .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(8,129,120,.08)}
        .form-group textarea{min-height:75px;resize:vertical}
        .btn-save{display:inline-flex;align-items:center;gap:8px;padding:12px 26px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius);font-size:.93rem;font-weight:700;font-family:inherit;cursor:pointer;transition:background .2s,transform .2s}
        .btn-save:hover{background:var(--dark);transform:translateY(-2px)}

        /* ── Staff cards ── */
        .staff-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:16px;padding:20px 24px}
        .staff-card{background:#f9fafb;border:1.5px solid var(--border);border-radius:var(--radius);padding:16px;display:flex;gap:13px;align-items:flex-start;transition:border-color .2s}
        .staff-card:hover{border-color:var(--primary)}
        .staff-card img{width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);flex-shrink:0}
        .staff-card .info h4{font-size:.93rem;font-weight:700;margin-bottom:3px}
        .staff-card .info p{font-size:.79rem;color:var(--muted);line-height:1.5}
        .staff-card .info .actions{margin-top:9px}

        /* ── Modal ── */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;padding:20px}
        .modal-overlay.active{display:flex}
        .modal{background:#fff;border-radius:var(--radius);padding:30px;max-width:540px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;max-height:90vh;overflow-y:auto}
        .modal h3{font-family:'Syne',sans-serif;font-size:1.05rem;margin-bottom:14px}
        .modal .meta{font-size:.81rem;color:var(--muted);margin-bottom:16px}
        .modal .body{font-size:.9rem;line-height:1.7;color:var(--text);white-space:pre-wrap;background:#f9fafb;padding:14px;border-radius:8px;border:1px solid var(--border)}
        .modal-close{position:absolute;top:14px;right:16px;background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--muted)}
        .modal-close:hover{color:#ef4444}

        .tab-hidden{display:none}
        .empty{text-align:center;padding:50px 20px;color:var(--muted)}
        .empty i{font-size:2.5rem;margin-bottom:12px;color:#d1d5db;display:block}

        @media(max-width:768px){.sidebar{width:200px}.main{margin-left:200px;padding:20px}.form-grid{grid-template-columns:1fr}}
        @media(max-width:600px){.layout{flex-direction:column}.sidebar{width:100%;height:auto;position:relative}.main{margin-left:0;padding:16px}.sidebar nav{display:flex;flex-wrap:wrap}}
    </style>
</head>
<body>
<div class="layout">

<!-- ── Sidebar ── -->
<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-store" style="color:#fff;font-size:1.8rem;"></i>
        <h2>KSD Admin</h2>
        <p>Kisken Trends Duuka</p>
    </div>
    <nav>
        <a href="?tab=messages"     class="<?= $active_tab==='messages'     ?'active':'' ?>">
            <i class="fas fa-envelope"></i> Messages
            <?php if($unread>0): ?><span class="badge"><?=$unread?></span><?php endif; ?>
        </a>
        <a href="?tab=subscribers"  class="<?= $active_tab==='subscribers'  ?'active':'' ?>">
            <i class="fas fa-bell"></i> Subscribers
        </a>
        <a href="?tab=categories"   class="<?= $active_tab==='categories'   ?'active':'' ?>">
            <i class="fas fa-tags"></i> Categories
            <span class="badge" style="background:#088178;"><?= $total_cats ?></span>
        </a>
        <a href="?tab=contact_page" class="<?= $active_tab==='contact_page' ?'active':'' ?>">
            <i class="fas fa-edit"></i> Contact Page
        </a>
    </nav>
    <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>

<!-- ── Main ── -->
<main class="main">
    <div class="topbar">
        <h1>
            <?php
            echo match($active_tab) {
                'messages'     => 'Contact Messages',
                'subscribers'  => 'Newsletter Subscribers',
                'categories'   => 'Categories Manager',
                'contact_page' => 'Edit Contact Page',
                default        => 'Dashboard'
            };
            ?>
        </h1>
        <span>Welcome, <?= htmlspecialchars($admin_name) ?></span>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success']==='deleted'): ?>
        <div class="alert success"><i class="fas fa-check-circle"></i> Category deleted successfully.</div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card"><div class="icon green"><i class="fas fa-envelope"></i></div><div><h3><?=$total_msgs?></h3><p>Total Messages</p></div></div>
        <div class="stat-card"><div class="icon red"><i class="fas fa-envelope-open"></i></div><div><h3><?=$unread?></h3><p>Unread</p></div></div>
        <div class="stat-card"><div class="icon blue"><i class="fas fa-users"></i></div><div><h3><?=$total_subs?></h3><p>Subscribers</p></div></div>
        <div class="stat-card"><div class="icon purple"><i class="fas fa-tags"></i></div><div><h3><?=$total_cats?></h3><p>Categories</p></div></div>
    </div>

    <!-- ══ MESSAGES TAB ══════════════════════════════════ -->
    <div class="<?= $active_tab!=='messages'?'tab-hidden':'' ?>">
        <form class="search-bar" method="GET">
            <input type="hidden" name="tab" value="messages">
            <input type="text" name="search" placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
            <?php if($search): ?><a href="?tab=messages">✕ Clear</a><?php endif; ?>
        </form>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-envelope" style="color:var(--primary);margin-right:8px;"></i>Contact Messages</h2>
                <span><?= count($messages) ?> result<?= count($messages)!==1?'s':'' ?><?= $search?" for \"$search\"":'' ?></span>
            </div>
            <?php if(empty($messages)): ?>
                <div class="empty"><i class="fas fa-inbox"></i><p><?= $search?'No messages match.':'No messages yet.' ?></p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($messages as $msg): ?>
                    <tr class="<?= !$msg['is_read']?'unread':'' ?>">
                        <td><?=$msg['id']?></td>
                        <td><strong><?= htmlspecialchars($msg['name']) ?></strong><?php if(!$msg['is_read']): ?><span class="badge-unread">NEW</span><?php endif; ?></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td><?= htmlspecialchars($msg['subject']?:'—') ?></td>
                        <td><div class="msg-text" onclick="openMsgModal('<?= htmlspecialchars(addslashes($msg['name'])) ?>','<?= htmlspecialchars(addslashes($msg['email'])) ?>','<?= htmlspecialchars(addslashes($msg['subject']?:'(No subject)')) ?>','<?= htmlspecialchars(addslashes($msg['message'])) ?>','<?= date('d M Y, H:i',strtotime($msg['submitted_at'])) ?>')" title="Click to read"><?= htmlspecialchars($msg['message']) ?></div></td>
                        <td style="white-space:nowrap;"><?= date('d M Y, H:i',strtotime($msg['submitted_at'])) ?></td>
                        <td>
                            <div class="actions">
                                <?php if(!$msg['is_read']): ?>
                                    <a href="?mark_read=<?=$msg['id']?>&tab=messages" class="btn-sm btn-read"><i class="fas fa-check"></i> Read</a>
                                <?php else: ?>
                                    <a href="?mark_unread=<?=$msg['id']?>&tab=messages" class="btn-sm btn-unread"><i class="fas fa-undo"></i> Unread</a>
                                <?php endif; ?>
                                <a href="?delete_msg=<?=$msg['id']?>&tab=messages" class="btn-sm btn-delete" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ══ SUBSCRIBERS TAB ═══════════════════════════════ -->
    <div class="<?= $active_tab!=='subscribers'?'tab-hidden':'' ?>">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-bell" style="color:var(--primary);margin-right:8px;"></i>Newsletter Subscribers</h2>
                <span><?=$total_subs?> total</span>
            </div>
            <?php if(empty($subscribers)): ?>
                <div class="empty"><i class="fas fa-bell-slash"></i><p>No subscribers yet.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>#</th><th>Email</th><th>Status</th><th>Subscribed On</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($subscribers as $sub): ?>
                    <tr>
                        <td><?=$sub['id']?></td>
                        <td><?= htmlspecialchars($sub['email']) ?></td>
                        <td><span class="status-badge <?=$sub['is_active']?'active':'inactive'?>"><?=$sub['is_active']?'Active':'Inactive'?></span></td>
                        <td style="white-space:nowrap;"><?= date('d M Y, H:i',strtotime($sub['subscribed_at'])) ?></td>
                        <td>
                            <div class="actions">
                                <a href="?toggle_sub=<?=$sub['id']?>&tab=subscribers" class="btn-sm btn-toggle"><i class="fas fa-toggle-<?=$sub['is_active']?'on':'off'?>"></i> <?=$sub['is_active']?'Deactivate':'Activate'?></a>
                                <a href="?delete_sub=<?=$sub['id']?>&tab=subscribers" class="btn-sm btn-delete" onclick="return confirm('Remove subscriber?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ══ CATEGORIES TAB ════════════════════════════════ -->
    <div class="<?= $active_tab!=='categories'?'tab-hidden':'' ?>">

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-tags" style="color:var(--primary);margin-right:8px;"></i>All Categories</h2>
                <a href="#" onclick="openCatModal(0)" class="btn-add"><i class="fas fa-plus"></i> Add Category</a>
            </div>
            <?php if(empty($all_cats)): ?>
                <div class="empty"><i class="fas fa-tags"></i><p>No categories yet. Click "Add Category" to start.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Parent</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($all_cats as $cat):
                        $is_child = !is_null($cat['parent_id']);
                    ?>
                    <tr class="<?= $is_child ? 'cat-child' : 'parent-row' ?>">
                        <td><?=$cat['id']?></td>
                        <td>
                            <span class="cat-name">
                                <?php if(!$is_child): ?>
                                    <i class="fas fa-folder" style="color:var(--primary);margin-right:6px;font-size:.85rem;"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($cat['name']) ?>
                            </span>
                        </td>
                        <td><code style="background:#f3f4f6;padding:2px 7px;border-radius:5px;font-size:.8rem;"><?= htmlspecialchars($cat['slug']) ?></code></td>
                        <td><?= $cat['parent_name'] ? '<span style="color:var(--primary);font-size:.83rem;">'.htmlspecialchars($cat['parent_name']).'</span>' : '<span style="color:#aaa;font-size:.82rem;">—</span>' ?></td>
                        <td><?=$cat['sort_order']?></td>
                        <td><span class="status-badge <?=$cat['status']?>"><?= ucfirst($cat['status']) ?></span></td>
                        <td>
                            <div class="actions">
                                <a href="#" onclick="openCatModal(<?=$cat['id']?>, <?= htmlspecialchars(json_encode($cat)) ?>)" class="btn-sm btn-edit"><i class="fas fa-pen"></i> Edit</a>
                                <a href="?toggle_cat=<?=$cat['id']?>&tab=categories" class="btn-sm btn-toggle">
                                    <i class="fas fa-eye<?=$cat['status']==='active'?'':'-slash'?>"></i> <?=$cat['status']==='active'?'Hide':'Show'?>
                                </a>
                                <a href="?delete_cat=<?=$cat['id']?>&tab=categories" class="btn-sm btn-delete" onclick="return confirm('Delete \'<?= addslashes($cat['name']) ?>\'? Its subcategories will become top-level.')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Category structure info -->
        <div style="background:#fff;border-radius:var(--radius);padding:20px 24px;border:1.5px solid var(--border);font-size:.85rem;color:var(--muted);">
            <strong style="color:var(--text);">📁 Category Structure:</strong>
            &nbsp; <i class="fas fa-folder" style="color:var(--primary);"></i> <strong>Parent</strong> = top-level (Men, Women, Kids)
            &nbsp;|&nbsp; └ <strong>Child</strong> = subcategory (Sneakers, Heels, Flats...)
            &nbsp;|&nbsp; Products link to the most specific category (child).
        </div>
    </div>

    <!-- ══ CONTACT PAGE TAB ══════════════════════════════ -->
    <div class="<?= $active_tab!=='contact_page'?'tab-hidden':'' ?>">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-sliders-h" style="color:var(--primary);margin-right:8px;"></i>Page Content & Contact Info</h2>
                <span>Changes appear live on contact.php</span>
            </div>
            <form method="POST" class="settings-form">
                <input type="hidden" name="save_settings" value="1">
                <div class="form-grid">
                    <div class="form-group"><label>Page Title</label><input type="text" name="page_title" value="<?= htmlspecialchars($cs['page_title'] ?? '') ?>" placeholder="#Let's_Talk"></div>
                    <div class="form-group"><label>Page Subtitle</label><input type="text" name="page_subtitle" value="<?= htmlspecialchars($cs['page_subtitle'] ?? '') ?>"></div>
                    <div class="form-group full"><label>Address</label><input type="text" name="address" value="<?= htmlspecialchars($cs['address'] ?? '') ?>"></div>
                    <div class="form-group"><label>Phone 1</label><input type="text" name="phone1" value="<?= htmlspecialchars($cs['phone1'] ?? '') ?>"></div>
                    <div class="form-group"><label>Phone 2 (optional)</label><input type="text" name="phone2" value="<?= htmlspecialchars($cs['phone2'] ?? '') ?>"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($cs['email'] ?? '') ?>"></div>
                    <div class="form-group"><label>Business Hours</label><input type="text" name="hours" value="<?= htmlspecialchars($cs['hours'] ?? '') ?>"></div>
                    <div class="form-group full"><label>Google Maps Embed URL</label><textarea name="map_embed_url"><?= htmlspecialchars($cs['map_embed_url'] ?? '') ?></textarea></div>
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Settings</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-users" style="color:var(--primary);margin-right:8px;"></i>Staff / People Cards</h2>
                <a href="#" onclick="openStaffModal(0)" class="btn-add"><i class="fas fa-plus"></i> Add Staff</a>
            </div>
            <div class="staff-grid">
                <?php if(empty($staff_list)): ?>
                    <p style="color:#aaa;padding:20px;">No staff yet. Click "Add Staff".</p>
                <?php else: ?>
                <?php foreach($staff_list as $m): ?>
                <div class="staff-card">
                    <img src="<?= htmlspecialchars($m['photo']) ?>" alt="<?= htmlspecialchars($m['name']) ?>"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($m['name']) ?>&background=088178&color=fff'">
                    <div class="info">
                        <h4><?= htmlspecialchars($m['name']) ?></h4>
                        <p><?= htmlspecialchars($m['role']) ?><br><?= htmlspecialchars($m['phone']) ?></p>
                        <p style="margin-top:4px;"><span class="status-badge <?=$m['is_active']?'active':'inactive'?>"><?=$m['is_active']?'Visible':'Hidden'?></span></p>
                        <div class="actions" style="margin-top:9px;">
                            <a href="#" onclick="openStaffModal(<?=$m['id']?>,<?= htmlspecialchars(json_encode($m)) ?>)" class="btn-sm btn-edit"><i class="fas fa-pen"></i> Edit</a>
                            <a href="?toggle_staff=<?=$m['id']?>&tab=contact_page" class="btn-sm btn-toggle"><i class="fas fa-eye<?=$m['is_active']?'':'-slash'?>"></i></a>
                            <a href="?delete_staff=<?=$m['id']?>&tab=contact_page" class="btn-sm btn-delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>
</div>

<!-- ── Message Modal ── -->
<div class="modal-overlay" id="msgModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('msgModal')"><i class="fas fa-times"></i></button>
        <h3 id="modalName"></h3>
        <div class="meta" id="modalMeta"></div>
        <div class="body" id="modalBody"></div>
    </div>
</div>

<!-- ── Category Modal ── -->
<div class="modal-overlay" id="catModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('catModal')"><i class="fas fa-times"></i></button>
        <h3 id="catModalTitle">Add Category</h3>
        <form method="POST" style="margin-top:14px;">
            <input type="hidden" name="save_category" value="1">
            <input type="hidden" name="cat_id" id="cat_id" value="0">
            <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label>Category Name <span style="color:red;">*</span></label>
                    <input type="text" name="cat_name" id="cat_name" required placeholder="e.g. Sneakers">
                </div>
                <div class="form-group">
                    <label>Slug (auto-generated if blank)</label>
                    <input type="text" name="cat_slug" id="cat_slug" placeholder="e.g. sneakers">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Parent Category (leave empty for top-level)</label>
                    <select name="cat_parent" id="cat_parent">
                        <option value="">— No parent (top-level) —</option>
                        <?php foreach($parent_cats as $pc): ?>
                        <option value="<?=$pc['id']?>"><?= htmlspecialchars($pc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Image Path (relative to site root)</label>
                    <input type="text" name="cat_image" id="cat_image" placeholder="shoes images/categories/sneakers.jpg">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="cat_order" id="cat_order" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="cat_status" id="cat_status">
                        <option value="active">Active (visible)</option>
                        <option value="inactive">Inactive (hidden)</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-save" style="margin-top:14px;"><i class="fas fa-save"></i> Save Category</button>
        </form>
    </div>
</div>

<!-- ── Staff Modal ── -->
<div class="modal-overlay" id="staffModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('staffModal')"><i class="fas fa-times"></i></button>
        <h3 id="staffModalTitle">Add Staff Member</h3>
        <form method="POST" style="margin-top:14px;">
            <input type="hidden" name="save_staff" value="1">
            <input type="hidden" name="staff_id" id="staff_id" value="0">
            <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group"><label>Full Name</label><input type="text" name="staff_name" id="staff_name" required></div>
                <div class="form-group"><label>Role / Title</label><input type="text" name="staff_role" id="staff_role"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="staff_phone" id="staff_phone"></div>
                <div class="form-group"><label>Email</label><input type="email" name="staff_email" id="staff_email"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Photo Path</label><input type="text" name="staff_photo" id="staff_photo" placeholder="officials/photo.jpg"></div>
                <div class="form-group"><label>Display Order</label><input type="number" name="staff_order" id="staff_order" value="0"></div>
            </div>
            <button type="submit" class="btn-save" style="margin-top:14px;"><i class="fas fa-save"></i> Save</button>
        </form>
    </div>
</div>

<script>
// Message modal
function openMsgModal(name,email,subject,message,date){
    document.getElementById('modalName').textContent=name;
    document.getElementById('modalMeta').textContent=email+' · '+subject+' · '+date;
    document.getElementById('modalBody').textContent=message;
    document.getElementById('msgModal').classList.add('active');
}

// Category modal
function openCatModal(id, data){
    document.getElementById('catModalTitle').textContent = id>0 ? 'Edit Category' : 'Add Category';
    document.getElementById('cat_id').value     = id||0;
    document.getElementById('cat_name').value   = data?.name       || '';
    document.getElementById('cat_slug').value   = data?.slug       || '';
    document.getElementById('cat_image').value  = data?.image      || '';
    document.getElementById('cat_order').value  = data?.sort_order || 0;
    document.getElementById('cat_status').value = data?.status     || 'active';
    // Set parent dropdown
    const sel = document.getElementById('cat_parent');
    sel.value = data?.parent_id || '';
    document.getElementById('catModal').classList.add('active');
    return false;
}

// Staff modal
function openStaffModal(id,data){
    document.getElementById('staffModalTitle').textContent = id>0?'Edit Staff':'Add Staff Member';
    document.getElementById('staff_id').value    = id||0;
    document.getElementById('staff_name').value  = data?.name       || '';
    document.getElementById('staff_role').value  = data?.role       || '';
    document.getElementById('staff_phone').value = data?.phone      || '';
    document.getElementById('staff_email').value = data?.email      || '';
    document.getElementById('staff_photo').value = data?.photo      || '';
    document.getElementById('staff_order').value = data?.sort_order || 0;
    document.getElementById('staffModal').classList.add('active');
    return false;
}

function closeModal(id){ document.getElementById(id).classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(o=>{
    o.addEventListener('click',function(e){ if(e.target===this) this.classList.remove('active'); });
});
document.addEventListener('keydown',e=>{ if(e.key==='Escape') document.querySelectorAll('.modal-overlay').forEach(m=>m.classList.remove('active')); });

// Auto-generate slug from name
document.getElementById('cat_name').addEventListener('input', function(){
    const slugField = document.getElementById('cat_slug');
    if(!slugField.dataset.manual){
        slugField.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
    }
});
document.getElementById('cat_slug').addEventListener('input', function(){
    this.dataset.manual = this.value ? 'true' : '';
});
</script>

</body>
</html>