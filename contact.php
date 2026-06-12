<?php
// ── contact.php ─────────────────────────────────────
// Fully dynamic contact page — all content from DB
// ────────────────────────────────────────────────────
require_once __DIR__ . '/config/db.php';

// ── Fetch all settings into a simple array ──────────
$rows = $conn->query("SELECT setting_key, setting_value FROM contact_settings")->fetchAll(PDO::FETCH_ASSOC);
$s = [];
foreach ($rows as $row) {
    $s[$row['setting_key']] = $row['setting_value'];
}

// ── Fetch active staff ───────────────────────────────
$staff = $conn->query("SELECT * FROM contact_staff WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

// Helper: safely get setting
function setting($s, $key, $default = '') {
    return htmlspecialchars($s[$key] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - KISKEN TRENDS DUUKA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="contact.css">
    <!-- ✅ Google reCAPTCHA v3 — replace 6LfcTP0sAAAAAIo44vui62FIYhgQVfwZucM14L6x -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LfcTP0sAAAAAIo44vui62FIYhgQVfwZucM14L6x"></script>
</head>
<body>

    <!-- ══ HEADER ══ -->
    <section id="header">
        <a href="index.html" style="text-decoration: none; display: flex; flex-direction: column; align-items: center;">
            <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width: 50px; height: 50px;" alt="Company Logo">
            <span style="font-size: 14px; font-weight: bold; color: black; margin-top: 5px;">KISKEN TRENDS DUUKA</span>
        </a>
        <div class="navbar-container">
            <div class="nav-menu" id="nav-menu">
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="blogs.php">Blog</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a class="active" href="contact.php">Contact</a></li>
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

    <!-- ══ PAGE HEADER ══ -->
    <section id="page-header" class="about-header">
        <h2><?= setting($s, 'page_title', "#Let's_Talk") ?></h2>
        <p><?= setting($s, 'page_subtitle', 'LEAVE A MESSAGE, We love to hear from you') ?></p>
    </section>

    <!-- ══ CONTACT DETAILS ══ -->
    <section id="contact-details" class="section-p1 fade-in">
        <div class="details">
            <span>GET IN TOUCH</span>
            <h2>Visit our agency or contact us today</h2>
            <h3>Head Office</h3>
            <ul>
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <p><?= setting($s, 'address') ?></p>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <p><?= setting($s, 'phone1') ?><?= !empty($s['phone2']) ? ' | ' . htmlspecialchars($s['phone2']) : '' ?></p>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <p><?= setting($s, 'email') ?></p>
                </li>
                <li>
                    <i class="fas fa-clock"></i>
                    <p><?= setting($s, 'hours') ?></p>
                </li>
            </ul>
        </div>

        <div class="map">
            <iframe src="<?= htmlspecialchars($s['map_embed_url'] ?? '') ?>"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- ══ FORM + PEOPLE ══ -->
    <section id="form-details" class="fade-in">
        <form action="" id="contactForm">
            <span>LEAVE A MESSAGE</span>
            <h2>We Love to hear from you</h2>
            <input type="text" placeholder="Your Name" id="name" required>
            <input type="email" placeholder="E-mail" id="email" required>
            <input type="text" placeholder="Subject" id="subject">
            <textarea placeholder="Your Message" id="message" required></textarea>

            <button type="submit" class="normal">Submit Message <i class="fas fa-paper-plane"></i></button>
            <div id="formMessage" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: 10px;"></div>
        </form>

        <!-- ✅ Staff cards from database -->
        <div class="people">
            <?php if (empty($staff)): ?>
                <p style="color:#aaa;">No staff members added yet.</p>
            <?php else: ?>
                <?php foreach ($staff as $member): ?>
                <div>
                    <img src="<?= htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>">
                    <p>
                        <span><?= htmlspecialchars($member['name']) ?></span>
                        <?= htmlspecialchars($member['role']) ?><br>
                        Phone: <?= htmlspecialchars($member['phone']) ?><br>
                        E-mail: <?= htmlspecialchars($member['email']) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ══ NEWSLETTER ══ -->
    <section id="newsletter" class="fade-in">
        <div class="newstext">
            <h4>Stay Updated</h4>
            <p>Get exclusive updates about our latest collections and <span>special offers</span></p>
            <div class="form">
                <input type="email" placeholder="Your E-mail address" id="newsletterEmail">
                <button class="normal" id="subscribeBtn">Subscribe <i class="fas fa-bell"></i></button>
            </div>
        </div>
    </section>

    <!-- ══ FOOTER ══ -->
    <footer class="section-p1">
        <div class="col">
            <img class="logo" src="shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
            <h4>Contact</h4>
            <p><span>Address:</span> <?= setting($s, 'address') ?></p>
            <p><strong>Phone:</strong> <?= setting($s, 'phone1') ?></p>
            <p><strong>Hours:</strong> <?= setting($s, 'hours') ?></p>
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
            <a href="login.php">Sign in</a>
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

*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
}

/* ── Fade-in animation ────────────────────────────── */
.fade-in {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.65s ease, transform 0.65s ease;
}
.fade-in.visible { opacity: 1; transform: translateY(0); }

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
.navbar-container { display: flex; align-items: center; }

.nav-menu { display: flex; align-items: center; }

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

/* ── PAGE HEADER ─────────────────────────────────────── */
#page-header.about-header {
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 60%, #0aada3 100%);
    padding: 80px 40px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

#page-header.about-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

#page-header.about-header h2 {
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 800;
    color: var(--white);
    letter-spacing: 1px;
    margin-bottom: 12px;
    position: relative;
}

#page-header.about-header p {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.85);
    letter-spacing: 0.5px;
    position: relative;
}

