<?php
// index.php — Dynamic Home Page
// Uses your existing config.php with $conn (PDO)
require_once 'config/db.php'; // your existing file — gives us $conn

// ── Fetch all sections ─────────────────────────────
$hero        = $conn->query("SELECT * FROM hero LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$tags        = $conn->query("SELECT * FROM trending_tags WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$products    = $conn->query("SELECT * FROM featured_products WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$categories  = $conn->query("SELECT * FROM home_categories WHERE is_active=1 AND section='category' ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$trends      = $conn->query("SELECT * FROM home_categories WHERE is_active=1 AND section='trend' ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$featureBoxes= $conn->query("SELECT * FROM feature_boxes WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mainBanner  = $conn->query("SELECT * FROM main_banner LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$smBanners   = $conn->query("SELECT * FROM small_banners WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$seasonal    = $conn->query("SELECT * FROM seasonal_banners WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$newsletter  = $conn->query("SELECT * FROM newsletter_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// ── Star helper ───────────────────────────────────
function stars(int $n): string {
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<!-- ─── Header ─────────────────────────────────── -->
<section id="header">
    <a href="#" style="text-decoration:none;display:flex;flex-direction:column;align-items:center;">
        <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width:50px;height:50px;" alt="Logo">
        <span style="font-size:14px;font-weight:bold;color:black;margin-top:5px;">KISKEN TRENDS DUUKA</span>
    </a>
    <div class="navbar-container">
        <div class="nav-menu" id="nav-menu">
            <ul id="navbar">
                <li><a class="active" href="index.php">Home</a></li>
                <li><a href="Shop.php">Shop</a></li>
                <li><a href="blogs.php">Blog</a></li>
                <li><a href="About.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
                <li><a href="login.php"><i class="fa fa-user"></i></a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>

            </ul>
            <a href="#" id="close" onclick="closeMenu()"><i class="fas fa-times"></i></a>
        </div>
        <div class="nav-overlay" id="nav-overlay" onclick="closeMenu()"></div>
        <div id="mobile">
            <a href="cart.php"><i class="fa fa-shopping-cart"></i></a>
            <i id="bar" class="fas fa-bars" onclick="openMenu()"></i>
        </div>
    </div>
</section>

<!-- ─── Hero ────────────────────────────────────── -->
<?php if ($hero): ?>
<section id="hero">
    <div class="main">
        <h4><?= htmlspecialchars($hero['badge_text']) ?></h4>
        <h2><?= htmlspecialchars($hero['heading']) ?></h2>
        <h1><?= htmlspecialchars($hero['subheading']) ?></h1>
        <p><?= htmlspecialchars($hero['description']) ?></p>
        <a href="<?= htmlspecialchars($hero['button_url']) ?>">
            <button><?= htmlspecialchars($hero['button_text']) ?></button>
        </a>
    </div>
    <div class="hero-image">
        <img src="<?= htmlspecialchars($hero['image_path']) ?>" alt="Hero">
    </div>
</section>
<?php endif; ?>

<br>

<!-- ─── What's Hot / Trending Tags ──────────────── -->
<section>
    <h2>What's hot now</h2>
    <?php if ($tags): ?>
    <div class="tags">
        <?php foreach ($tags as $tag): ?>
        <div class="tag"><?= htmlspecialchars($tag['tag_name']) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Viral Products -->
    <div class="viral">
        <div class="viral-header">
            <h2>Viral Shoe Designs finds</h2>
            <a href="#">See more</a>
        </div>
        <div class="products">
            <?php foreach ($products as $p): ?>
            <div class="product">
                <a href="<?= htmlspecialchars($p['product_url']) ?>">
                    <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                    <div class="title"><?= htmlspecialchars($p['title']) ?></div>
                    <div class="rating"><?= stars((int)$p['rating_stars']) ?> <?= number_format($p['rating_count']) ?></div>
                    <div class="price">shs<?= number_format($p['price']) ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<br>

<!-- ─── Shop by Category ────────────────────────── -->
<section>
    <h2>Shop by category</h2>
    <div class="grid">
        <?php foreach ($categories as $c): ?>
        <div class="card">
            <a href="<?= htmlspecialchars($c['link_url']) ?>">
                <div class="img-box"><img src="<?= htmlspecialchars($c['image_path']) ?>" alt="<?= htmlspecialchars($c['label']) ?>"></div>
                <div class="label"><?= htmlspecialchars($c['label']) ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <h2>Trends &amp; deals</h2>
    <div class="grid">
        <?php foreach ($trends as $t): ?>
        <div class="card <?= $t['is_highlight'] ? 'highlight' : '' ?>">
            <a href="<?= htmlspecialchars($t['link_url']) ?>">
                <div class="img-box"><img src="<?= htmlspecialchars($t['image_path']) ?>" alt="<?= htmlspecialchars($t['label']) ?>"></div>
                <div class="label"><?= htmlspecialchars($t['label']) ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<br><br>

<!-- ─── Feature Boxes ────────────────────────────── -->
<section id="feature" class="section-p1">
    <?php foreach ($featureBoxes as $fb): ?>
    <div class="fe-box">
        <img src="<?= htmlspecialchars($fb['image_path']) ?>" width="100px" alt="<?= htmlspecialchars($fb['title']) ?>">
        <h6><?= htmlspecialchars($fb['title']) ?></h6>
    </div>
    <?php endforeach; ?>
</section>

<!-- ─── Main Banner ──────────────────────────────── -->
<?php if ($mainBanner): ?>
<section id="banner" class="section-mi">
    <h4><?= htmlspecialchars($mainBanner['badge_text']) ?></h4>
    <h2><?= $mainBanner['heading'] /* intentionally unescaped — allows <span> set in admin */ ?></h2>
    <a href="<?= htmlspecialchars($mainBanner['button_url']) ?>">
        <button class="normal"><?= htmlspecialchars($mainBanner['button_text']) ?></button>
    </a>
</section>
<?php endif; ?>

<!-- ─── Small Banners ────────────────────────────── -->
<?php if ($smBanners): ?>
<section id="sm-banner" class="section-p1">
    <?php foreach ($smBanners as $sb): ?>
    <div class="banner-box <?= $sb['style_variant'] == 2 ? 'banner-box2' : '' ?>">
        <h4><?= htmlspecialchars($sb['badge_text']) ?></h4>
        <h2><?= htmlspecialchars($sb['heading']) ?></h2>
        <span><?= htmlspecialchars($sb['description']) ?></span>
        <a href="<?= htmlspecialchars($sb['button_url']) ?>">
            <button class="normal"><?= htmlspecialchars($sb['button_text']) ?></button>
        </a>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- ─── Seasonal Banners ─────────────────────────── -->
<?php if ($seasonal): ?>
<section id="banner3">
    <?php foreach ($seasonal as $s): ?>
    <div class="banner-box <?= htmlspecialchars($s['style_class']) ?>">
        <h2><?= htmlspecialchars($s['heading']) ?></h2>
        <h3><?= htmlspecialchars($s['subheading']) ?></h3>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- ─── Newsletter ───────────────────────────────── -->
<?php if ($newsletter): ?>
<section id="newsletter" class="section-p1 section-m1">
    <div class="newstext">
        <h4><?= htmlspecialchars($newsletter['badge_text']) ?></h4>
        <p><?= $newsletter['heading'] /* allows <span> */ ?></p>
        <div class="form">
            <input type="email" id="nl-email" placeholder="<?= htmlspecialchars($newsletter['placeholder_text']) ?>">
            <button class="normal" onclick="subscribeNewsletter()"><?= htmlspecialchars($newsletter['button_text']) ?></button>
        </div>
        <p id="nl-message" style="margin-top:12px;font-size:.9rem;display:none;"></p>
    </div>
</section>
<?php endif; ?>

<br><br>

<!-- ─── Footer ───────────────────────────────────── -->
<footer class="section-p1">
    <div class="col">
        <img class="logo" src="/shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
        <h4>Contact</h4>
        <p><span>Address</span>: kaguje Road, street 32, kampala</p>
        <p><strong>Phone:</strong> +256789340639</p>
        <p><strong>Hours:</strong> 10:00-18:00, Mon - Sat</p>
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
        <a href="About.php">About us</a>
        <a href="delivery.php">Delivery Information</a>
        <a href="privacy-policy.php">Privacy &amp; Policy</a>
        <a href="terms.php">Terms &amp; Conditions</a>
        <a href="contact.php">Contact us</a>
    </div>
    <div class="col">
        <h4>My Account</h4>
        <a href="login.php">Sign in</a>
        <a href="cart.php">View Cart</a>
        <a href="track-my-order.php">Track My order</a>
        <a href="help.php">Help</a>
    </div>
    <div class="col install">
        <h4>Install App</h4>
        <p>From App store or Google Play</p>
        <div class="row">
            <img src="shoes images/apps/download.jpeg" width="50px" alt="">
            <img src="shoes images/apps/images (4).jpeg" width="50px" alt="">
        </div>
        <p>Secured Payment Gateways</p>
        <img src="shoes images/payement/download (9).jpeg" width="50px" alt="">
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?>, Bibidong Tech Ug</p>
    </div>
</footer>

<script src="script.js"></script>
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

 // ── Newsletter AJAX ────────────────────────────────
 async function subscribeNewsletter() {
    const email = document.getElementById('nl-email').value.trim();
    const msg   = document.getElementById('nl-message');
    if (!email) { showMsg('Please enter your email address.', '#e74c3c'); return; }
    const fd = new FormData();
    fd.append('email', email);
    const res  = await fetch('api/subscribe.php', { method: 'POST', body: fd });
    const data = await res.json();
    showMsg(data.message, data.success ? '#088178' : '#e74c3c');
    if (data.success) document.getElementById('nl-email').value = '';
 }
 function showMsg(text, color) {
    const el = document.getElementById('nl-message');
    el.textContent = text;
    el.style.color = color;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
 }
</script>
</body>
</html>

<style>

:root {
  --primary:    #088178;
  --dark:       #04534e;
  --accent:     #e8f5f4;
  --bg:         #f9f9f9;
  --text:       #333333;
  --text-light: #777777;
  --white:      #ffffff;
  --border:     #e0e0e0;
  --shadow-sm:  0 2px 8px rgba(8, 129, 120, 0.10);
  --shadow-md:  0 6px 24px rgba(8, 129, 120, 0.14);
  --shadow-lg:  0 12px 40px rgba(8, 129, 120, 0.18);
  --radius:     12px;
  --transition: 0.3s ease;
}

*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.6;
}

/* ─── Header ──────────────────────────────────────── */
#header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 5%;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
  position: sticky;
  top: 0;
  z-index: 999;
}

#header > a {
  text-decoration: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex-shrink: 0;
}

#header > a span {
  font-size: 13px;
  font-weight: 700;
  color: #111;
  margin-top: 4px;
}

/* ─── Navbar Container ────────────────────────────── */
.navbar-container {
  display: flex;
  align-items: center;
}

/* Desktop nav */
.nav-menu {
  display: flex;
  align-items: center;
}

#navbar {
  list-style: none;
  display: flex;
  gap: 24px;
  align-items: center;
}

#navbar li a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  font-size: 14px;
  transition: color .2s;
}

