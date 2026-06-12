<?php
// ══════════════════════════════════════════════════════════════
//  Admin_about.php — Kisken Trends Duuka | About Page Admin
// ══════════════════════════════════════════════════════════════
session_start();
require_once(__DIR__ . '/config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg      = '';
$msg_type = 'success';

function handleUpload($fieldName, $uploadDir = 'uploads/about/') {
    if (empty($_FILES[$fieldName]['name'])) return null;
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) return null;
    $filename = uniqid('img_') . '.' . $ext;
    $dest     = $uploadDir . $filename;
    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dest)) return $dest;
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_hero') {
            $stmt = $conn->prepare("UPDATE about_hero SET title=?, subtitle=? ORDER BY id DESC LIMIT 1");
            $stmt->execute([trim($_POST['hero_title']), trim($_POST['hero_subtitle'])]);
            if ($stmt->rowCount() === 0) {
                $conn->prepare("INSERT INTO about_hero (title,subtitle) VALUES (?,?)")
                     ->execute([trim($_POST['hero_title']), trim($_POST['hero_subtitle'])]);
            }
            $msg = '✅ Hero section updated!';
        }
        elseif ($action === 'save_story') {
            $imgUrl   = trim($_POST['story_img_url']);
            $uploaded = handleUpload('story_img_file');
            if ($uploaded) $imgUrl = $uploaded;
            $stmt = $conn->prepare("UPDATE about_story SET heading=?,paragraph1=?,paragraph2=?,paragraph3=?,image_url=?,image_alt=? ORDER BY id DESC LIMIT 1");
            $stmt->execute([trim($_POST['story_heading']),trim($_POST['story_p1']),trim($_POST['story_p2']),trim($_POST['story_p3']),$imgUrl,trim($_POST['story_img_alt'])]);
            if ($stmt->rowCount() === 0) {
                $conn->prepare("INSERT INTO about_story (heading,paragraph1,paragraph2,paragraph3,image_url,image_alt) VALUES (?,?,?,?,?,?)")
                     ->execute([trim($_POST['story_heading']),trim($_POST['story_p1']),trim($_POST['story_p2']),trim($_POST['story_p3']),$imgUrl,trim($_POST['story_img_alt'])]);
            }
            $msg = '✅ Story section updated!';
        }
        elseif ($action === 'add_value') {
            $conn->prepare("INSERT INTO about_values (icon_class,title,description,sort_order) VALUES (?,?,?,?)")
                 ->execute([trim($_POST['val_icon']),trim($_POST['val_title']),trim($_POST['val_desc']),(int)$_POST['val_order']]);
            $msg = '✅ Value card added!';
        }
        elseif ($action === 'edit_value') {
            $conn->prepare("UPDATE about_values SET icon_class=?,title=?,description=?,sort_order=? WHERE id=?")
                 ->execute([trim($_POST['val_icon']),trim($_POST['val_title']),trim($_POST['val_desc']),(int)$_POST['val_order'],(int)$_POST['val_id']]);
            $msg = '✅ Value card updated!';
        }
        elseif ($action === 'delete_value') {
            $conn->prepare("UPDATE about_values SET is_active=0 WHERE id=?")->execute([(int)$_POST['item_id']]);
            $msg = '🗑️ Value card removed.';
        }
        elseif ($action === 'add_team') {
            $photo    = trim($_POST['tm_photo_url']);
            $uploaded = handleUpload('tm_photo_file');
            if ($uploaded) $photo = $uploaded;
            $conn->prepare("INSERT INTO about_team (full_name,role,photo_url,sort_order) VALUES (?,?,?,?)")
                 ->execute([trim($_POST['tm_name']),trim($_POST['tm_role']),$photo,(int)$_POST['tm_order']]);
            $msg = '✅ Team member added!';
        }
        elseif ($action === 'edit_team') {
            $photo    = trim($_POST['tm_photo_url']);
            $uploaded = handleUpload('tm_photo_file');
            if ($uploaded) $photo = $uploaded;
            $conn->prepare("UPDATE about_team SET full_name=?,role=?,photo_url=?,sort_order=? WHERE id=?")
                 ->execute([trim($_POST['tm_name']),trim($_POST['tm_role']),$photo,(int)$_POST['tm_order'],(int)$_POST['tm_id']]);
            $msg = '✅ Team member updated!';
        }
        elseif ($action === 'delete_team') {
            $conn->prepare("UPDATE about_team SET is_active=0 WHERE id=?")->execute([(int)$_POST['item_id']]);
            $msg = '🗑️ Team member removed.';
        }
        elseif ($action === 'save_stats') {
            $stmt = $conn->prepare("UPDATE about_stats SET label=?, value=? WHERE id=?");
            foreach (($_POST['stat_id'] ?? []) as $i => $id) {
                $stmt->execute([trim($_POST['stat_label'][$i]),(int)$_POST['stat_value'][$i],(int)$id]);
            }
            $msg = '✅ Stats updated!';
        }
        elseif ($action === 'add_stat') {
            $conn->prepare("INSERT INTO about_stats (label,value,sort_order) VALUES (?,?,?)")
                 ->execute([trim($_POST['new_stat_label']),(int)$_POST['new_stat_value'],(int)$_POST['new_stat_order']]);
            $msg = '✅ Stat added!';
        }
        elseif ($action === 'delete_stat') {
            $conn->prepare("UPDATE about_stats SET is_active=0 WHERE id=?")->execute([(int)$_POST['item_id']]);
            $msg = '🗑️ Stat removed.';
        }
    } catch (PDOException $e) {
        $msg      = '❌ Error: ' . $e->getMessage();
        $msg_type = 'error';
    }
}