/* ── CONTACT DETAILS ─────────────────────────────────── */
#contact-details {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    align-items: flex-start;
    padding: 70px 8%;
    background: var(--white);
}

#contact-details .details { flex: 1 1 300px; }

#contact-details .details > span {
    display: inline-block;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--primary);
    background: var(--accent);
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
}

#contact-details .details h2 {
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 700;
    color: var(--text);
    margin-bottom: 20px;
    line-height: 1.3;
}

#contact-details .details h3 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dark);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--accent);
}

#contact-details .details ul { list-style: none; display: flex; flex-direction: column; gap: 16px; }

#contact-details .details ul li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 18px;
    background: var(--accent);
    border-radius: var(--radius);
    border-left: 4px solid var(--primary);
    transition: var(--transition);
}

#contact-details .details ul li:hover { transform: translateX(5px); box-shadow: var(--shadow-sm); }
#contact-details .details ul li i { color: var(--primary); font-size: 1.1rem; margin-top: 3px; flex-shrink: 0; }
#contact-details .details ul li p { font-size: 0.93rem; color: var(--text); line-height: 1.5; }

#contact-details .map {
    flex: 1 1 380px;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 3px solid var(--accent);
    min-height: 320px;
}

#contact-details .map iframe {
    width: 100%;
    height: 100%;
    min-height: 320px;
    border: none;
    display: block;
}

/* ── FORM + PEOPLE ───────────────────────────────────── */
#form-details {
    display: flex;
    flex-wrap: wrap;
    gap: 50px;
    padding: 70px 8%;
    background: var(--bg);
}

#form-details form {
    flex: 1 1 340px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

#form-details form > span {
    display: inline-block;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--primary);
    background: var(--accent);
    padding: 5px 14px;
    border-radius: 20px;
    width: fit-content;
}

#form-details form h2 {
    font-size: clamp(1.4rem, 2.5vw, 1.9rem);
    font-weight: 700;
    color: var(--text);
    line-height: 1.3;
    margin-bottom: 4px;
}

#form-details form input,
#form-details form textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.95rem;
    font-family: inherit;
    color: var(--text);
    background: var(--white);
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}

#form-details form input:focus,
#form-details form textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(8,129,120,0.10);
}

#form-details form input::placeholder,
#form-details form textarea::placeholder { color: #aaa; }

#form-details form textarea { min-height: 140px; resize: vertical; }

button.normal {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 30px;
    background: var(--primary);
    color: var(--white);
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
    width: fit-content;
}

button.normal:hover:not(:disabled) {
    background: var(--dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

button.normal:disabled { opacity: 0.7; cursor: not-allowed; }

#form-details .people {
    flex: 1 1 300px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

#form-details .people > div {
    display: flex;
    align-items: center;
    gap: 18px;
    background: var(--white);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    border: 1.5px solid var(--border);
    transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
}

#form-details .people > div:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
}

#form-details .people > div img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 3px solid var(--accent);
    box-shadow: 0 0 0 2px var(--primary);
}

#form-details .people > div p { font-size: 0.88rem; color: var(--text-light); line-height: 1.65; }
#form-details .people > div p span { display: block; font-size: 0.98rem; font-weight: 700; color: var(--text); margin-bottom: 2px; }

/* ── NEWSLETTER ──────────────────────────────────────── */
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
.newstext p  { font-size: clamp(1.2rem, 2.5vw, 1.7rem); font-weight: 700; color: var(--white); margin-bottom: 28px; line-height: 1.4; }
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
    transform: none;
}

