<?php
// ══════════════════════════════════════════════════════
//  about.php — Kisken Trends Duuka | Dynamic About Page
// ══════════════════════════════════════════════════════
require_once(__DIR__ . '/config/db.php');
// ── Fetch all sections ──────────────────────────────────
$hero   = $conn->query("SELECT * FROM about_hero   ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$story  = $conn->query("SELECT * FROM about_story  ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$values = $conn->query("SELECT * FROM about_values WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$team   = $conn->query("SELECT * FROM about_team   WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$stats  = $conn->query("SELECT * FROM about_stats  WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f9f9f9;
    color: #333;
    line-height: 1.7;
}

/* ── HEADER ─────────────────────────────────────────── */
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

/* ── Navbar container ──────────────────────────────── */
.navbar-container {
    display: flex;
    align-items: center;
}

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
#navbar li a.active { color: #088178; }

#close { display: none; }

/* ── Mobile controls ──────────────────────────────── */
#mobile {
    display: none;
    align-items: center;
    gap: 16px;
    font-size: 20px;
}

#mobile a { color: #333; text-decoration: none; }
#bar { cursor: pointer; color: #333; font-size: 22px; }

/* ── Overlay ──────────────────────────────────────── */
.nav-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 997;
}

.nav-overlay.active { display: block; }

/* ═══ RESPONSIVE HAMBURGER ═══════════════════════════ */
@media (max-width: 768px) {
    #mobile { display: flex; }

    .nav-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 260px;
        height: 100vh;
        background: #fff;
        box-shadow: -4px 0 20px rgba(0,0,0,.15);
        flex-direction: column;
        align-items: flex-start;
        padding: 70px 24px 24px;
        transition: right 0.3s ease;
        z-index: 998;
        overflow-y: auto;
    }

    .nav-menu.active { right: 0; }

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
    #navbar li a.active { color: #088178; }

    #close {
        display: block;
        position: absolute;
        top: 18px;
        right: 20px;
        font-size: 22px;
        color: #333;
        text-decoration: none;
        transition: color .2s;
    }

    #close:hover { color: #088178; }
}

/* ── HERO ────────────────────────────────────────────── */
.hero {
    background: linear-gradient(135deg, #088178 0%, #04534e 100%);
    color: #fff;
    text-align: center;
    padding: 70px 5%;
}

.hero h1 { font-size: 34px; font-weight: 700; letter-spacing: .5px; margin-bottom: 14px; }
.hero p  { font-size: 16px; opacity: .88; max-width: 620px; margin: 0 auto; }

/* ── LAYOUT ──────────────────────────────────────────── */
.container {
    width: 90%;
    max-width: 1100px;
    margin: 0 auto;
}

.section { padding: 60px 0; }

.section-title {
    font-size: 22px;
    color: #088178;
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 8px;
    margin-bottom: 32px;
    text-align: center;
}

/* ── ABOUT CONTENT ───────────────────────────────────── */
.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: center;
}

