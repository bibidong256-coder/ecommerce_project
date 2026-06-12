<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once './config/db.php';
require_once './blog_functions.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    die("<div style='font-family:Segoe UI,sans-serif;padding:60px;text-align:center;color:#e74c3c;font-size:18px'>Access denied. Please <a href='../login.php'>login as admin</a>.</div>");
}

$currentUser = getCurrentUser($conn);
$tab = $_GET['tab'] ?? 'posts';
$msg = '';

/* ─── Image upload helper ─────────────────────────────────────── */
function handleImageUpload(string $field): string
{
    if (empty($_FILES[$field]['tmp_name'])) return '';

    $file    = $_FILES[$field];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Validate MIME via finfo (more reliable than extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        return '__ERROR__Invalid file type. Only JPG, PNG, GIF and WebP are allowed.';
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return '__ERROR__Image must be under 5 MB.';
    }

    $ext     = explode('/', $mime)[1];        // e.g. 'jpeg'
    $ext     = ($ext === 'jpeg') ? 'jpg' : $ext;
    $name    = uniqid('blog_', true) . '.' . $ext;
    $dir     = __DIR__ . '/uploads/blog/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $dest    = $dir . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return '__ERROR__Failed to save the uploaded image. Check folder permissions.';
    }

    // Return a root-relative URL (adjust if your public root differs)
    return '/uploads/blog/' . $name;
}

/* ─── Resolve final image URL for a form submission ──────────────
   Uploaded file takes priority; falls back to the typed URL field. */
function resolveImageUrl(string $fileField, string $urlField): array
{
    $result = handleImageUpload($fileField);
    if ($result === '') {
        // No file uploaded – use the URL field
        return ['url' => trim($_POST[$urlField] ?? ''), 'error' => ''];
    }
    if (str_starts_with($result, '__ERROR__')) {
        return ['url' => trim($_POST[$urlField] ?? ''), 'error' => substr($result, 9)];
    }
    return ['url' => $result, 'error' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['save_post'])) {
    ['url' => $imgUrl, 'error' => $uploadErr] = resolveImageUrl('image_file', 'image_url');
    if ($uploadErr) {
        $msg = "⚠️ $uploadErr";
        $tab = 'new_post';
    } else {
        $result = savePost($conn, [
            'title'       => $_POST['title']       ?? '',
            'excerpt'     => $_POST['excerpt']      ?? '',
            'body'        => $_POST['body']         ?? '',
            'image_url'   => $imgUrl,
            'category_id' => $_POST['category_id']  ?? 1,
            'status'      => $_POST['status']        ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'author_id'   => currentUserId(),
        ]);
        // ✅ Now shows the real DB error if something went wrong
        $msg = $result['ok']
            ? '✅ Post saved successfully!'
            : '❌ Error saving post: ' . ($result['error'] ?? 'Unknown error');
        $tab = $result['ok'] ? 'posts' : 'new_post';
    }
}

    if (isset($_POST['update_post'])) {
        ['url' => $imgUrl, 'error' => $uploadErr] = resolveImageUrl('image_file', 'image_url');
        if ($uploadErr) {
            $msg = "⚠️ $uploadErr";
            $tab = 'posts';
        } else {
            updatePost($conn, (int)$_POST['post_id'], [
                'title'       => $_POST['title']       ?? '',
                'excerpt'     => $_POST['excerpt']     ?? '',
                'body'        => $_POST['body']        ?? '',
                'image_url'   => $imgUrl,
                'category_id' => $_POST['category_id'] ?? 1,
                'status'      => $_POST['status']      ?? 'draft',
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            ]);
            $msg = '✅ Post updated!';
            $tab = 'posts';
        }
    }

    if (isset($_POST['comment_action'])) {
        $cid    = (int)$_POST['comment_id'];
        $action = $_POST['comment_action'];
        if ($action === 'delete') {
            deleteComment($conn, $cid);
            $msg = '🗑️ Comment deleted.';
        } else {
            updateCommentStatus($conn, $cid, $action);
            $msg = "✅ Comment marked as $action.";
        }
        $tab = 'comments';
    }

    if (isset($_POST['delete_post'])) {
        deletePost($conn, (int)$_POST['post_id']);
        $msg = '🗑️ Post deleted.';
        $tab = 'posts';
    }
}

