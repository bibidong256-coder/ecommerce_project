<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/db.php';
require_once 'blog_functions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: blogs.php'); exit; }

$post = getPostBySlug($conn, $slug);
if (!$post) {
    http_response_code(404);
    echo '<p style="font-family:sans-serif;padding:60px;text-align:center;color:#c00;font-size:18px">Post not found. <a href="blogs.php">Back to blog</a></p>';
    exit;
}

incrementViews($conn, $post['id']);

$comments     = getComments($conn, $post['id']);
$commentCount = count($comments);
$categories   = getCategoriesWithCount($conn);
$popularPosts = getPopularPosts($conn, 3);
$currentUser  = getCurrentUser($conn);

// ── Admin check ────────────────────────────────────────
$isAdmin = isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';

// ── Base path for uploaded images ──────────────────────
define('BASE_PATH',    '/ecommerce');
define('FALLBACK_HERO',  'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=1200&q=80');
define('FALLBACK_THUMB', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=150&q=80');

function imgSrc(?string $url, string $fallback): string {
    if (empty($url)) return $fallback;
    if (str_starts_with($url, 'http')) return $url;
    return BASE_PATH . $url;
}

// ── Handle comment submit ──────────────────────────────
$cmtMsg = $cmtOk = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $result = postComment($conn, [
        'post_id'     => $post['id'],
        'user_id'     => currentUserId(),
        'guest_name'  => $_POST['guest_name']  ?? '',
        'guest_email' => $_POST['guest_email'] ?? '',
        'body'        => $_POST['body']        ?? '',
    ]);
    $cmtMsg = $result['msg'];
    $cmtOk  = $result['ok'];
}

