<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisken Trends Duuka | Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ══ HEADER ══ -->
<section id="header">
        <a href="index.php" style="text-decoration: none; display: flex; flex-direction: column; align-items: center;">
            <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width: 50px; height: 50px;" alt="Company Logo">
            <span style="font-size: 14px; font-weight: bold; color: black; margin-top: 5px;">KISKEN TRENDS DUUKA</span>
        </a>

        <div class="navbar-container">
            <div class="nav-menu" id="nav-menu">
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
                    <li><a class="active" href="shop.php">Shop</a></li>
                    <li><a href="blogs.php">Blog</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
                    <li><a href="login.php"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                </ul>
                <a href="#" id="close" onclick="closeMenu()"><i class="fas fa-times"></i></a>
            </div>

            <!-- Overlay -->
            <div class="nav-overlay" id="nav-overlay" onclick="closeMenu()"></div>

            <div id="mobile">
                <a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
                <i id="bar" class="fas fa-bars" onclick="openMenu()"></i>
            </div>
        </div>
</section>

    <!-- ══ PAGE HEADER ══ -->
<section id="page-header">
        <h2>#stayhome</h2>
        <p>Save more with coupons & up to 70% off</p>
</section>

    <!-- ══ PRODUCTS ══ -->
<section id="product" class="section-p1">
        <h2>Featured Products</h2>
        <p>Explore our latest shoe collection</p>

        <div class="product-controls">
            <div class="category-filter">
                <a href="shop.php" style="text-decoration: none;">
                    <button class="category-btn" data-filter="all">All Shoes</button>
                </a>
                <a href="men.php" style="text-decoration: none;">
                    <button class="category-btn" data-filter="men">MEN</button>
                </a>
                <a href="women.php" style="text-decoration: none;">
                    <button class="category-btn" data-filter="women">WOMEN</button>
                </a>
                <a href="kids.php" style="text-decoration: none;">
                    <button class="category-btn" data-filter="kids">KIDS</button>
                </a>
            </div>
        </div>

<div class="pro-container" data-page="1"></div>    

</section>


    <!-- ══ PAGINATION ══ -->
<section id="pagination" class="section-p1">
        <a href="#">1</a>
        <a href="more-shoes.php">2</a>
</section>

    <!-- ══ NEWSLETTER ══ -->
<section id="newsletter" class="section-p1 section-m1">
        <div class="newstext">
            <h4>Sign Up for Newsletter</h4>
            <p>Get E-mail updates about our latest shop and <span>special offers</span></p>
            <div class="form">
                <input type="text" placeholder="Your E-mail address">
                <button class="normal">Sign up</button>
            </div>
        </div>
</section>

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

<script src="script.js"></script>
<script>
        // ── Hamburger menu (matches index.php exactly) ──
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

        // ── Active category button based on current page ──
        const buttons     = document.querySelectorAll(".category-btn");
        const currentPage = window.location.pathname.split("/").pop();

        buttons.forEach(button => {
            const link = button.closest('a')?.getAttribute("href");
            button.classList.remove("active");
            if (link === currentPage || (currentPage === "" && link === "shop.php")) {
                button.classList.add("active");
            }
        });

        // ── Auto year in footer ──
        document.getElementById('year').textContent = new Date().getFullYear();
</script>

</body>

</html>

<style>

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f9f9f9;
    color: #333;
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

/* Desktop nav wrapper */
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

/* Close button — hidden on desktop */
#close {
    display: none;
}

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

    /* Slide-in drawer from the right — same as index.php */
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

    /* Show close (✕) inside drawer */
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
#page-header {
    background: linear-gradient(135deg, #088178 0%, #04534e 100%);
    color: #fff;
    text-align: center;
    padding: 50px 5%;
}

#page-header h2 { font-size: 28px; letter-spacing: 1px; }
#page-header p  { margin-top: 8px; opacity: .85; font-size: 15px; }

/* ── PRODUCT SECTION ─────────────────────────────────── */
#product { padding: 40px 5%; }

#product h2 {
    text-align: center;
    font-size: 24px;
    color: #088178;
    text-decoration: underline;
    text-underline-offset: 10px;
    margin-bottom: 8px;
}