#navbar li a:hover,
#navbar li a.active {
  color: #088178;
}

/* Close button — hidden on desktop */
#close {
  display: none;
}

/* ─── Mobile controls ─────────────────────────────── */
#mobile {
  display: none;
  align-items: center;
  gap: 16px;
  font-size: 20px;
}

#mobile a { color: #333; text-decoration: none; }
#bar { cursor: pointer; color: #333; font-size: 22px; }

/* ─── Overlay ─────────────────────────────────────── */
.nav-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 997;
}

.nav-overlay.active {
  display: block;
}

/* ═══ RESPONSIVE HAMBURGER ═══════════════════════════ */
@media (max-width: 768px) {
  /* Show hamburger + cart icon */
  #mobile {
    display: flex;
  }

  /* Slide-in drawer */
  .nav-menu {
    position: fixed;
    top: 0;
    right: -100%;
    width: 260px;
    height: 100vh;
    background: #fff;
    box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    flex-direction: column;
    align-items: flex-start;
    padding: 70px 24px 24px;
    transition: right 0.3s ease;
    z-index: 998;
    overflow-y: auto;
  }

  .nav-menu.active {
    right: 0;
  }

  /* Stack nav links vertically */
  #navbar {
    flex-direction: column;
    align-items: flex-start;
    gap: 0;
    width: 100%;
  }

  #navbar li {
    width: 100%;
    border-bottom: 1px solid #f0f0f0;
  }

  #navbar li a {
    display: block;
    padding: 14px 4px;
    font-size: 15px;
    font-weight: 600;
    color: #333;
  }

  #navbar li a:hover,
  #navbar li a.active {
    color: #088178;
  }

  /* Show close button inside drawer */
  #close {
    display: block;
    position: absolute;
    top: 18px;
    right: 20px;
    font-size: 22px;
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
  }

  #close:hover {
    color: #088178;
  }
}