.newstext .form button.normal:hover { background: #032e2b; transform: none; box-shadow: none; }

/* ── FOOTER ──────────────────────────────────────────── */
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
footer .col h4 { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 6px; letter-spacing: 0.5px; }
footer .col p  { font-size: 0.87rem; color: #aaa; line-height: 1.6; }
footer .col p strong { color: #ccc; }
footer .col a  { font-size: 0.87rem; color: #aaa; text-decoration: none; transition: color var(--transition); width: fit-content; }
footer .col a:hover { color: var(--primary); }
footer .follow h4 { margin-top: 14px; }
footer .icon  { display: flex; gap: 12px; margin-top: 6px; }
footer .icon i { font-size: 1.2rem; color: #aaa; cursor: pointer; transition: color var(--transition), transform var(--transition); }
footer .icon i:hover { color: var(--primary); transform: scale(1.2); }
footer .col.install .row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
footer .col.install .row img,
footer .col.install > img { border-radius: 6px; object-fit: cover; }
footer .copyright { width: 100%; text-align: center; padding-top: 22px; border-top: 1px solid #2e2e4a; font-size: 0.82rem; color: #666; }

.section-p1 { padding: 40px 5%; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 900px) {
    #contact-details,
    #form-details,
    #newsletter,
    footer.section-p1 {
        padding-left: 5%;
        padding-right: 5%;
    }
    footer.section-p1 { padding: 40px 5% 24px; }
}

@media (max-width: 640px) {
    #page-header.about-header { padding: 60px 24px 44px; }

    #contact-details,
    #form-details { padding: 40px 5%; gap: 28px; }

    #contact-details .map { min-height: 240px; }
    #contact-details .map iframe { min-height: 240px; }

    .newstext .form {
        flex-direction: column;
        overflow: visible;
        box-shadow: none;
        gap: 10px;
    }
    .newstext .form input  { border-radius: var(--radius); box-shadow: var(--shadow-sm); }
    .newstext .form button.normal { border-radius: var(--radius); width: 100%; justify-content: center; }

    #form-details .people > div { flex-direction: column; text-align: center; }

    footer.section-p1 { padding: 36px 5% 20px; }
    footer .col { flex: 1 1 140px; }

    /* ── reCAPTCHA v3 badge repositioning (optional) ── */
    .grecaptcha-badge { bottom: 70px !important; }
}
</style>

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
        document.querySelectorAll('#navbar li a').forEach(link => link.addEventListener('click', closeMenu));
        window.addEventListener('resize', () => { if (window.innerWidth > 768) closeMenu(); });
        document.getElementById('year').textContent = new Date().getFullYear();

        // ── Fade-in on scroll ──
        const fadeEls = document.querySelectorAll('.fade-in');
        const checkFade = () => {
            fadeEls.forEach(el => {
                if (el.getBoundingClientRect().top < window.innerHeight - 80)
                    el.classList.add('visible');
            });
        };
        checkFade();
        window.addEventListener('scroll', checkFade);

        // ── Contact Form ──
        const contactForm = document.getElementById('contactForm');
        const formMessage = document.getElementById('formMessage');

        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name    = document.getElementById('name').value.trim();
                const email   = document.getElementById('email').value.trim();
                const subject = document.getElementById('subject').value.trim();
                const message = document.getElementById('message').value.trim();

                if (!name || !email || !message) { showFormMessage('Please fill in all required fields.', 'error'); return; }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showFormMessage('Please enter a valid email address.', 'error'); return; }

                const btn = contactForm.querySelector('button[type="submit"]');
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                btn.disabled = true;

                try {
                    // ✅ v3: get token invisibly, no user interaction needed
                    const token = await grecaptcha.execute('6LfcTP0sAAAAAIo44vui62FIYhgQVfwZucM14L6x', { action: 'contact_form' });

                    const formData = new FormData();
                    formData.append('name', name);
                    formData.append('email', email);
                    formData.append('subject', subject);
                    formData.append('message', message);
                    formData.append('g-recaptcha-response', token); // ✅ v3 token

                    const res  = await fetch('contact_submit.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    showFormMessage(data.message, data.success ? 'success' : 'error');
                    if (data.success) contactForm.reset();
                } catch (err) {
                    showFormMessage('Something went wrong. Please try again.', 'error');
                } finally {
                    btn.innerHTML = orig;
                    btn.disabled = false;
                }
            });
        }

        function showFormMessage(text, type) {
            formMessage.textContent = text;
            formMessage.style.display = 'block';
            formMessage.style.background = type === 'success' ? '#088178' : '#ef4444';
            formMessage.style.color = '#fff';
            setTimeout(() => { formMessage.style.display = 'none'; }, 5000);
        }

        // ── Newsletter ──
        document.getElementById('subscribeBtn').addEventListener('click', async () => {
            const email = document.getElementById('newsletterEmail').value.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert('Please enter a valid email address.'); return; }

            const btn = document.getElementById('subscribeBtn');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('email', email);
                const res  = await fetch('newsletter_subscribe.php', { method: 'POST', body: formData });
                const data = await res.json();
                alert(data.message);
                if (data.success) document.getElementById('newsletterEmail').value = '';
            } catch (err) {
                alert('Something went wrong. Please try again.');
            } finally {
                btn.innerHTML = orig;
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>