$allPosts    = [];
$pendingCmts = [];
$subscribers = [];
$categories  = getCategoriesWithCount($conn);
$editPost    = null;

$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn() ?? 0;

if ($tab === 'posts') {
    $stmt = $conn->query("
        SELECT p.*, u.name AS author, c.name AS category
        FROM blog_posts p
        JOIN users u ON u.id = p.author_id
        JOIN blog_categories c ON c.id = p.category_id
        ORDER BY p.created_at DESC
    ");
    $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['edit'])) {
        $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_GET['edit']]);
        $editPost = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if ($tab === 'comments') {
    $pendingCmts = getPendingComments($conn);
    $stmt = $conn->query("
        SELECT c.*, p.title AS post_title, p.slug AS post_slug, u.name AS registered_name
        FROM blog_comments c
        JOIN blog_posts p ON p.id = c.post_id
        LEFT JOIN users u ON u.id = c.user_id
        ORDER BY c.created_at DESC LIMIT 50
    ");
    $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($tab === 'subscribers') {
    $subscribers = getActiveSubscribers($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin | Kisken Trends Duuka</title>
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
            <a href="blog_admin.php" class="active">
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
    <div class="admin-main">

        <div class="admin-header">
            <div>
                <h2>Blog Management</h2>
                <p class="header-sub">Create and manage your blog posts</p>
            </div>
            <span class="date-badge">
                <i class="fas fa-calendar" style="margin-right:6px;color:#088178;"></i>
                <?= date('F j, Y') ?>
            </span>
        </div>

        <!-- BLOG SUB-TABS -->
        <div class="blog-tabs">
            <a href="?tab=posts"       class="tab-btn <?= $tab==='posts'       ? 'active' : '' ?>"><i class="fas fa-newspaper"></i> All Posts</a>
            <a href="?tab=new_post"    class="tab-btn <?= $tab==='new_post'    ? 'active' : '' ?>"><i class="fas fa-plus"></i> New Post</a>
            <a href="?tab=comments"    class="tab-btn <?= $tab==='comments'    ? 'active' : '' ?>"><i class="fas fa-comments"></i> Comments</a>
            <a href="?tab=subscribers" class="tab-btn <?= $tab==='subscribers' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Subscribers</a>
        </div>

        <?php if ($msg): ?>
            <div class="msg"><?= e($msg) ?></div>
        <?php endif; ?>

        <?php /* ═══ POSTS LIST ═══ */ if ($tab === 'posts' && !$editPost): ?>
        <div class="card">
            <h2><i class="fas fa-newspaper"></i> All Posts (<?= count($allPosts) ?>)</h2>
            <?php if ($allPosts): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th><th>Category</th><th>Author</th>
                        <th>Status</th><th>Views</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($allPosts as $p): ?>
                <tr>
                    <td>
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= e($p['image_url']) ?>" alt=""
                                 style="width:40px;height:40px;object-fit:cover;border-radius:6px;vertical-align:middle;margin-right:8px;">
                        <?php endif; ?>
                        <strong><?= e($p['title']) ?></strong>
                        <?php if ($p['is_featured']): ?>
                            <span class="badge badge-feat">Featured</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($p['category']) ?></td>
                    <td><?= e($p['author']) ?></td>
                    <td><span class="badge <?= $p['status']==='published' ? 'badge-pub' : 'badge-draft' ?>"><?= e($p['status']) ?></span></td>
                    <td><?= number_format($p['views']) ?></td>
                    <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="?tab=posts&edit=<?= $p['id'] ?>" class="btn btn-gray btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <a href="post.php?slug=<?= e($p['slug']) ?>" target="_blank" class="btn btn-gray btn-sm"><i class="fas fa-eye"></i></a>
                            <form method="POST" onsubmit="return confirm('Delete this post?');" style="display:inline">
                                <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                                <button type="submit" name="delete_post" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="empty"><i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:12px"></i>No posts yet. <a href="?tab=new_post">Create your first post!</a></p>
            <?php endif; ?>
        </div>

        <?php /* ═══ EDIT POST ═══ */ elseif ($tab === 'posts' && $editPost): ?>
        <div class="card">
            <h2><i class="fas fa-edit"></i> Edit Post</h2>
            <!-- enctype required for file uploads -->
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?= $editPost['id'] ?>">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" value="<?= e($editPost['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Excerpt *</label>
                    <textarea name="excerpt" rows="3" required><?= e($editPost['excerpt']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Body *</label>
                    <textarea name="body" required><?= e($editPost['body']) ?></textarea>
                </div>

                <!-- ── Image field (upload OR URL) ── -->
                <div class="form-group">
                    <label>Post Image</label>

                    <!-- Tab switcher -->
                    <div class="img-source-tabs">
                        <button type="button" class="img-tab active" onclick="switchImgTab('edit','upload')">
                            <i class="fas fa-upload"></i> Upload from PC
                        </button>
                        <button type="button" class="img-tab" onclick="switchImgTab('edit','url')">
                            <i class="fas fa-link"></i> Paste URL
                        </button>
                    </div>

                    <!-- Upload panel -->
                    <div id="edit-tab-upload" class="img-tab-panel">
                        <div class="upload-drop-zone" id="edit_drop_zone"
                             onclick="document.getElementById('edit_image_file').click()"
                             ondragover="handleDragOver(event)" ondrop="handleDrop(event,'edit')">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to browse or drag &amp; drop an image here</p>
                            <small>JPG, PNG, GIF or WebP — max 5 MB</small>
                        </div>
                        <input type="file" name="image_file" id="edit_image_file" accept="image/*"
                               style="display:none" onchange="previewUpload(this,'edit_upload_preview','edit_upload_preview_err')">
                        <div class="img-preview-wrap" id="edit_upload_preview_wrap">
                            <img id="edit_upload_preview" src="" alt="Upload preview"
                                 onerror="document.getElementById('edit_upload_preview_err').style.display='block';this.style.display='none';">
                            <div class="preview-err" id="edit_upload_preview_err">⚠️ Could not preview this image.</div>
                            <button type="button" class="clear-img-btn"
                                    onclick="clearUpload('edit_image_file','edit_upload_preview','edit_upload_preview_err','edit_upload_preview_wrap','edit_drop_zone')">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                        <?php if (!empty($editPost['image_url'])): ?>
                        <p class="current-img-note">
                            <i class="fas fa-image"></i> Current image:
                            <a href="<?= e($editPost['image_url']) ?>" target="_blank">view</a>
                            — upload a new file to replace it.
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- URL panel (hidden by default) -->
                    <div id="edit-tab-url" class="img-tab-panel" style="display:none">
                        <input type="text" name="image_url" id="edit_image_url"
                               value="<?= e($editPost['image_url'] ?? '') ?>"
                               placeholder="https://example.com/image.jpg"
                               oninput="previewImage(this.value,'edit_url_preview','edit_url_preview_err')">
                        <div class="img-preview-wrap" id="edit_url_preview_wrap">
                            <img id="edit_url_preview" src="" alt="URL preview"
                                 onerror="document.getElementById('edit_url_preview_err').style.display='block';this.style.display='none';">
                            <div class="preview-err" id="edit_url_preview_err">⚠️ Could not load this image. Check the URL.</div>
                        </div>
                    </div>
                </div>
                <!-- ── End image field ── -->

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($editPost['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="draft"     <?= $editPost['status']==='draft'     ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $editPost['status']==='published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="is_featured" name="is_featured" <?= $editPost['is_featured'] ? 'checked' : '' ?>>
                    <label for="is_featured">Set as Featured Post</label>
                </div>
                <div style="display:flex;gap:12px">
                    <button type="submit" name="update_post" class="btn btn-green"><i class="fas fa-save"></i> Update Post</button>
                    <a href="?tab=posts" class="btn btn-gray">Cancel</a>
                </div>
            </form>
        </div>

        <?php /* ═══ NEW POST ═══ */ elseif ($tab === 'new_post'): ?>
        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> Create New Post</h2>
            <!-- enctype required for file uploads -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required placeholder="Enter a catchy title...">
                </div>
                <div class="form-group">
                    <label>Excerpt * <small>(short summary shown on blog listing)</small></label>
                    <textarea name="excerpt" rows="3" required placeholder="A brief summary of the article..."></textarea>
                </div>
                <div class="form-group">
                    <label>Body *</label>
                    <textarea name="body" required placeholder="Write your full article content here..."></textarea>
                </div>

                <!-- ── Image field (upload OR URL) ── -->
                <div class="form-group">
                    <label>Post Image <small>(optional)</small></label>

                    <!-- Tab switcher -->
                    <div class="img-source-tabs">
                        <button type="button" class="img-tab active" onclick="switchImgTab('new','upload')">
                            <i class="fas fa-upload"></i> Upload from PC
                        </button>
                        <button type="button" class="img-tab" onclick="switchImgTab('new','url')">
                            <i class="fas fa-link"></i> Paste URL
                        </button>
                    </div>

                    <!-- Upload panel -->
                    <div id="new-tab-upload" class="img-tab-panel">
                        <div class="upload-drop-zone" id="new_drop_zone"
                             onclick="document.getElementById('new_image_file').click()"
                             ondragover="handleDragOver(event)" ondrop="handleDrop(event,'new')">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to browse or drag &amp; drop an image here</p>
                            <small>JPG, PNG, GIF or WebP — max 5 MB</small>
                        </div>
                        <input type="file" name="image_file" id="new_image_file" accept="image/*"
                               style="display:none" onchange="previewUpload(this,'new_upload_preview','new_upload_preview_err')">
                        <div class="img-preview-wrap" id="new_upload_preview_wrap">
                            <img id="new_upload_preview" src="" alt="Upload preview"
                                 onerror="document.getElementById('new_upload_preview_err').style.display='block';this.style.display='none';">
                            <div class="preview-err" id="new_upload_preview_err">⚠️ Could not preview this image.</div>
                            <button type="button" class="clear-img-btn"
                                    onclick="clearUpload('new_image_file','new_upload_preview','new_upload_preview_err','new_upload_preview_wrap','new_drop_zone')">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>

                    <!-- URL panel (hidden by default) -->
                    <div id="new-tab-url" class="img-tab-panel" style="display:none">
                        <input type="text" name="image_url" id="new_image_url"
                               placeholder="https://example.com/image.jpg"
                               oninput="previewImage(this.value,'new_url_preview','new_url_preview_err')">
                        <div class="img-preview-wrap" id="new_url_preview_wrap">
                            <img id="new_url_preview" src="" alt="URL preview"
                                 onerror="document.getElementById('new_url_preview_err').style.display='block';this.style.display='none';">
                            <div class="preview-err" id="new_url_preview_err">⚠️ Could not load this image. Check the URL.</div>
                        </div>
                    </div>
                </div>
                <!-- ── End image field ── -->

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="is_featured" name="is_featured">
                    <label for="is_featured">Set as Featured Post (shows at top of blog)</label>
                </div>
                <button type="submit" name="save_post" class="btn btn-green"><i class="fas fa-paper-plane"></i> Publish Post</button>
            </form>
        </div>

        <?php /* ═══ COMMENTS ═══ */ elseif ($tab === 'comments'): ?>
        <div class="card">
            <h2><i class="fas fa-clock"></i> Pending Approval (<?= count($pendingCmts) ?>)</h2>
            <?php if ($pendingCmts): ?>
            <table>
                <thead><tr><th>Author</th><th>Post</th><th>Comment</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($pendingCmts as $c): ?>
                <tr>
                    <td>
                        <strong><?= e($c['guest_name'] ?? $c['registered_name'] ?? 'User') ?></strong><br>
                        <small style="color:#aaa"><?= e($c['guest_email'] ?? '') ?></small>
                    </td>
                    <td><a href="../post.php?slug=<?= e($c['post_slug']) ?>" target="_blank"><?= e(substr($c['post_title'],0,30)) ?>...</a></td>
                    <td><?= e(substr($c['body'],0,80)) ?>...</td>
                    <td><?= date('M j', strtotime($c['created_at'])) ?></td>
                    <td>
                        <div class="action-btns">
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                <button name="comment_action" value="approved" class="btn btn-green btn-sm"><i class="fas fa-check"></i> Approve</button>
                            </form>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                <button name="comment_action" value="spam" class="btn btn-gray btn-sm"><i class="fas fa-ban"></i> Spam</button>
                            </form>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this comment?');">
                                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                <button name="comment_action" value="delete" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="empty"><i class="fas fa-check-circle" style="font-size:32px;display:block;margin-bottom:12px;color:#088178"></i>No comments pending approval!</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2><i class="fas fa-comments"></i> All Recent Comments</h2>
            <?php if ($allComments): ?>
            <table>
                <thead><tr><th>Author</th><th>Post</th><th>Comment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($allComments as $c): ?>
                <tr>
                    <td><?= e($c['registered_name'] ?? $c['guest_name'] ?? 'Guest') ?></td>
                    <td><?= e(substr($c['post_title'],0,25)) ?>...</td>
                    <td><?= e(substr($c['body'],0,60)) ?>...</td>
                    <td><span class="badge badge-<?= substr($c['status'],0,4) ?>"><?= e($c['status']) ?></span></td>
                    <td><?= date('M j', strtotime($c['created_at'])) ?></td>
                    <td>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete?');">
                            <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                            <button name="comment_action" value="delete" class="btn btn-red btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="empty">No comments yet.</p>
            <?php endif; ?>
        </div>

        <?php /* ═══ SUBSCRIBERS ═══ */ elseif ($tab === 'subscribers'): ?>
        <div class="card">
            <h2><i class="fas fa-envelope"></i> Newsletter Subscribers (<?= count($subscribers) ?>)</h2>
            <?php if ($subscribers): ?>
            <table>
                <thead><tr><th>#</th><th>Email</th><th>Subscribed On</th></tr></thead>
                <tbody>
                <?php foreach ($subscribers as $i => $sub): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($sub['email']) ?></td>
                    <td><?= date('M j, Y', strtotime($sub['subscribed_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="empty"><i class="fas fa-envelope-open" style="font-size:32px;display:block;margin-bottom:12px"></i>No subscribers yet.</p>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </div><!-- /.admin-main -->
</div><!-- /.admin-container -->

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
.admin-main{margin-left:230px;padding:0 40px 40px;width:calc(100% - 230px)}

.admin-header{
    position:sticky;top:0;z-index:100;
    background:rgba(244,247,246,.95);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    padding:22px 0 18px;margin-bottom:28px;border-bottom:1px solid #eee;
    display:flex;justify-content:space-between;align-items:center
}
.admin-header h2{font-size:20px;font-weight:600;color:#1a1a1a}
.header-sub{font-size:13px;color:#888;margin-top:2px}
.date-badge{
    background:#fff;padding:9px 16px;border-radius:50px;
    font-weight:600;font-size:13px;color:#444;
    border:1px solid #eee;box-shadow:0 2px 5px rgba(0,0,0,.04)
}

/* ── Blog tabs ── */
.blog-tabs{display:flex;gap:8px;margin-bottom:28px;flex-wrap:wrap}
.tab-btn{
    padding:9px 18px;border:1px solid #dde1e7;border-radius:8px;
    font-size:13px;font-weight:600;cursor:pointer;background:#fff;
    color:#555;text-decoration:none;transition:all .2s;
    display:flex;align-items:center;gap:7px
}
.tab-btn:hover{background:#f0f0f0;border-color:#ccc}
.tab-btn.active{background:#0f0f0f;color:#fff;border-color:#0f0f0f}

/* ── Message ── */
.msg{
    padding:12px 18px;border-radius:8px;margin-bottom:20px;
    font-size:14px;font-weight:500;background:#e8f5f4;
    color:#088178;border:1px solid #c0e4e1
}

/* ── Cards ── */
.card{
    background:#fff;padding:25px;border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,.03);border:1px solid #eee;margin-bottom:28px;
    transition:box-shadow .2s
}
.card:hover{box-shadow:0 4px 16px rgba(0,0,0,.07)}
.card h2{
    font-size:14px;color:#1a1a1a;margin-bottom:20px;
    padding-bottom:12px;border-bottom:1px solid #f1f1f1;
    display:flex;align-items:center;gap:8px;text-transform:uppercase;
    letter-spacing:.4px;font-weight:600
}
.card h2 i{color:#088178}

/* ── Forms ── */
.form-group{display:flex;flex-direction:column;margin-bottom:16px}
.form-group label{font-size:13px;font-weight:600;color:#555;margin-bottom:6px}
.form-group input[type=text],
.form-group select,
.form-group textarea{
    padding:10px 14px;border:1.5px solid #ddd;border-radius:8px;
    font-size:14px;font-family:inherit;transition:border-color .2s;outline:none
}
.form-group input[type=text]:focus,
.form-group select:focus,
.form-group textarea:focus{border-color:#088178}
.form-group textarea{resize:vertical;min-height:200px}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.checkbox-row{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.checkbox-row input{width:18px;height:18px;accent-color:#088178}
.checkbox-row label{font-size:14px;font-weight:500;color:#555}

/* ── Image source tabs ── */
.img-source-tabs{display:flex;gap:8px;margin-bottom:12px}
.img-tab{
    padding:7px 16px;border:1.5px solid #ddd;border-radius:8px;
    font-size:13px;font-weight:600;cursor:pointer;background:#f8f8f8;color:#555;
    display:flex;align-items:center;gap:6px;transition:all .2s
}
.img-tab:hover{border-color:#088178;color:#088178}
.img-tab.active{background:#088178;color:#fff;border-color:#088178}

/* ── Drop zone ── */
.upload-drop-zone{
    border:2px dashed #cce8e6;border-radius:10px;
    padding:32px 20px;text-align:center;cursor:pointer;
    background:#f5fcfb;transition:border-color .2s,background .2s;
    color:#6b8f8d
}
.upload-drop-zone:hover,.upload-drop-zone.dragover{
    border-color:#088178;background:#e8f5f4;color:#088178
}
.upload-drop-zone i{font-size:32px;display:block;margin-bottom:10px}
.upload-drop-zone p{font-size:14px;font-weight:500;margin-bottom:4px}
.upload-drop-zone small{font-size:12px;color:#aaa}

/* ── Image preview ── */
.img-preview-wrap{margin-top:12px;display:none}
.img-preview-wrap img{
    max-width:320px;max-height:200px;border-radius:8px;
    border:1px solid #ddd;object-fit:cover;display:block
}
.preview-err{font-size:12px;color:#e53935;margin-top:6px;display:none}
.clear-img-btn{
    margin-top:8px;padding:5px 12px;border:none;border-radius:6px;
    background:#f0f0f0;color:#666;font-size:12px;cursor:pointer;
    display:inline-flex;align-items:center;gap:5px
}
.clear-img-btn:hover{background:#e0e0e0;color:#333}
.current-img-note{
    font-size:12px;color:#888;margin-top:8px;
    display:flex;align-items:center;gap:5px
}
.current-img-note a{color:#088178}

/* ── Buttons ── */
.btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:9px 18px;border:none;border-radius:8px;
    font-size:13px;font-weight:600;cursor:pointer;
    transition:background .2s;text-decoration:none
}
.btn-green{background:#088178;color:#fff}.btn-green:hover{background:#066b63}
.btn-red{background:#e53935;color:#fff}.btn-red:hover{background:#b71c1c}
.btn-gray{background:#f0f0f0;color:#555;border:1px solid #ddd}.btn-gray:hover{background:#e0e0e0}
.btn-sm{padding:6px 13px;font-size:12px}

/* ── Table ── */
table{width:100%;border-collapse:collapse;font-size:14px}
th{
    text-align:left;background:#f9f9f9;padding:12px 15px;
    color:#777;font-size:11px;text-transform:uppercase;letter-spacing:.5px;font-weight:600
}
td{padding:14px 15px;border-bottom:1px solid #f1f1f1;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafcfb}

/* ── Badges ── */
.badge{
    padding:4px 12px;border-radius:20px;
    font-size:11px;font-weight:700;text-transform:uppercase;
    letter-spacing:.3px;display:inline-block
}
.badge-pub{background:#eafff0;color:#2ecc71}
.badge-draft{background:#fff9e6;color:#d4a017}
.badge-feat{background:#e6f7ff;color:#3498db}
.badge-pend{background:#fff9e6;color:#d4a017}
.badge-appr{background:#eafff0;color:#2ecc71}
.badge-spam{background:#fff0f0;color:#e74c3c}

/* ── Action buttons ── */
.action-btns{display:flex;gap:8px;flex-wrap:wrap}

/* ── Empty state ── */
.empty{text-align:center;padding:40px;color:#aaa;font-size:14px}

@media(max-width:768px){
    .admin-nav{width:100%;height:auto;position:relative}
    .admin-main{margin-left:0;width:100%;padding:20px}
    .form-row-2{grid-template-columns:1fr}
}
</style>

<script>
/* ── Image source tab switcher ────────────────────────────────── */
function switchImgTab(prefix, tab) {
    const uploadPanel = document.getElementById(prefix + '-tab-upload');
    const urlPanel    = document.getElementById(prefix + '-tab-url');
    const tabs        = uploadPanel.closest('.form-group').querySelectorAll('.img-tab');

    tabs.forEach(t => t.classList.remove('active'));
    event.currentTarget.classList.add('active');

    if (tab === 'upload') {
        uploadPanel.style.display = '';
        urlPanel.style.display    = 'none';
        // Clear URL field so it won't override the upload
        const urlInput = document.getElementById(prefix + '_image_url');
        if (urlInput) urlInput.value = '';
    } else {
        uploadPanel.style.display = 'none';
        urlPanel.style.display    = '';
        // Clear file input so it won't override the URL
        const fileInput = document.getElementById(prefix + '_image_file');
        if (fileInput) fileInput.value = '';
        hidePreview(prefix + '_upload_preview_wrap');
    }
}

/* ── Local file preview (before upload) ──────────────────────── */
function previewUpload(input, imgId, errId) {
    const wrapId = imgId + '_wrap';       // e.g. new_upload_preview_wrap
    const wrap   = document.getElementById(wrapId);
    const img    = document.getElementById(imgId);
    const err    = document.getElementById(errId);
    const zone   = input.id.replace('_file', '_drop_zone');   // e.g. new_drop_zone

    err.style.display = 'none';

    if (!input.files || !input.files[0]) {
        wrap.style.display = 'none';
        return;
    }

    const file = input.files[0];
    if (!file.type.startsWith('image/')) {
        err.style.display = 'block';
        err.textContent   = '⚠️ Please select a valid image file.';
        wrap.style.display = 'block';
        img.style.display  = 'none';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        img.src           = e.target.result;
        img.style.display = 'block';
        wrap.style.display = 'block';
        // Update drop zone to look "selected"
        const dz = document.getElementById(zone);
        if (dz) dz.style.borderColor = '#088178';
    };
    reader.readAsDataURL(file);
}

/* ── URL preview ─────────────────────────────────────────────── */
function previewImage(url, imgId, errId) {
    const wrapId = imgId + '_wrap';
    const wrap   = document.getElementById(wrapId);
    const img    = document.getElementById(imgId);
    const err    = document.getElementById(errId);

    err.style.display = 'none';
    img.style.display = 'block';

    if (!url || url.trim() === '') {
        wrap.style.display = 'none';
        img.src = '';
        return;
    }
    wrap.style.display = 'block';
    img.src = url.trim();
}

/* ── Clear uploaded file ─────────────────────────────────────── */
function clearUpload(fileInputId, imgId, errId, wrapId, zoneId) {
    const fi = document.getElementById(fileInputId);
    if (fi) fi.value = '';
    document.getElementById(wrapId).style.display = 'none';
    document.getElementById(imgId).src = '';
    document.getElementById(errId).style.display = 'none';
    const dz = document.getElementById(zoneId);
    if (dz) dz.style.borderColor = '';
}

function hidePreview(wrapId) {
    const w = document.getElementById(wrapId);
    if (w) w.style.display = 'none';
}

/* ── Drag and drop ───────────────────────────────────────────── */
function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDrop(e, prefix) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (!files.length) return;

    const fileInput = document.getElementById(prefix + '_image_file');
    // FileList is read-only, use DataTransfer to set it
    const dt = new DataTransfer();
    dt.items.add(files[0]);
    fileInput.files = dt.files;
    previewUpload(fileInput, prefix + '_upload_preview', prefix + '_upload_preview_err');
}

/* ── Auto-preview existing edit image on page load ───────────── */
(function () {
    const editUrl = document.getElementById('edit_image_url');
    if (editUrl && editUrl.value.trim() !== '') {
        // If there's an existing URL, show it in the URL tab panel
        // (upload tab is shown by default; switch to URL tab to display it)
        const btn = editUrl.closest('.form-group').querySelectorAll('.img-tab')[1];
        if (btn) btn.click();
        previewImage(editUrl.value, 'edit_url_preview', 'edit_url_preview_err');
    }
})();
</script>
</body>
</html>