// ── Handle newsletter ──────────────────────────────────
$nlMsg = $nlOk = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $result = subscribeEmail($conn, trim($_POST['email'] ?? ''));
    $nlMsg  = $result['msg'];
    $nlOk   = $result['ok'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($post['title']) ?> | Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #333; line-height: 1.7; }

        /* ── HEADER ── */
        #header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.08); position: sticky; top: 0; z-index: 999; }
        #header > a { text-decoration: none; display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        #header > a span { font-size: 13px; font-weight: 700; color: #111; margin-top: 4px; }
        .navbar-container { display: flex; align-items: center; }
        .nav-menu { display: flex; align-items: center; }
        #navbar { list-style: none; display: flex; gap: 24px; align-items: center; }
        #navbar li a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color .2s; }
        #navbar li a:hover, #navbar li a.active { color: #088178; }
        #close { display: none; }
        #mobile { display: none; align-items: center; gap: 16px; font-size: 20px; }
        #mobile a { color: #333; text-decoration: none; }
        #bar { cursor: pointer; color: #333; font-size: 22px; }
        .nav-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 997; }
        .nav-overlay.active { display: block; }

        @media (max-width: 768px) {
            #mobile { display: flex; }
            .nav-menu { position: fixed; top: 0; right: -100%; width: 260px; height: 100vh; background: #fff; box-shadow: -4px 0 20px rgba(0,0,0,.15); flex-direction: column; align-items: flex-start; padding: 70px 24px 24px; transition: right 0.3s ease; z-index: 998; overflow-y: auto; }
            .nav-menu.active { right: 0; }
            #navbar { flex-direction: column; align-items: flex-start; gap: 0; width: 100%; }
            #navbar li { width: 100%; border-bottom: 1px solid #f0f0f0; }
            #navbar li a { display: block; padding: 14px 4px; font-size: 15px; font-weight: 600; color: #333; }
            #close { display: block; position: absolute; top: 18px; right: 20px; font-size: 22px; color: #333; text-decoration: none; }
            #close:hover { color: #088178; }
        }

        /* ── POST HERO ── */
        .post-hero { background: linear-gradient(135deg, #088178, #04534e); color: #fff; padding: 50px 5%; }
        .breadcrumb { font-size: 13px; opacity: .8; margin-bottom: 14px; }
        .breadcrumb a { color: #fff; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { opacity: .6; margin: 0 6px; }
        .post-hero h1 { font-size: 32px; font-weight: 700; line-height: 1.3; margin-bottom: 16px; }
        .post-meta { display: flex; flex-wrap: wrap; gap: 18px; font-size: 13px; opacity: .85; margin-bottom: 20px; }
        .post-meta i { margin-right: 5px; }

        /* ── BACK BUTTON ── */
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15); color: #fff;
            padding: 9px 18px; border-radius: 8px; text-decoration: none;
            font-size: 13px; font-weight: 600; border: 1px solid rgba(255,255,255,0.3);
            transition: background .2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.25); }

        /* ── LAYOUT ── */
        .container { width: 90%; max-width: 1200px; margin: 0 auto; }
        .post-layout { display: grid; grid-template-columns: 1fr 300px; gap: 40px; padding: 50px 0; }

        /* ── POST BODY ── */
        .post-image { width: 100%; max-height: 420px; object-fit: cover; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.1); display: block; }
        .post-body { background: #fff; border-radius: 12px; padding: 36px; box-shadow: 0 2px 12px rgba(0,0,0,.07); margin-bottom: 30px; font-size: 16px; color: #444; }
        .post-body p { margin-bottom: 18px; }
        .post-body h2, .post-body h3 { color: #088178; margin: 24px 0 12px; }
        .category-tag { display: inline-block; background: #e8f5f4; color: #088178; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-decoration: none; margin-bottom: 20px; }

        /* ── BACK TO BLOG (inside content) ── */
        .back-to-blog {
            display: inline-flex; align-items: center; gap: 8px;
            color: #088178; font-weight: 600; font-size: 14px;
            text-decoration: none; margin-top: 10px; margin-bottom: 24px;
            padding: 9px 18px; border: 2px solid #088178; border-radius: 8px;
            transition: all .2s;
        }
        .back-to-blog:hover { background: #088178; color: #fff; }

        /* ── COMMENTS ── */
        .comments-section { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .section-title { font-size: 20px; color: #088178; font-weight: 700; margin-bottom: 24px; padding-bottom: 10px; border-bottom: 2px solid #e8f5f4; }
        .comment { display: flex; gap: 14px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f0f0f0; }
        .comment:last-of-type { border-bottom: none; }
        .c-avatar { width: 42px; height: 42px; background: #088178; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 16px; flex-shrink: 0; }
        .c-content { flex: 1; }
        .c-author { font-weight: 600; font-size: 14px; color: #222; }
        .c-date { font-size: 12px; color: #aaa; margin-bottom: 6px; }
        .c-body { font-size: 14px; color: #555; line-height: 1.6; }
        .no-comments { color: #aaa; font-size: 14px; text-align: center; padding: 24px 0; }

        /* ── COMMENT FORM ── */
        .comment-form { margin-top: 30px; padding-top: 24px; border-top: 2px solid #e8f5f4; }
        .comment-form h3 { font-size: 18px; color: #088178; margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 16px; }
        .form-group label { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
        .form-group input, .form-group textarea { padding: 11px 14px; border: 1.5px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit; transition: border-color .2s; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: #088178; }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .btn-submit { background: #088178; color: #fff; padding: 12px 28px; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: #04534e; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; font-weight: 500; }
        .alert-ok { background: #e8f5f4; color: #088178; border: 1px solid #c0e4e1; }
        .alert-err { background: #fde8e8; color: #c00; border: 1px solid #f5c6c6; }
        .logged-in-note { font-size: 13px; color: #088178; margin-bottom: 16px; background: #e8f5f4; padding: 10px 14px; border-radius: 8px; }

        /* ── SIDEBAR ── */
        .sidebar { display: flex; flex-direction: column; gap: 28px; }
        .sidebar-widget { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .sidebar-widget h3 { font-size: 16px; color: #088178; font-weight: 700; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #e8f5f4; }
        .categories-list { list-style: none; }
        .categories-list li { border-bottom: 1px solid #f0f0f0; }
        .categories-list li:last-child { border-bottom: none; }
        .categories-list a { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; color: #555; text-decoration: none; font-size: 14px; transition: color .2s; }
        .categories-list a:hover { color: #088178; }
        .categories-list span { background: #e8f5f4; color: #088178; font-size: 12px; font-weight: 700; padding: 2px 9px; border-radius: 20px; }
        .popular-posts { list-style: none; }
        .popular-post { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .popular-post:last-child { border-bottom: none; }
        .popular-post img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .popular-post-content h4 { font-size: 13px; line-height: 1.4; margin-bottom: 4px; }
        .popular-post-content h4 a { color: #333; text-decoration: none; transition: color .2s; }
        .popular-post-content h4 a:hover { color: #088178; }
        .popular-post-content .date { font-size: 12px; color: #aaa; }
        .shop-banner { background: linear-gradient(135deg, #088178, #04534e); padding: 24px; border-radius: 10px; text-align: center; color: #fff; }
        .shop-banner h3 { color: #fff; border: none; padding: 0; margin-bottom: 8px; font-size: 18px; }
        .shop-banner p { font-size: 14px; opacity: .9; margin-bottom: 16px; }
        .shop-banner a { display: inline-block; background: #fff; color: #088178; padding: 9px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; }

        /* ── NEWSLETTER ── */
        .newsletter { background: linear-gradient(135deg, #088178, #04534e); padding: 60px 5%; text-align: center; color: #fff; margin-top: 20px; }
        .newsletter h2 { font-size: 26px; margin-bottom: 12px; }
        .newsletter p  { font-size: 15px; opacity: .88; max-width: 560px; margin: 0 auto 28px; }
        .newsletter-form { display: flex; justify-content: center; max-width: 480px; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.15); }
        .newsletter-form input { flex: 1; padding: 13px 18px; border: none; font-size: 14px; outline: none; color: #333; }
        .newsletter-form button { padding: 13px 26px; background: #04534e; color: #fff; border: none; font-size: 14px; font-weight: 700; cursor: pointer; }
        .newsletter-form button:hover { background: #032e2b; }

        /* ── FOOTER ── */
        footer.section-p1 { display: flex; flex-wrap: wrap; gap: 32px; padding: 50px 5% 30px; background: #1a1a2e; color: #ccc; }
        footer .col { flex: 1 1 160px; display: flex; flex-direction: column; gap: 10px; }
        footer .col h4 { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 6px; }
        footer .col p  { font-size: 0.87rem; color: #aaa; line-height: 1.6; }
        footer .col p strong { color: #ccc; }
        footer .col a  { font-size: 0.87rem; color: #aaa; text-decoration: none; transition: color .2s; width: fit-content; }
        footer .col a:hover { color: #088178; }
        footer .follow h4 { margin-top: 14px; }
        footer .icon { display: flex; gap: 12px; margin-top: 6px; }
        footer .icon i { font-size: 1.2rem; color: #aaa; cursor: pointer; transition: color .2s, transform .2s; }
        footer .icon i:hover { color: #088178; transform: scale(1.2); }
        footer .col.install .row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        footer .col.install .row img, footer .col.install > img { border-radius: 6px; object-fit: cover; }
        footer .copyright { width: 100%; text-align: center; padding-top: 22px; border-top: 1px solid #2e2e4a; font-size: 0.82rem; color: #666; }

        /* ── ADMIN FLOATING BAR ── */
        .admin-float-bar {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        .admin-float-bar a {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 12px 22px;
            border-radius: 50px;
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(0,0,0,.18);
            transition: transform .2s, box-shadow .2s, background .2s;
            white-space: nowrap;
        }
        .admin-float-bar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0,0,0,.22);
        }
        .admin-float-bar .btn-edit-post {
            background: #088178;
            color: #fff;
        }
        .admin-float-bar .btn-edit-post:hover { background: #066b63; }
        .admin-float-bar .btn-back-admin {
            background: #1a1a1a;
            color: #fff;
        }
        .admin-float-bar .btn-back-admin:hover { background: #333; }
        .admin-float-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #aaa;
            text-align: right;
            padding-right: 4px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) { .post-layout { grid-template-columns: 1fr; } .sidebar { order: -1; } }
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .post-body { padding: 20px; }
            .post-hero h1 { font-size: 24px; }
            .newsletter-form { flex-direction: column; overflow: visible; gap: 10px; }
            .newsletter-form input, .newsletter-form button { border-radius: 8px; width: 100%; }
            .admin-float-bar { bottom: 16px; right: 16px; }
            .admin-float-bar a { padding: 10px 16px; font-size: 13px; }
        }
    </style>
</head>
<body>

<!-- ══ HEADER ══ -->
<!-- <section id="header"> ... </section> -->

<!-- ══ POST HERO ══ -->
<section class="post-hero">
    <div class="container">
        <p class="breadcrumb">
            <a href="blogs.php">Blog</a>
            <span>&rsaquo;</span>
            <a href="category.php?slug=<?= e($post['category_slug']) ?>"><?= e($post['category']) ?></a>
        </p>
        <h1><?= e($post['title']) ?></h1>
        <div class="post-meta">
            <span><i class="far fa-calendar"></i> <?= formatDate($post['created_at']) ?></span>
            <span><i class="far fa-user"></i> <?= e($post['author']) ?></span>
            <span><i class="far fa-folder"></i> <?= e($post['category']) ?></span>
            <span><i class="far fa-eye"></i> <?= number_format($post['views']) ?> views</span>
            <span><i class="far fa-comments"></i> <?= $commentCount ?> comments</span>
        </div>
        <a href="blogs.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Blog
        </a>
    </div>
</section>

<!-- ══ MAIN ══ -->
<div class="container post-layout">
    <main>

        <!-- Post Image -->
        <?php if (!empty($post['image_url'])): ?>
            <img src="<?= e(imgSrc($post['image_url'], FALLBACK_HERO)) ?>"
                 alt="<?= e($post['title']) ?>"
                 class="post-image"
                 onerror="this.src='<?= FALLBACK_HERO ?>'; this.onerror=null;">
        <?php endif; ?>

        <!-- Post Content -->
        <div class="post-body">
            <a href="category.php?slug=<?= e($post['category_slug']) ?>" class="category-tag">
                <i class="far fa-folder"></i> <?= e($post['category']) ?>
            </a>
            <p><?= nl2br(e($post['body'])) ?></p>
        </div>

        <!-- Back to Blog button inside content -->
        <a href="blogs.php" class="back-to-blog">
            <i class="fas fa-arrow-left"></i> Back to Blog
        </a>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2 class="section-title"><i class="far fa-comments"></i> Comments (<?= $commentCount ?>)</h2>

            <?php if ($comments): ?>
                <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <div class="c-avatar">
                        <?= strtoupper(substr($c['registered_name'] ?? $c['guest_name'] ?? 'G', 0, 1)) ?>
                    </div>
                    <div class="c-content">
                        <div class="c-author"><?= e($c['registered_name'] ?? $c['guest_name'] ?? 'Guest') ?></div>
                        <div class="c-date"><?= formatDate($c['created_at']) ?></div>
                        <div class="c-body"><?= nl2br(e($c['body'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-comments"><i class="far fa-comment" style="font-size:28px;display:block;margin-bottom:10px;color:#ddd;"></i>No comments yet — be the first to share your thoughts!</p>
            <?php endif; ?>

            <div class="comment-form">
                <h3>Leave a Comment</h3>
                <?php if ($cmtMsg): ?>
                    <div class="alert <?= $cmtOk ? 'alert-ok' : 'alert-err' ?>"><?= e($cmtMsg) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?php if ($currentUser): ?>
                        <p class="logged-in-note">
                            <i class="fa fa-check-circle"></i> Commenting as <strong><?= e($currentUser['name']) ?></strong>
                        </p>
                    <?php else: ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="guest_name">Name *</label>
                                <input type="text" id="guest_name" name="guest_name" placeholder="Your name" required value="<?= e($_POST['guest_name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="guest_email">Email * <small>(not published)</small></label>
                                <input type="email" id="guest_email" name="guest_email" placeholder="your@email.com" required value="<?= e($_POST['guest_email'] ?? '') ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="body">Comment *</label>
                        <textarea id="body" name="body" required placeholder="Share your thoughts..."><?= e($_POST['body'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="comment_submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </form>
            </div>
        </div>

    </main>

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="sidebar-widget">
            <h3>Categories</h3>
            <ul class="categories-list">
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="category.php?slug=<?= e($cat['slug']) ?>">
                        <?= e($cat['name']) ?>
                        <span><?= (int)$cat['post_count'] ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="sidebar-widget">
            <h3>Popular Posts</h3>
            <ul class="popular-posts">
                <?php foreach ($popularPosts as $pop): ?>
                <li class="popular-post">
                    <img src="<?= e(imgSrc($pop['image_url'] ?? '', FALLBACK_THUMB)) ?>"
                         alt="<?= e($pop['title']) ?>"
                         onerror="this.src='<?= FALLBACK_THUMB ?>'; this.onerror=null;">
                    <div class="popular-post-content">
                        <h4><a href="post.php?slug=<?= e($pop['slug']) ?>"><?= e($pop['title']) ?></a></h4>
                        <div class="date"><?= formatDate($pop['created_at']) ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="sidebar-widget">
            <div class="shop-banner">
                <h3>Summer Sale!</h3>
                <p>Up to 40% off on selected footwear</p>
                <a href="shop.php">Shop Now</a>
            </div>
        </div>

    </aside>
</div>

<!-- ══ NEWSLETTER ══ -->
<section class="newsletter">
    <div class="container">
        <h2>Stay Updated on Footwear Trends</h2>
        <p>Subscribe and get the latest shoe trends, tips, and exclusive offers in your inbox.</p>
        <?php if ($nlMsg): ?>
            <div style="max-width:480px;margin:0 auto 20px;padding:11px 18px;border-radius:8px;font-size:14px;font-weight:500;
                background:<?= $nlOk ? 'rgba(255,255,255,.2)' : 'rgba(200,0,0,.25)' ?>;
                color:#fff;border:1px solid <?= $nlOk ? 'rgba(255,255,255,.35)' : 'rgba(200,0,0,.4)' ?>;">
                <?= e($nlMsg) ?>
            </div>
        <?php endif; ?>
        <form class="newsletter-form" method="POST">
            <input type="email" name="email" placeholder="Enter your email address" required>
            <button type="submit" name="subscribe">Subscribe</button>
        </form>
    </div>
</section>

<!-- ══ FOOTER ══ -->
<!-- <footer class="section-p1"> ... </footer> -->

<!-- ══ ADMIN FLOATING BAR (admin only) ══════════════════════════ -->
<?php if ($isAdmin): ?>
<div class="admin-float-bar">
    <span class="admin-float-label">Admin Tools</span>
    <a href="blog_admin.php?tab=posts&edit=<?= $post['id'] ?>" class="btn-edit-post">
        <i class="fas fa-edit"></i> Edit This Post
    </a>
    <a href="blog_admin.php?tab=posts" class="btn-back-admin">
        <i class="fas fa-th-list"></i> All Posts
    </a>
</div>
<?php endif; ?>

<script>
    function openMenu() {
        document.getElementById('nav-menu').classList.add('active');
        document.getElementById('nav-overlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeMenu() {
        document.getElementById('nav-menu').classList.remove('active');
        document.getElementById('nav-overlay').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('#navbar li a').forEach(l => l.addEventListener('click', closeMenu));
    window.addEventListener('resize', () => { if (window.innerWidth > 768) closeMenu(); });

    const yearEl = document.getElementById('year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();
</script>

</body>
</html>