/* ─── Section headings ───────────────────────────── */
section > h2 {
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 800;
  color: var(--text);
  text-align: center;
  padding: 40px 8% 20px;
  position: relative;
}

section > h2::after {
  content: '';
  display: block;
  width: 50px;
  height: 3px;
  background: var(--primary);
  border-radius: 2px;
  margin: 10px auto 0;
}

/* ─── Button ─────────────────────────────────────── */
button.normal,
.normal {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 28px;
  background: var(--primary);
  color: var(--white);
  font-size: 0.93rem;
  font-weight: 700;
  letter-spacing: 0.4px;
  border: none;
  border-radius: var(--radius);
  cursor: pointer;
  text-decoration: none;
  transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
}

button.normal:hover,
.normal:hover {
  background: var(--dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* ─── Hero ───────────────────────────────────────── */
#hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 30px;
  padding: 60px 8%;
  background: linear-gradient(135deg, var(--accent) 0%, #d4f0ee 40%, #f9f9f9 100%);
  min-height: 480px;
  position: relative;
  overflow: hidden;
}

#hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -80px;
  width: 320px; height: 320px;
  border-radius: 50%;
  background: rgba(8, 129, 120, 0.07);
  pointer-events: none;
}

#hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: rgba(8, 129, 120, 0.05);
  pointer-events: none;
}