$hero   = $conn->query("SELECT * FROM about_hero   ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$story  = $conn->query("SELECT * FROM about_story  ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$values = $conn->query("SELECT * FROM about_values WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$team   = $conn->query("SELECT * FROM about_team   WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$stats  = $conn->query("SELECT * FROM about_stats  WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn() ?? 0;

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — About Page | Kisken Trends Duuka</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="admin-container">

    <!-- ═══ SIDEBAR ════════════════════════════════════════ -->
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
                <span class="nav-icon"><i class="fas fa-pencil-alt"></i></span>
                <span class="nav-text">Blog</span>
            </a>
            <a href="Admin_about.php" class="active">
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
    <div class="admin-main">

        <div class="admin-header">
            <div>
                <h2>About Page</h2>
                <p class="header-sub">Edit your about page content</p>
            </div>
            <a href="about.php" target="_blank" class="preview-btn">
                <i class="fas fa-eye"></i> Preview Page
            </a>
        </div>

        <!-- Page sub-nav -->
        <div class="page-tabs">
            <a href="#hero"><i class="fas fa-image"></i> Hero</a>
            <a href="#story"><i class="fas fa-book-open"></i> Story</a>
            <a href="#values"><i class="fas fa-heart"></i> Values</a>
            <a href="#team"><i class="fas fa-users"></i> Team</a>
            <a href="#stats"><i class="fas fa-chart-bar"></i> Stats</a>
        </div>

        <?php if ($msg): ?>
        <div class="alert <?= $msg_type ?>"><?= $msg ?></div>
        <?php endif; ?>

        <!-- ── HERO ── -->
        <div class="card" id="hero">
            <div class="card-header">
                <h2><i class="fas fa-image"></i> Hero Section</h2>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="save_hero">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Page Title</label>
                        <input type="text" name="hero_title" value="<?= e($hero['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Subtitle / Tagline</label>
                        <textarea name="hero_subtitle"><?= e($hero['subtitle'] ?? '') ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Hero</button>
            </form>
        </div>

        <!-- ── STORY ── -->
        <div class="card" id="story">
            <div class="card-header">
                <h2><i class="fas fa-book-open"></i> About Story Section</h2>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_story">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Section Heading</label>
                        <input type="text" name="story_heading" value="<?= e($story['heading'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Paragraph 1</label>
                        <textarea name="story_p1" rows="4"><?= e($story['paragraph1'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Paragraph 2</label>
                        <textarea name="story_p2" rows="4"><?= e($story['paragraph2'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Paragraph 3</label>
                        <textarea name="story_p3" rows="4"><?= e($story['paragraph3'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image URL <small>(or upload below)</small></label>
                        <input type="text" name="story_img_url" value="<?= e($story['image_url'] ?? '') ?>" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Upload New Image <small>(overrides URL if chosen)</small></label>
                        <input type="file" name="story_img_file" accept="image/*">
                        <?php if (!empty($story['image_url'])): ?>
                            <img src="<?= e($story['image_url']) ?>" style="margin-top:8px;max-height:80px;border-radius:8px;" alt="current">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Image Alt Text</label>
                        <input type="text" name="story_img_alt" value="<?= e($story['image_alt'] ?? '') ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Story</button>
            </form>
        </div>

        <!-- ── VALUES ── -->
        <div class="card" id="values">
            <div class="card-header">
                <h2><i class="fas fa-heart"></i> Values Cards</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-value')">
                    <i class="fas fa-plus"></i> Add Value
                </button>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Icon</th><th>Title</th><th>Description</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($values as $v): ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><i class="<?= e($v['icon_class']) ?>" style="font-size:20px;color:#088178"></i></td>
                        <td><?= e($v['title']) ?></td>
                        <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($v['description']) ?></td>
                        <td><?= $v['sort_order'] ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-warning btn-sm" onclick='editValue(<?= json_encode($v) ?>)'><i class="fas fa-edit"></i></button>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Remove this value card?')">
                                    <input type="hidden" name="action" value="delete_value">
                                    <input type="hidden" name="item_id" value="<?= $v['id'] ?>">
                                    <button type="submit" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── TEAM ── -->
        <div class="card" id="team">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Team Members</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-team')">
                    <i class="fas fa-plus"></i> Add Member
                </button>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Photo</th><th>Name</th><th>Role</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($team as $m): ?>
                    <tr>
                        <td><img src="<?= e($m['photo_url']) ?>" class="team-thumb" alt="<?= e($m['full_name']) ?>"></td>
                        <td><?= e($m['full_name']) ?></td>
                        <td><?= e($m['role']) ?></td>
                        <td><?= $m['sort_order'] ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-warning btn-sm" onclick='editTeam(<?= json_encode($m) ?>)'><i class="fas fa-edit"></i></button>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Remove this team member?')">
                                    <input type="hidden" name="action" value="delete_team">
                                    <input type="hidden" name="item_id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── STATS ── -->
        <div class="card" id="stats">
            <div class="card-header">
                <h2><i class="fas fa-chart-bar"></i> Stats / Numbers</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-stat')">
                    <i class="fas fa-plus"></i> Add Stat
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="save_stats">
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Label</th><th>Value</th><th>Remove</th></tr></thead>
                        <tbody>
                        <?php foreach ($stats as $s): ?>
                        <tr>
                            <td>
                                <input type="hidden" name="stat_id[]" value="<?= $s['id'] ?>">
                                <input type="text" name="stat_label[]" value="<?= e($s['label']) ?>" class="inline-input">
                            </td>
                            <td>
                                <input type="number" name="stat_value[]" value="<?= $s['value'] ?>" class="inline-input" style="width:130px">
                            </td>
                            <td>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Remove this stat?')">
                                    <input type="hidden" name="action" value="delete_stat">
                                    <input type="hidden" name="item_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save All Stats</button>
            </form>
        </div>

    </div><!-- /.admin-main -->
</div><!-- /.admin-container -->

<!-- ── Modal: Add Value ── -->
<div class="modal-bg" id="modal-add-value">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Value Card</h3>
            <button class="modal-close" onclick="closeModal('modal-add-value')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_value">
            <div class="form-grid">
                <div class="form-group">
                    <label>Font Awesome Icon Class</label>
                    <input type="text" name="val_icon" placeholder="fas fa-star" required>
                    <small style="color:#aaa">e.g. fas fa-leaf · fas fa-heart · fas fa-users</small>
                </div>
                <div class="form-group"><label>Title</label><input type="text" name="val_title" required></div>
                <div class="form-group"><label>Description</label><textarea name="val_desc" required></textarea></div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="val_order" value="<?= count($values)+1 ?>"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Card</button>
        </form>
    </div>
</div>

<!-- ── Modal: Edit Value ── -->
<div class="modal-bg" id="modal-edit-value">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Value Card</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-value')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit_value">
            <input type="hidden" name="val_id" id="edit-val-id">
            <div class="form-grid">
                <div class="form-group">
                    <label>Font Awesome Icon Class</label>
                    <input type="text" name="val_icon" id="edit-val-icon" required>
                </div>
                <div class="form-group"><label>Title</label><input type="text" name="val_title" id="edit-val-title" required></div>
                <div class="form-group"><label>Description</label><textarea name="val_desc" id="edit-val-desc"></textarea></div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="val_order" id="edit-val-order"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </form>
    </div>
</div>

<!-- ── Modal: Add Team ── -->
<div class="modal-bg" id="modal-add-team">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add Team Member</h3>
            <button class="modal-close" onclick="closeModal('modal-add-team')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_team">
            <div class="form-grid">
                <div class="form-group"><label>Full Name</label><input type="text" name="tm_name" required></div>
                <div class="form-group"><label>Role / Title</label><input type="text" name="tm_role" required></div>
                <div class="form-group"><label>Photo URL <small>(or upload below)</small></label><input type="text" name="tm_photo_url" placeholder="officials/photo.jpg"></div>
                <div class="form-group"><label>Upload Photo</label><input type="file" name="tm_photo_file" accept="image/*"></div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="tm_order" value="<?= count($team)+1 ?>"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Member</button>
        </form>
    </div>
</div>

<!-- ── Modal: Edit Team ── -->
<div class="modal-bg" id="modal-edit-team">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Edit Team Member</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-team')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_team">
            <input type="hidden" name="tm_id" id="edit-tm-id">
            <div class="form-grid">
                <div class="form-group"><label>Full Name</label><input type="text" name="tm_name" id="edit-tm-name" required></div>
                <div class="form-group"><label>Role / Title</label><input type="text" name="tm_role" id="edit-tm-role" required></div>
                <div class="form-group"><label>Photo URL</label><input type="text" name="tm_photo_url" id="edit-tm-photo"></div>
                <div class="form-group">
                    <label>Upload New Photo <small>(overrides URL)</small></label>
                    <input type="file" name="tm_photo_file" accept="image/*">
                    <img id="edit-tm-thumb" src="" style="margin-top:8px;max-height:70px;border-radius:8px;display:none;" alt="">
                </div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="tm_order" id="edit-tm-order"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </form>
    </div>
</div>

<!-- ── Modal: Add Stat ── -->
<div class="modal-bg" id="modal-add-stat">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Stat</h3>
            <button class="modal-close" onclick="closeModal('modal-add-stat')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_stat">
            <div class="form-grid">
                <div class="form-group"><label>Label (e.g. "Awards Won")</label><input type="text" name="new_stat_label" required></div>
                <div class="form-group"><label>Value (number)</label><input type="number" name="new_stat_value" required></div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="new_stat_order" value="<?= count($stats)+1 ?>"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Stat</button>
        </form>
    </div>
</div>

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Roboto,sans-serif;background:#f4f7f6;color:#333}
.admin-container{display:flex}

/* ── Sidebar ── */
.admin-nav{width:230px;background:#0f0f0f;display:flex;flex-direction:column;position:fixed;height:100vh;z-index:1000;overflow-y:auto;overflow-x:hidden}
.nav-brand{display:flex;align-items:center;gap:10px;padding:20px 18px 18px;border-bottom:1px solid rgba(255,255,255,.06);flex-shrink:0}
.nav-brand-icon{width:34px;height:34px;background:#088178;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;flex-shrink:0}
.nav-brand-name{font-size:13px;font-weight:600;color:#fff;line-height:1.3}
.nav-brand-sub{font-size:10.5px;color:rgba(255,255,255,.28)}
.nav-label{font-size:10px;font-weight:600;color:rgba(255,255,255,.22);letter-spacing:.1em;text-transform:uppercase;padding:16px 18px 5px}
.nav-links{padding:0 8px}
.nav-links a{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;text-decoration:none;color:rgba(255,255,255,.45);font-size:13.5px;margin-bottom:1px;transition:background .15s,color .15s;position:relative}
.nav-links a:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85)}
.nav-links a.active{background:rgba(8,129,120,.2);color:#fff}
.nav-links a.active .nav-icon{color:#0bbfb4}
.nav-links a.active::before{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:#088178;border-radius:0 3px 3px 0}
.nav-icon{width:20px;text-align:center;font-size:14px;flex-shrink:0;color:inherit}
.nav-text{flex:1}
.nav-badge{margin-left:auto;background:#088178;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;line-height:1.7;flex-shrink:0}
.nav-badge.danger{background:rgba(231,76,60,.2);color:#e74c3c}
.nav-footer{margin-top:auto;border-top:1px solid rgba(255,255,255,.06);padding:12px 8px 10px;flex-shrink:0}
.nav-user{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:9px;margin-bottom:2px}
.nav-avatar{width:30px;height:30px;border-radius:50%;background:#088178;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#fff;flex-shrink:0}
.nav-user-name{font-size:12.5px;font-weight:600;color:#fff;line-height:1.3}
.nav-user-role{font-size:10.5px;color:rgba(255,255,255,.28)}
.nav-logout{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;text-decoration:none;color:rgba(231,76,60,.75);font-size:13.5px;transition:background .15s,color .15s}
.nav-logout:hover{background:rgba(231,76,60,.1);color:#e74c3c}

/* ── Main ── */
.admin-main{margin-left:230px;padding:0 40px 40px;width:calc(100% - 230px)}

.admin-header{position:sticky;top:0;z-index:100;background:rgba(244,247,246,.95);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);padding:22px 0 18px;margin-bottom:28px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
.admin-header h2{font-size:20px;font-weight:600;color:#1a1a1a}
.header-sub{font-size:13px;color:#888;margin-top:2px}
.preview-btn{background:#fff;padding:9px 16px;border-radius:50px;font-weight:600;font-size:13px;color:#444;border:1px solid #eee;box-shadow:0 2px 5px rgba(0,0,0,.04);text-decoration:none;display:flex;align-items:center;gap:6px;transition:box-shadow .2s}
.preview-btn:hover{box-shadow:0 4px 12px rgba(0,0,0,.08)}

/* ── Page sub-tabs ── */
.page-tabs{display:flex;gap:8px;margin-bottom:28px;flex-wrap:wrap;position:sticky;top:72px;z-index:90;background:rgba(244,247,246,.95);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);padding:12px 0 10px;border-bottom:1px solid #eee}
.page-tabs a{padding:9px 18px;border:1px solid #dde1e7;border-radius:8px;font-size:13px;font-weight:600;background:#fff;color:#555;text-decoration:none;transition:all .2s;display:flex;align-items:center;gap:7px}
.page-tabs a:hover{background:#0f0f0f;color:#fff;border-color:#0f0f0f}

/* ── Alert ── */
.alert{padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:14px;font-weight:500}
.alert.success{background:#e8f5f4;color:#088178;border:1px solid #c0e4e1}
.alert.error{background:#fff0f0;color:#e74c3c;border:1px solid #fca5a5}

/* ── Cards ── */
.card{background:#fff;border-radius:12px;padding:25px;margin-bottom:28px;box-shadow:0 2px 8px rgba(0,0,0,.03);border:1px solid #eee;transition:box-shadow .2s;scroll-margin-top:20px}
.card:hover{box-shadow:0 4px 16px rgba(0,0,0,.07)}
.card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f1f1f1}
.card-header h2{font-size:14px;color:#1a1a1a;display:flex;align-items:center;gap:8px;text-transform:uppercase;letter-spacing:.4px;font-weight:600}
.card-header h2 i{color:#088178}

/* ── Forms ── */
.form-grid{display:flex;flex-direction:column;gap:14px;margin-bottom:18px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-group label{font-size:13px;font-weight:600;color:#555}
.form-group input,.form-group textarea{padding:10px 14px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .2s;outline:none}
.form-group input:focus,.form-group textarea:focus{border-color:#088178}
.form-group textarea{resize:vertical;min-height:80px}
.inline-input{width:100%;padding:7px 10px;border:1.5px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s}
.inline-input:focus{border-color:#088178}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;text-decoration:none}
.btn-primary{background:#088178;color:#fff}.btn-primary:hover{background:#066b63}
.btn-warning{background:#f59e0b;color:#fff}.btn-warning:hover{background:#d97706}
.btn-red{background:#e53935;color:#fff}.btn-red:hover{background:#b71c1c}
.btn-sm{padding:6px 13px;font-size:12px}

/* ── Table ── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:14px}
th{text-align:left;background:#f9f9f9;padding:12px 15px;color:#777;font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:600}
td{padding:14px 15px;border-bottom:1px solid #f1f1f1;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafcfb}
.team-thumb{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #eee}
.action-btns{display:flex;gap:6px;flex-wrap:wrap}

/* ── Modal ── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;align-items:center;justify-content:center}
.modal-bg.open{display:flex}
.modal{background:#fff;border-radius:14px;padding:28px;width:90%;max-width:520px;max-height:90vh;overflow-y:auto}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #f1f1f1}
.modal-header h3{font-size:15px;font-weight:600;color:#1a1a1a;display:flex;align-items:center;gap:8px}
.modal-header h3 i{color:#088178}
.modal-close{background:none;border:none;font-size:22px;cursor:pointer;color:#aaa;transition:color .2s}
.modal-close:hover{color:#333}
</style>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-bg').forEach(bg => {
    bg.addEventListener('click', e => { if (e.target === bg) bg.classList.remove('open'); });
});
function editValue(v) {
    document.getElementById('edit-val-id').value    = v.id;
    document.getElementById('edit-val-icon').value  = v.icon_class;
    document.getElementById('edit-val-title').value = v.title;
    document.getElementById('edit-val-desc').value  = v.description;
    document.getElementById('edit-val-order').value = v.sort_order;
    openModal('modal-edit-value');
}
function editTeam(m) {
    document.getElementById('edit-tm-id').value    = m.id;
    document.getElementById('edit-tm-name').value  = m.full_name;
    document.getElementById('edit-tm-role').value  = m.role;
    document.getElementById('edit-tm-photo').value = m.photo_url;
    document.getElementById('edit-tm-order').value = m.sort_order;
    const thumb = document.getElementById('edit-tm-thumb');
    if (m.photo_url) { thumb.src = m.photo_url; thumb.style.display = 'block'; }
    else { thumb.style.display = 'none'; }
    openModal('modal-edit-team');
}
</script>
</body>
</html>