#product > p {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
    font-size: 14px;
}

/* ── CATEGORY FILTER BUTTONS ─────────────────────────── */
.product-controls {
    display: flex;
    justify-content: center;
    margin-bottom: 32px;
}

.category-filter {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
}

.category-btn {
    padding: 9px 22px;
    border: 1.5px solid #088178;
    border-radius: 30px;
    background: #fff;
    color: #088178;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    letter-spacing: .4px;
}

.category-btn:hover,
.category-btn.active {
    background: #088178;
    color: #fff;
}

/* ── PRODUCT GRID ────────────────────────────────────── */
.pro-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
}

/* ── PRODUCT CARD ────────────────────────────────────── */
.pro {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    transition: transform .2s, box-shadow .2s;
    position: relative;
    display: flex;
    flex-direction: column;
}

.pro:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(8,129,120,.15);
}

.pro img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
    transition: transform .3s;
}

.pro:hover img { transform: scale(1.04); }

.des { padding: 14px 16px 10px; flex: 1; }

.des span {
    display: block;
    font-size: 11px;
    color: #aaa;
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 4px;
}

.des h5 {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.des .star { display: flex; gap: 3px; margin-bottom: 8px; }
.des .star i { font-size: 12px; color: #f4c430; }
.des h4 { font-size: 15px; color: #088178; font-weight: 700; }

/* ── PRODUCT ACTIONS ────────────────────────────────── */
.product-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px 14px;
    border-top: 1px solid #f0f0f0;
    gap: 8px;
}

.product-actions a.cart-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: #e8f5f4;
    border-radius: 50%;
    color: #088178;
    font-size: 14px;
    text-decoration: none;
    transition: background .2s, color .2s;
    flex-shrink: 0;
}

.product-actions a.cart-link:hover { background: #088178; color: #fff; }

.product-actions a.view-details-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: #088178;
    color: #fff;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s;
    white-space: nowrap;
}

.product-actions a.view-details-btn:hover { background: #04534e; }

/* ── PAGINATION ──────────────────────────────────────── */
#pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 20px 5% 40px;
}

#pagination a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1.5px solid #088178;
    border-radius: 6px;
    color: #088178;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all .2s;
}

#pagination a:hover,
#pagination a.active { background: #088178; color: #fff; }

/* ── NEWSLETTER ──────────────────────────────────────── */
#newsletter {
    background: linear-gradient(135deg, #088178 0%, #04534e 100%);
    padding: 50px 5%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.newstext { text-align: center; max-width: 560px; }
.newstext h4 { font-size: 22px; color: #fff; margin-bottom: 10px; }
.newstext p  { font-size: 14px; color: rgba(255,255,255,.9); margin-bottom: 22px; }
.newstext p span { font-weight: 700; text-decoration: underline; }

.newstext .form {
    display: flex;
    justify-content: center;
    max-width: 460px;
    margin: 0 auto;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
}

.newstext .form input {
    flex: 1;
    padding: 13px 18px;
    border: none;
    font-size: 14px;
    outline: none;
    color: #333;
}

.newstext .form button.normal {
    padding: 13px 24px;
    background: #04534e;
    color: #fff;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
    white-space: nowrap;
}

.newstext .form button.normal:hover { background: #032e2b; }

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

/* ── SECTION HELPERS ─────────────────────────────────── */
.section-p1 { padding: 40px 5%; }
.section-m1 { margin: 40px 0; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 640px) {
    .pro-container { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .pro img { height: 160px; }
    .category-filter { gap: 8px; }
    .category-btn { padding: 7px 16px; font-size: 12px; }

    .newstext .form {
        flex-direction: column;
        overflow: visible;
        box-shadow: none;
        gap: 10px;
    }
    .newstext .form input  { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .newstext .form button.normal { border-radius: 8px; width: 100%; }

    footer.section-p1 { padding: 36px 5% 20px; }
    footer .col { flex: 1 1 140px; }
}

@media (max-width: 380px) {
    .pro-container { grid-template-columns: 1fr; }
}
</style>