.about-text h2 { font-size: 24px; color: #222; margin-bottom: 18px; font-weight: 700; }
.about-text p  { color: #666; font-size: 15px; margin-bottom: 14px; }

.about-image img {
    width: 100%;
    border-radius: 14px;
    object-fit: cover;
    max-height: 380px;
    box-shadow: 0 8px 28px rgba(8,129,120,.14);
    display: block;
}

/* ── VALUES ──────────────────────────────────────────── */
.values { background: #fff; padding: 60px 5%; }

.values-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 28px;
}

.value-card {
    background: #f9f9f9;
    border-radius: 12px;
    padding: 32px 24px;
    text-align: center;
    border: 1.5px solid #e8f5f4;
    transition: transform .2s, box-shadow .2s;
}

.value-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 28px rgba(8,129,120,.12);
}

.value-card i    { font-size: 38px; color: #088178; margin-bottom: 16px; display: block; }
.value-card h3   { font-size: 17px; color: #222; font-weight: 700; margin-bottom: 10px; }
.value-card p    { font-size: 14px; color: #777; line-height: 1.6; }

/* ── TEAM ────────────────────────────────────────────── */
.team-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 28px;
}

.team-member {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    text-align: center;
    transition: transform .2s, box-shadow .2s;
}

.team-member:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 28px rgba(8,129,120,.14);
}

.member-img { overflow: hidden; height: 220px; }

/* ── TEAM MEMBER IMAGE FIX ── */
.member-img {
    overflow: hidden;
    height: 260px;          /* taller = more face visible */
    background: #f0f0f0;    /* placeholder color while loading */
}

.member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 20%;   /* slightly above center to catch faces */
    transition: transform .35s;
    display: block;
    image-orientation: from-image; /* ← fixes EXIF/rotation issues */
}
.team-member:hover .member-img img { transform: scale(1.06); }

.member-info { padding: 18px 16px 22px; }
.member-info h3 { font-size: 16px; font-weight: 700; color: #222; margin-bottom: 5px; }
.member-info p  { font-size: 13px; color: #088178; font-weight: 600; }

/* ── STATS ───────────────────────────────────────────── */
.stats {
    background: linear-gradient(135deg, #088178 0%, #04534e 100%);
    padding: 60px 5%;
    color: #fff;
    text-align: center;
}

.stats .section-title { color: #fff; text-decoration-color: rgba(255,255,255,.5); }

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 28px;
    margin-top: 10px;
}

.stat-item {
    background: rgba(255,255,255,.10);
    border-radius: 12px;
    padding: 28px 20px;
    border: 1px solid rgba(255,255,255,.18);
    transition: background .2s;
}

.stat-item:hover { background: rgba(255,255,255,.18); }
.stat-item h2  { font-size: 38px; font-weight: 800; color: #fff; margin-bottom: 6px; letter-spacing: -1px; }
.stat-item p   { font-size: 14px; opacity: .85; font-weight: 500; }

/* ── CTA ─────────────────────────────────────────────── */
.cta {
    background: #fff;
    border-radius: 14px;
    margin: 0 0 60px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
}

.cta h2 { font-size: 26px; color: #222; font-weight: 700; margin-bottom: 14px; }
.cta p  { font-size: 15px; color: #666; max-width: 560px; margin: 0 auto 28px; }

.btn {
    display: inline-block;
    padding: 13px 32px;
    background: linear-gradient(135deg, #088178, #04534e);
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 700;
    transition: opacity .2s;
    border: none;
    cursor: pointer;
}

.btn:hover { opacity: .87; }

/* ── FOOTER ──────────────────────────────────────────── */
footer.section-p1 {
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
    padding: 50px 5% 30px;
    background: #1a1a2e;
    color: #ccc;
}

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
footer .col.install .row img,
footer .col.install > img { border-radius: 6px; object-fit: cover; }
footer .copyright { width: 100%; text-align: center; padding-top: 22px; border-top: 1px solid #2e2e4a; font-size: 0.82rem; color: #666; }

.section-p1 { padding: 40px 5%; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 640px) {
    .hero { padding: 50px 5%; }
    .hero h1 { font-size: 24px; }
    .hero p  { font-size: 14px; }

    .about-content { grid-template-columns: 1fr; gap: 28px; }
    .about-image   { order: -1; }

    .member-img { height: 180px; }

    .stat-item h2 { font-size: 30px; }
    .stats-container { grid-template-columns: repeat(2, 1fr); }

    .values-container { grid-template-columns: 1fr; }

    .cta { padding: 40px 24px; }
    .cta h2 { font-size: 22px; }

    footer.section-p1 { padding: 36px 5% 20px; }
    footer .col { flex: 1 1 140px; }
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
                    <li><a href="blogs.php">Blog</a></li>
                    <li><a class="active" href="about.php">About</a></li>
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
            <h1><?= e($hero['title']) ?></h1>
            <p><?= e($hero['subtitle']) ?></p>
        </div>
    </section>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="container">

        <!-- About Section -->
        <section class="section">
            <h2 class="section-title">About Kisken Trends Duuka</h2>
            <div class="about-content">
                <div class="about-text">
                    <h2><?= e($story['heading']) ?></h2>
                    <?php if (!empty($story['paragraph1'])): ?>
                        <p><?= e($story['paragraph1']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($story['paragraph2'])): ?>
                        <p><?= e($story['paragraph2']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($story['paragraph3'])): ?>
                        <p><?= e($story['paragraph3']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="about-image">
                    <img src="<?= e($story['image_url']) ?>" alt="<?= e($story['image_alt']) ?>">
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <?php if (!empty($values)): ?>
        <section class="values">
            <div class="container">
                <h2 class="section-title">Our Values</h2>
                <div class="values-container">
                    <?php foreach ($values as $v): ?>
                    <div class="value-card">
                        <i class="<?= e($v['icon_class']) ?>"></i>
                        <h3><?= e($v['title']) ?></h3>
                        <p><?= e($v['description']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Team Section -->
        <?php if (!empty($team)): ?>
        <section class="section">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="team-container">
                <?php foreach ($team as $m): ?>
                <div class="team-member">
                    <div class="member-img">
                        <img src="<?= e($m['photo_url']) ?>" alt="<?= e($m['full_name']) ?>">
                    </div>
                    <div class="member-info">
                        <h3><?= e($m['full_name']) ?></h3>
                        <p><?= e($m['role']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Stats Section -->
        <?php if (!empty($stats)): ?>
        <section class="stats">
            <div class="container">
                <h2 class="section-title">Kisken Trends Duuka By The Numbers</h2>
                <div class="stats-container">
                    <?php foreach ($stats as $i => $s): ?>
                    <div class="stat-item">
                        <h2 id="stat-<?= $i ?>" data-target="<?= (int)$s['value'] ?>">0</h2>
                        <p><?= e($s['label']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2>Step Into Your Next Adventure</h2>
                <p>Discover our collection of premium footwear designed for every step of your journey. From casual sneakers to rugged hiking boots, we have the perfect pair for you.</p>
                <a href="shop.php" class="btn">Explore Our Collection</a>
            </div>
        </section>

    </main>

    <!-- ══ FOOTER ══ -->
    <footer class="section-p1">
        <div class="col">
            <img class="logo" src="/shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
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
            <a href="delivery.html">Delivery Information</a>
            <a href="privacy-policy.html">Privacy &amp; Policy</a>
            <a href="terms.html">Terms &amp; Conditions</a>
            <a href="contact.php">Contact us</a>
        </div>
        <div class="col">
            <h4>My Account</h4>
            <a href="login.html">Sign in</a>
            <a href="cart.php">View Cart</a>
            <a href="track-my-order.html">Track My Order</a>
            <a href="help.html">Help</a>
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
        document.querySelectorAll('#navbar li a').forEach(l => l.addEventListener('click', closeMenu));
        window.addEventListener('resize', () => { if (window.innerWidth > 768) closeMenu(); });

        document.getElementById('year').textContent = new Date().getFullYear();

        // Animated counters — reads data-target set by PHP
        function animateCounter(el, end, duration) {
            let startTime = null;
            const step = ts => {
                if (!startTime) startTime = ts;
                const progress = Math.min((ts - startTime) / duration, 1);
                el.textContent = Math.floor(progress * end).toLocaleString();
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }

        let countersAnimated = false;
        function checkCounters() {
            if (countersAnimated) return;
            const statsSection = document.querySelector('.stats');
            if (!statsSection) return;
            const rect = statsSection.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                document.querySelectorAll('.stat-item h2[data-target]').forEach(el => {
                    animateCounter(el, parseInt(el.dataset.target), 2000);
                });
                countersAnimated = true;
            }
        }
        window.addEventListener('scroll', checkCounters);
        checkCounters();

        // Value card scroll animations
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.value-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>