#hero .main { flex: 1 1 300px; max-width: 520px; z-index: 1; }

#hero .main h4 {
  display: inline-block;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--primary);
  background: rgba(8, 129, 120, 0.12);
  padding: 5px 14px;
  border-radius: 20px;
  margin-bottom: 14px;
}

#hero .main h2 {
  font-size: clamp(1.5rem, 3.5vw, 2.4rem);
  font-weight: 700;
  color: var(--text);
  margin-bottom: 6px;
  line-height: 1.3;
}

#hero .main h1 {
  font-size: clamp(2rem, 5vw, 3.4rem);
  font-weight: 900;
  color: var(--dark);
  line-height: 1.1;
  margin-bottom: 14px;
  letter-spacing: -0.5px;
}

#hero .main p {
  font-size: 1rem;
  color: var(--text-light);
  margin-bottom: 28px;
}

#hero .main a button {
  padding: 15px 34px;
  font-size: 1rem;
  border-radius: var(--radius);
  background: var(--primary);
  color: var(--white);
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
}

#hero .main a button:hover {
  background: var(--dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

#hero .hero-image {
  flex: 1 1 260px;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1;
}

#hero .hero-image img {
  max-width: 420px;
  width: 100%;
  border-radius: 20px;
  object-fit: cover;
  filter: drop-shadow(0 12px 30px rgba(8, 129, 120, 0.2));
  animation: floatHero 4s ease-in-out infinite;
}

