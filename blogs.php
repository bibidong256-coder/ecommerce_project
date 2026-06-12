<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/db.php';
require_once 'blog_functions.php';

// ── Fetch blog data ────────────────────────────────────
$featured     = getFeaturedPost($conn);
$recentPosts  = getRecentPosts($conn, 6, $featured['id'] ?? 0);
$categories   = getCategoriesWithCount($conn);
$popularPosts = getPopularPosts($conn, 3);

// ── Who is logged in? ──────────────────────────────────
$loggedInUser = $_SESSION['user'] ?? null;

// ── Newsletter POST ────────────────────────────────────
$nlMsg = $nlOk = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $result = subscribeEmail($conn, trim($_POST['email'] ?? ''));
    $nlMsg  = $result['msg'];
    $nlOk   = $result['ok'];
}

// ── Base path for uploaded images ─────────────────────
// Change '/ecommerce' if your project is in a different folder
define('BASE_PATH',        '/ecommerce');

// ── Fallback image constants ───────────────────────────
define('FALLBACK_FEATURED', 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=1200&q=80');
define('FALLBACK_CARD',     'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80');
define('FALLBACK_THUMB',    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=150&q=80');

// ── Helper: resolve image src ──────────────────────────
function imgSrc(?string $url, string $fallback): string {
    if (empty($url)) return $fallback;
    // If it's already an absolute URL (http/https), use as-is
    if (str_starts_with($url, 'http')) return $url;
    // Otherwise prepend base path
    return BASE_PATH . $url;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepStyle Blog | Footwear Trends &amp; Tips</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #333; line-height: 1.6; }

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

        /* ── HERO ── */
        .hero { background: linear-gradient(135deg, #088178 0%, #04534e 100%); color: #fff; text-align: center; padding: 70px 5%; }
        .hero h1 { font-size: 36px; font-weight: 700; letter-spacing: 1px; margin-bottom: 14px; }
        .hero p  { font-size: 16px; opacity: .88; max-width: 620px; margin: 0 auto 28px; }

        .btn { display: inline-block; padding: 11px 26px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; transition: opacity .2s, background .2s; cursor: pointer; border: none; }
        .btn-primary { background: #fff; color: #088178; }
        .btn-primary:hover { opacity: .88; }

        /* ── LAYOUT ── */
        .container { width: 90%; max-width: 1200px; margin: 0 auto; }
        .blog-container { display: grid; grid-template-columns: 1fr 300px; gap: 36px; padding: 50px 0; }

        .section-title { font-size: 22px; color: #088178; font-weight: 700; text-decoration: underline; text-underline-offset: 8px; margin-bottom: 24px; }

        /* ── FEATURED ── */
        #featured { margin-bottom: 50px; }
        .featured-article { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: box-shadow .2s; }
        .featured-article:hover { box-shadow: 0 8px 24px rgba(8,129,120,.13); }
        .featured-image { width: 100%; height: 320px; object-fit: cover; display: block; }
        .article-content { padding: 28px; }
        .article-content h2 { font-size: 22px; color: #222; margin: 12px 0; line-height: 1.35; }
        .article-excerpt { color: #666; font-size: 15px; margin-bottom: 20px; }
        .article-meta { display: flex; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #999; margin-bottom: 10px; }
        .article-meta i { margin-right: 5px; color: #088178; }
        .read-more { display: inline-flex; align-items: center; gap: 6px; color: #088178; font-weight: 600; font-size: 14px; text-decoration: none; transition: gap .2s; }
        .read-more:hover { gap: 10px; }

        /* ── ARTICLE CARDS ── */
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; }
        .article-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; display: flex; flex-direction: column; }
        .article-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(8,129,120,.13); }
        .article-card img { width: 100%; height: 180px; object-fit: cover; display: block; transition: transform .3s; }
        .article-card:hover img { transform: scale(1.04); }
        .article-card-content { padding: 18px; flex: 1; display: flex; flex-direction: column; }
        .article-card-content h3 { font-size: 15px; color: #222; margin: 10px 0 8px; line-height: 1.4; }
        .article-card-content p  { font-size: 13px; color: #777; flex: 1; margin-bottom: 14px; }

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
        .shop-banner { background: linear-gradient(135deg, #088178 0%, #04534e 100%); padding: 24px; border-radius: 10px; text-align: center; color: #fff; }
        .shop-banner h3 { color: #fff; border-bottom: none; padding-bottom: 0; margin-bottom: 8px; font-size: 18px; }
        .shop-banner p { font-size: 14px; opacity: .9; margin-bottom: 16px; }
        .shop-banner .btn { background: #fff; color: #088178; padding: 9px 22px; }
        .shop-banner .btn:hover { opacity: .88; }

        /* ── NEWSLETTER ── */
        .newsletter { background: linear-gradient(135deg, #088178 0%, #04534e 100%); padding: 60px 5%; text-align: center; color: #fff; margin-top: 20px; }
        .newsletter h2 { font-size: 26px; margin-bottom: 12px; }
        .newsletter p  { font-size: 15px; opacity: .88; max-width: 560px; margin: 0 auto 28px; }
        .newsletter-form { display: flex; justify-content: center; max-width: 480px; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.15); }
        .newsletter-form input { flex: 1; padding: 13px 18px; border: none; font-size: 14px; outline: none; color: #333; }
        .newsletter-form button { padding: 13px 26px; background: #04534e; color: #fff; border: none; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .2s; white-space: nowrap; }
        .newsletter-form button:hover { background: #032e2b; }

        /* ── FOOTER ── */
        footer.section-p1 { display: flex; flex-wrap: wrap; gap: 32px; padding: 50px 5% 30px; background: #1a1a2e; color: #ccc; }
        footer .col { flex: 1 1 160px; display: flex; flex-direction: column; gap: 10px; }
        footer .col img.logo { margin-bottom: 8px; border-radius: 8px; }
        footer .col h4 { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 6px; }
        footer .col p  { font-size: 0.87rem; color: #aaa; line-height: 1.6; }
        footer .col p strong { color: #ccc; }
        footer .col a  { font-size: 0.87rem; color: #aaa; text-decoration: none; transition: color .2s; width: fit-content; }
        footer .col a:hover { color: #088178; }
        footer .follow h4 { margin-top: 14px; }
        footer .icon  { display: flex; gap: 12px; margin-top: 6px; }
        footer .icon i { font-size: 1.2rem; color: #aaa; cursor: pointer; transition: color .2s, transform .2s; }
        footer .icon i:hover { color: #088178; transform: scale(1.2); }
        footer .col.install .row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        footer .col.install .row img, footer .col.install > img { border-radius: 6px; object-fit: cover; }
        footer .copyright { width: 100%; text-align: center; padding-top: 22px; border-top: 1px solid #2e2e4a; font-size: 0.82rem; color: #666; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) { .blog-container { grid-template-columns: 1fr; padding: 30px 0; } .sidebar { order: -1; } }
        @media (max-width: 640px) {
            .hero { padding: 50px 5%; } .hero h1 { font-size: 26px; } .hero p { font-size: 14px; }
            .featured-image { height: 220px; } .article-content { padding: 18px; } .article-content h2 { font-size: 18px; }
            .articles-grid { grid-template-columns: 1fr; }
            .newsletter-form { flex-direction: column; overflow: visible; box-shadow: none; gap: 10px; }
            .newsletter-form input  { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
            .newsletter-form button { border-radius: 8px; width: 100%; }
            footer.section-p1 { padding: 36px 5% 20px; } footer .col { flex: 1 1 140px; }
        }
    </style>
</head>
<body>

<!-- ══ HEADER ══ -->
<section id="header">
    <a href="index.php" style="text-decoration:none;display:flex;flex-direction:column;align-items:center;">
        <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width:50px;height:50px;" alt="Logo">
        <span style="font-size:14px;font-weight:bold;color:black;margin-top:5px;">KISKEN TRENDS DUUKA</span>
    </a>
    <div class="navbar-container">
        <div class="nav-menu" id="nav-menu">
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a class="active" href="blogs.php">Blog</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
                <li><a href="login.php"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
            <a href="#" id="close" onclick="closeMenu()"><i class="fas fa-times"></i></a>
        </div>
        <div class="nav-overlay" id="nav-overlay" onclick="closeMenu()"></div>
        <div id="mobile">
            <a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
            <i id="bar" class="fas fa-bars" onclick="openMenu()"></i>
        </div>
    </div>
</section>

<!-- ══ HERO ══ -->
<section class="hero">
    <div class="container">
        <h1>StepStyle Footwear Blog</h1>
        <p>Discover the latest trends, styling tips, and expert advice on all things footwear. From running sneakers to elegant heels, we've got you covered.</p>
        <?php if ($featured): ?>
            <a href="post.php?slug=<?= htmlspecialchars($featured['slug'], ENT_QUOTES) ?>" class="btn btn-primary">Read Featured Article</a>
        <?php else: ?>
            <a href="#recent" class="btn btn-primary">Browse Articles</a>
        <?php endif; ?>
    </div>
</section>

<!-- ══ MAIN CONTENT ══ -->
<div class="container blog-container">
    <main>

        <!-- Featured Article -->
        <?php if ($featured): ?>
        <section id="featured">
            <h2 class="section-title">Featured Article</h2>
            <article class="featured-article">
                <img src="<?= htmlspecialchars(imgSrc($featured['image_url'] ?? '', FALLBACK_FEATURED), ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($featured['title'], ENT_QUOTES) ?>"
                     class="featured-image"
                     onerror="this.src='<?= FALLBACK_FEATURED ?>';this.onerror=null;">
                <div class="article-content">
                    <div class="article-meta">
                        <span><i class="far fa-calendar"></i> <?= formatDate($featured['created_at']) ?></span>
                        <span><i class="far fa-user"></i> <?= htmlspecialchars($featured['author'], ENT_QUOTES) ?></span>
                        <span><i class="far fa-folder"></i> <?= htmlspecialchars($featured['category'], ENT_QUOTES) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($featured['views']) ?> views</span>
                    </div>
                    <h2><?= htmlspecialchars($featured['title'], ENT_QUOTES) ?></h2>
                    <p class="article-excerpt"><?= htmlspecialchars($featured['excerpt'], ENT_QUOTES) ?></p>
                    <a href="post.php?slug=<?= htmlspecialchars($featured['slug'], ENT_QUOTES) ?>" class="read-more">
                        Continue Reading <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
        </section>
        <?php endif; ?>

        <!-- Recent Articles -->
        <section id="recent">
            <h2 class="section-title">Recent Articles</h2>
            <?php if ($recentPosts): ?>
            <div class="articles-grid">
                <?php foreach ($recentPosts as $post): ?>
                <article class="article-card">
                    <img src="<?= htmlspecialchars(imgSrc($post['image_url'] ?? '', FALLBACK_CARD), ENT_QUOTES) ?>"
                         alt="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>"
                         onerror="this.src='<?= FALLBACK_CARD ?>';this.onerror=null;">
                    <div class="article-card-content">
                        <div class="article-meta">
                            <span><i class="far fa-calendar"></i> <?= formatDate($post['created_at']) ?></span>
                            <span><i class="far fa-comments"></i> <?= getCommentCount($conn, $post['id']) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h3>
                        <p><?= htmlspecialchars($post['excerpt'], ENT_QUOTES) ?></p>
                        <a href="post.php?slug=<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>" class="read-more">Read More</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="text-align:center;padding:50px;color:#aaa;font-size:15px;">
                No articles published yet. Check back soon!
            </p>
            <?php endif; ?>
        </section>

    </main>

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="sidebar-widget">
            <h3>Categories</h3>
            <ul class="categories-list">
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="category.php?slug=<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>">
                        <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
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
                    <img src="<?= htmlspecialchars(imgSrc($pop['image_url'] ?? '', FALLBACK_THUMB), ENT_QUOTES) ?>"
                         alt="<?= htmlspecialchars($pop['title'], ENT_QUOTES) ?>"
                         onerror="this.src='<?= FALLBACK_THUMB ?>';this.onerror=null;">
                    <div class="popular-post-content">
                        <h4>
                            <a href="post.php?slug=<?= htmlspecialchars($pop['slug'], ENT_QUOTES) ?>">
                                <?= htmlspecialchars($pop['title'], ENT_QUOTES) ?>
                            </a>
                        </h4>
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
                <a href="shop.php" class="btn">Shop Now</a>
            </div>
        </div>

    </aside>
</div>

<!-- ══ NEWSLETTER ══ -->
<section class="newsletter">
    <div class="container">
        <h2>Stay Updated on Footwear Trends</h2>
        <p>Subscribe to our newsletter and get the latest shoe trends, styling tips, and exclusive offers delivered straight to your inbox.</p>
        <?php if ($nlMsg): ?>
            <div style="max-width:480px;margin:0 auto 20px;padding:11px 18px;border-radius:8px;font-size:14px;font-weight:500;
                background:<?= $nlOk ? 'rgba(255,255,255,.2)' : 'rgba(200,0,0,.25)' ?>;
                color:#fff;border:1px solid <?= $nlOk ? 'rgba(255,255,255,.35)' : 'rgba(200,0,0,.4)' ?>;">
                <?= htmlspecialchars($nlMsg, ENT_QUOTES) ?>
            </div>
        <?php endif; ?>
        <form class="newsletter-form" method="POST">
            <input type="email" name="email" placeholder="Enter your email address" required>
            <button type="submit" name="subscribe">Subscribe</button>
        </form>
    </div>
</section>

<!-- ══ FOOTER ══ -->
<footer class="section-p1">
    <div class="col">
        <img class="logo" src="shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
        <h4>Contact</h4>
        <p><span>Address:</span> Kaguje Road, Street 32, Kampala</p>
        <p><strong>Phone:</strong> +256789340639</p>
        <p><strong>Hours:</strong> 10:00–18:00, Mon–Sat</p>
        <div class="follow">
            <h4>Follow us</h4>
            <div class="icon">
                <i class="fab fa-facebook"></i>
                <i class="fab fa-twitter"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-youtube"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <h4>About</h4>
        <a href="about.php">About us</a>
        <a href="delivery.php">Delivery Information</a>
        <a href="privacy-policy.php">Privacy &amp; Policy</a>
        <a href="terms.php">Terms &amp; Conditions</a>
        <a href="contact.php">Contact us</a>
    </div>
    <div class="col">
        <h4>My Account</h4>
        <a href="login.php">Sign in</a>
        <a href="cart.php">View Cart</a>
        <a href="track-my-order.php">Track My Order</a>
        <a href="help.php">Help</a>
    </div>
    <div class="col install">
        <h4>Install App</h4>
        <p>From App Store or Google Play</p>
        <div class="row">
            <img src="shoes images/apps/download.jpeg" width="50px" alt="App Store">
            <img src="shoes images/apps/images (4).jpeg" width="50px" alt="Google Play">
        </div>
        <p>Secured Payment Gateways</p>
        <img src="shoes images/payement/download (9).jpeg" width="50px" alt="">
    </div>
    <div class="copyright">
        <p>&copy; <span id="year"></span>, Bibidong Tech Ug</p>
    </div>
</footer>

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
    document.querySelectorAll('#navbar li a').forEach(link => {
        link.addEventListener('click', closeMenu);
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeMenu();
    });
    document.getElementById('year').textContent = new Date().getFullYear();
</script>

</body>
</html>