@keyframes floatHero {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-10px); }
}

/* ─── Tags ───────────────────────────────────────── */
.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  padding: 0 8% 30px;
}

.tag {
  padding: 8px 20px;
  background: var(--white);
  border: 2px solid var(--border);
  border-radius: 30px;
  font-size: 0.87rem;
  font-weight: 600;
  color: var(--text);
  cursor: pointer;
  transition: all var(--transition);
}

.tag:hover {
  background: var(--primary);
  border-color: var(--primary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}

/* ─── Viral Finds ────────────────────────────────── */
.viral { padding: 0 8% 50px; }

.viral-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 10px;
}

.viral-header h2 {
  font-size: clamp(1.2rem, 2.5vw, 1.7rem);
  font-weight: 800;
  color: var(--text);
  padding: 0;
  text-align: left;
}

.viral-header h2::after { display: none; }

.viral-header a {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary);
  text-decoration: none;
  border-bottom: 2px solid transparent;
  transition: border-color var(--transition);
}

.viral-header a:hover { border-color: var(--primary); }

.products {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
}

.product {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  border: 1.5px solid var(--border);
  box-shadow: var(--shadow-sm);
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
}

.product:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-md);
  border-color: var(--primary);
}

.product a { display: block; text-decoration: none; color: inherit; }

.product img {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.product:hover img { transform: scale(1.04); }

.product .title {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--text);
  padding: 10px 12px 4px;
  line-height: 1.4;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product .rating { font-size: 0.78rem; color: #f5a623; padding: 2px 12px; }
.product .price { font-size: 0.95rem; font-weight: 800; color: var(--primary); padding: 4px 12px 14px; }

/* ─── Grid / Cards ───────────────────────────────── */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 18px;
  padding: 0 8% 40px;
}

.card {
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--white);
  border: 1.5px solid var(--border);
  box-shadow: var(--shadow-sm);
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
  border-color: var(--primary);
}

.card a { display: block; text-decoration: none; color: inherit; }

.card .img-box { overflow: hidden; aspect-ratio: 1 / 1; }

.card .img-box img {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.card:hover .img-box img { transform: scale(1.06); }

.card .label {
  text-align: center;
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--text);
  padding: 10px 8px 12px;
  letter-spacing: 0.3px;
}

.card.highlight {
  border-color: var(--primary);
  position: relative;
}

.card.highlight::after {
  content: 'HOT';
  position: absolute;
  top: 10px; right: 10px;
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 1px;
  background: var(--primary);
  color: var(--white);
  padding: 3px 8px;
  border-radius: 20px;
}

/* ─── Features Strip ─────────────────────────────── */
#feature.section-p1 {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  padding: 50px 8%;
  background: var(--white);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}

.fe-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  flex: 1 1 120px;
  max-width: 150px;
  padding: 20px 12px;
  border-radius: var(--radius);
  background: var(--accent);
  border: 1.5px solid transparent;
  transition: all var(--transition);
  text-align: center;
}

.fe-box:hover {
  border-color: var(--primary);
  transform: translateY(-4px);
  box-shadow: var(--shadow-sm);
}

.fe-box img { width: 60px; height: 60px; object-fit: contain; border-radius: 8px; }
.fe-box h6 { font-size: 0.82rem; font-weight: 700; color: var(--dark); letter-spacing: 0.3px; }

/* ─── Main Banner ────────────────────────────────── */
#banner.section-mi {
  background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 60%, #0aada3 100%);
  padding: 70px 8%;
  text-align: center;
  position: relative;
  overflow: hidden;
}

#banner.section-mi::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 240px; height: 240px;
  border-radius: 50%;
  background: rgba(255,255,255,0.06);
  pointer-events: none;
}

#banner.section-mi h4 {
  font-size: 0.8rem; font-weight: 700; letter-spacing: 3px;
  text-transform: uppercase; color: rgba(255,255,255,0.8); margin-bottom: 10px;
}

#banner.section-mi h2 {
  font-size: clamp(1.6rem, 4vw, 2.8rem);
  font-weight: 800; color: var(--white); margin-bottom: 28px;
}

#banner.section-mi h2 span { color: #a8e6e2; }

#banner.section-mi button.normal {
  background: var(--white); color: var(--primary);
  font-weight: 800; padding: 14px 36px; font-size: 1rem;
}

#banner.section-mi button.normal:hover {
  background: var(--accent);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

/* ─── Small Banner Row ───────────────────────────── */
#sm-banner.section-p1 {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  padding: 40px 8%;
  background: var(--bg);
}

#sm-banner .banner-box {
  flex: 1 1 280px;
  background: linear-gradient(135deg, #e8f5f4 0%, #c8eeeb 100%);
  border-radius: var(--radius);
  padding: 36px 32px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  border: 1.5px solid rgba(8,129,120,0.15);
  position: relative;
  overflow: hidden;
  transition: transform var(--transition), box-shadow var(--transition);
}

#sm-banner .banner-box:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

#sm-banner .banner-box::after {
  content: '';
  position: absolute;
  bottom: -30px; right: -30px;
  width: 120px; height: 120px;
  border-radius: 50%;
  background: rgba(8,129,120,0.08);
}

#sm-banner .banner-box2 {
  background: linear-gradient(135deg, #fff8f0 0%, #fde8cc 100%);
  border-color: rgba(255,160,60,0.15);
}

#sm-banner .banner-box h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--primary); }
#sm-banner .banner-box2 h4 { color: #c97a10; }
#sm-banner .banner-box h2 { font-size: clamp(1.2rem, 2.5vw, 1.7rem); font-weight: 800; color: var(--text); line-height: 1.3; }
#sm-banner .banner-box span { font-size: 0.87rem; color: var(--text-light); }
#sm-banner .banner-box button.normal { margin-top: 8px; width: fit-content; padding: 10px 24px; font-size: 0.88rem; }
#sm-banner .banner-box2 button.normal { background: #c97a10; }
#sm-banner .banner-box2 button.normal:hover { background: #a0600b; }

/* ─── Banner3 ────────────────────────────────────── */
#banner3 {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  padding: 0 8% 40px;
}

#banner3 .banner-box {
  flex: 1 1 200px;
  border-radius: var(--radius);
  padding: 36px 28px;
  background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
  color: var(--white);
  position: relative;
  overflow: hidden;
  transition: transform var(--transition), box-shadow var(--transition);
}

#banner3 .banner-box:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }

#banner3 .banner-box::after {
  content: '';
  position: absolute;
  bottom: -40px; right: -40px;
  width: 140px; height: 140px;
  border-radius: 50%;
  background: rgba(255,255,255,0.07);
}

#banner3 .banner-box h2 { font-size: clamp(1.3rem, 2.5vw, 1.9rem); font-weight: 900; letter-spacing: 1px; margin-bottom: 6px; position: relative; z-index: 1; }
#banner3 .banner-box h3 { font-size: 0.88rem; font-weight: 500; color: rgba(255,255,255,0.8); position: relative; z-index: 1; }
#banner3 .banner-boxa { background: linear-gradient(135deg, #1a3c34 0%, #0d6b62 100%); }
#banner3 .banner-boxb { background: linear-gradient(135deg, #04534e 0%, #088178 50%, #0aada3 100%); }

/* ─── Newsletter ─────────────────────────────────── */
#newsletter {
  background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
  padding: 60px 8%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

#newsletter::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
  pointer-events: none;
}

#newsletter::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -40px;
  width: 280px; height: 280px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  pointer-events: none;
}

.newstext { text-align: center; max-width: 560px; position: relative; z-index: 1; }
.newstext h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.75); margin-bottom: 10px; }
.newstext p { font-size: clamp(1.1rem, 2.5vw, 1.6rem); font-weight: 700; color: var(--white); margin-bottom: 28px; line-height: 1.4; }
.newstext p span { color: #a8e6e2; font-style: italic; }

.newstext .form {
  display: flex;
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  max-width: 460px;
  margin: 0 auto;
}

.newstext .form input {
  flex: 1;
  padding: 14px 20px;
  border: none;
  outline: none;
  font-size: 0.95rem;
  font-family: inherit;
  color: var(--text);
  background: var(--white);
}

.newstext .form input::placeholder { color: #aaa; }

.newstext .form button.normal {
  border-radius: 0;
  padding: 14px 24px;
  font-size: 0.9rem;
  background: var(--dark);
  flex-shrink: 0;
  white-space: nowrap;
  border: none;
  transform: none;
}

.newstext .form button.normal:hover { background: #032e2b; transform: none; box-shadow: none; }

/* ─── Footer ─────────────────────────────────────── */
footer.section-p1 {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  padding: 50px 8% 30px;
  background: #1a1a2e;
  color: #ccc;
}

footer .col { flex: 1 1 160px; display: flex; flex-direction: column; gap: 10px; }
footer .col img.logo { margin-bottom: 8px; border-radius: 8px; }
footer .col h4 { font-size: 1rem; font-weight: 700; color: var(--white); margin-bottom: 6px; letter-spacing: 0.5px; }
footer .col p { font-size: 0.87rem; color: #aaa; line-height: 1.6; }
footer .col p strong { color: #ccc; }
footer .col a { font-size: 0.87rem; color: #aaa; text-decoration: none; transition: color var(--transition); width: fit-content; }
footer .col a:hover { color: var(--primary); }
footer .follow h4 { margin-top: 14px; }
footer .icon { display: flex; gap: 12px; margin-top: 6px; }
footer .icon i { font-size: 1.2rem; color: #aaa; cursor: pointer; transition: color var(--transition), transform var(--transition); }
footer .icon i:hover { color: var(--primary); transform: scale(1.2); }
footer .col.install .row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
footer .col.install .row img,
footer .col.install > img { border-radius: 6px; object-fit: cover; }
footer .copyright { width: 100%; text-align: center; padding-top: 22px; border-top: 1px solid #2e2e4a; font-size: 0.82rem; color: #666; }

/* ─── Responsive ─────────────────────────────────── */
@media (max-width: 900px) {
  #hero, .viral, .grid,
  #feature.section-p1,
  #sm-banner.section-p1,
  #banner.section-mi,
  #newsletter, #banner3,
  footer.section-p1, .tags, section > h2 {
    padding-left: 5%;
    padding-right: 5%;
  }
  footer.section-p1 { padding: 40px 5% 24px; }
}

@media (max-width: 640px) {
  #hero { flex-direction: column; text-align: center; padding: 40px 5%; }
  #hero .main h1 { font-size: 2rem; }
  #hero .hero-image img { max-width: 280px; }
  .products { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
  .grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
  #sm-banner.section-p1 { flex-direction: column; }
  #banner3 { flex-direction: column; }
  .newstext .form {
    flex-direction: column;
    border-radius: var(--radius);
    overflow: visible;
    box-shadow: none;
    gap: 10px;
  }
  .newstext .form input { border-radius: var(--radius); box-shadow: var(--shadow-sm); }
  .newstext .form button.normal { border-radius: var(--radius); width: 100%; }
  footer.section-p1 { padding: 36px 5% 20px; }
  footer .col { flex: 1 1 140px; }
  .fe-box { flex: 1 1 100px; }
}
</style>
