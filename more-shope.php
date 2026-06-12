<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <section id="header" >
        <a href="#"style="text-decoration: none; display: flex; flex-direction: column; align-items: center;">
            <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width: 50px; height: 50px;"
             alt="Company Logo">
        <span style="font-size: 14px; font-weight: bold; color: black; margin-top: 5px;"
        >KISKEN TRENDS DUUKA</span></a>

        <div>
            <ul id="navbar">
                <li> <a  href="index.php">Home</a></li>
                <li> <a class="active" href="Shop.php">shop</a></li>
                <li> <a href="blogs.php">Blog</a></li>
                <li> <a href="About.php">About</a></li>
                <li> <a href="contact.php">Contact</a></li>
                <li id="lg-bag"><a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></li>
                 <li> <a href="login.php"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                                     <li><a href="orders.php">My Orders</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>


            </ul>
        <a href="#" id="close"><i class="fas fa-times"></i></a>

            </div>
            <div id="mobile">
            <a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
            </div>
        </div>
    </section>



    <section id="product" class="section-p1">
         <h2>Featured Products</h2>
    <p>Explore our latest  shoe collection</p>

<div class="product-controls">
    <div class="category-filter">
        
        <a href="Shop.php" style="text-decoration: none;">
            <button class="category-btn active" data-filter="all">
                All Shoes
            </button>
        </a>
        
        
        <a href="men.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="men">
                MEN
            </button>
        </a>
        
        
        <a href="women.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="women">
                WOMEN
            </button>
        </a>
        
        <!-- Kids button with both filter and link -->
        <a href="kids.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="kids">
                KIDS
            </button>
        </a>
    </div>
</div> 
<br>
<div class="pro-container" data-page="2"></div>    
</div>

    <!-- View More Button -->
    <div class="btn-container">
        <a href="Shop.php" class="view-more-btn">back</a>
    </div>
</section>

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
            <a href="about.html">About us</a>
            <a href="delivery.html">Delivery Information</a>
            <a href="privacy-policy.html">Privacy &amp; Policy</a>
            <a href="terms.html">Terms &amp; Conditions</a>
            <a href="contact.html">Contact us</a>
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
    <script src="script.js"></script>

<script>
    const buttons = document.querySelectorAll(".category-btn");
    const currentPage = window.location.pathname.split("/").pop();

    buttons.forEach(button => {
        const link = button.parentElement.getAttribute("href");

        // Remove active from all
        button.classList.remove("active");

        // Add active if page matches
        if (link === currentPage) {
            button.classList.add("active");
        }
    });
</script>

</body>
</html>
<style>
    /* ═══════════════════════════════════════════════════════
   shop.css  —  matches cart.php design + script.js cards
   Primary:  #088178  |  Dark: #04534e
   Accent:   #e8f5f4  |  Hover: #f6fffe
   Body bg:  #f9f9f9  |  Text: #333
════════════════════════════════════════════════════════ */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

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
    box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    position: sticky;
    top: 0;
    z-index: 999;
}

#header a {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
}

#header a span {
    font-size: 13px;
    font-weight: 700;
    color: #111;
    margin-top: 4px;
}

#navbar {
    list-style: none;
    display: flex;
    gap: 24px;
    align-items: center;
}

#navbar li a,
#navbar > a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    font-size: 14px;
    transition: color .2s;
}

#navbar li a:hover,
#navbar > a:hover,
#navbar li a.active {
    color: #088178;
}

#mobile {
    display: none;
    align-items: center;
    gap: 16px;
    font-size: 20px;
}

#mobile a { color: #333; }
#bar { cursor: pointer; color: #333; font-size: 22px; }
#close { display: none; cursor: pointer; color: #333; font-size: 22px; }

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

/* ── PRODUCT CARD (.pro) ─────────────────────────────── */
/* This matches exactly what script.js renders:
   .pro > img
   .pro > .des > span, h5, .star, h4
   .pro > .product-actions > a.cart-link, a.view-details-btn
*/
/* ------button----- */

/* ─── Back / View-more Button ────────────────────────── */
.btn-container {
  display: flex;
  justify-content: center;
  padding: 30px 0;
}

.view-more-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 32px;
  background: transparent;
  color: #088178;
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-decoration: none;
  border: 2px solid #088178;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.3s ease, color 0.3s ease,
              transform 0.3s ease, box-shadow 0.3s ease;
}

.view-more-btn:hover {
  background: #088178;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(8, 129, 120, 0.25);
}

.view-more-btn:active {
  transform: translateY(0);
  box-shadow: none;
}
.pro {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
    transition: transform .2s, box-shadow .2s;
    position: relative;
    display: flex;
    flex-direction: column;
}

.pro:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(8, 129, 120, .15);
}

/* product image */
.pro img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
    transition: transform .3s;
}

.pro:hover img {
    transform: scale(1.04);
}

/* description block */
.des {
    padding: 14px 16px 10px;
    flex: 1;
}

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

/* star rating */
.des .star {
    display: flex;
    gap: 3px;
    margin-bottom: 8px;
}

.des .star i {
    font-size: 12px;
    color: #f4c430;
}

/* price */
.des h4 {
    font-size: 15px;
    color: #088178;
    font-weight: 700;
}

/* ── PRODUCT ACTIONS (cart + view) ───────────────────── */
.product-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px 14px;
    border-top: 1px solid #f0f0f0;
    gap: 8px;
}

/* cart icon button */
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

.product-actions a.cart-link:hover {
    background: #088178;
    color: #fff;
}

/* view details button */
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

.product-actions a.view-details-btn:hover {
    background: #04534e;
}

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
#pagination a.active {
    background: #088178;
    color: #fff;
}

/* ── NEWSLETTER ──────────────────────────────────────── */
#newsletter {
    background: linear-gradient(135deg, #088178 0%, #04534e 100%);
    padding: 50px 5%;
    text-align: center;
    color: #fff;
}

#newsletter h4    { font-size: 22px; margin-bottom: 10px; }
#newsletter p     { font-size: 14px; opacity: .9; margin-bottom: 22px; }
#newsletter p span { font-weight: 700; text-decoration: underline; }

#newsletter .form {
    display: flex;
    justify-content: center;
    max-width: 460px;
    margin: 0 auto;
}

#newsletter .form input {
    flex: 1;
    padding: 12px 18px;
    border: none;
    border-radius: 6px 0 0 6px;
    font-size: 14px;
    outline: none;
    color: #333;
}

#newsletter .form button.normal {
    padding: 12px 24px;
    background: #333;
    color: #fff;
    border: none;
    border-radius: 0 6px 6px 0;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}

#newsletter .form button.normal:hover { background: #111; }

/* ── FOOTER ──────────────────────────────────────────── */
footer {
    background: #222;
    color: #ccc;
    padding: 50px 5% 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 30px;
}

footer h4            { color: #fff; margin-bottom: 12px; font-size: 15px; }
footer a             { display: block; color: #aaa; text-decoration: none; font-size: 13px; margin-bottom: 6px; }
footer a:hover       { color: #088178; }
footer p             { font-size: 13px; margin-bottom: 6px; }
footer .follow h4    { margin-top: 16px; }
footer .icon         { display: flex; gap: 12px; margin-top: 8px; font-size: 18px; }
footer .icon i       { cursor: pointer; transition: color .2s; }
footer .icon i:hover { color: #088178; }
footer .row          { display: flex; gap: 10px; margin: 10px 0; }
footer .row img, footer .install img { border-radius: 6px; }

footer .copyright {
    grid-column: 1 / -1;
    text-align: center;
    border-top: 1px solid #444;
    padding-top: 16px;
    font-size: 12px;
    color: #666;
}

/* ── SECTION HELPERS ─────────────────────────────────── */
.section-p1 { padding: 40px 5%; }
.section-m1 { margin: 40px 0; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 768px) {
    #navbar {
        display: none;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0;
        width: 70%; height: 100vh;
        background: #fff;
        padding: 80px 30px 30px;
        box-shadow: 4px 0 20px rgba(0,0,0,.15);
        z-index: 1000;
        gap: 20px;
    }
    #navbar.active { display: flex; }
    #close { display: block !important; position: absolute; top: 20px; right: 20px; }
    #mobile { display: flex; }

    .pro-container { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .pro img { height: 160px; }

    #newsletter .form  { flex-direction: column; }
    #newsletter .form input          { border-radius: 6px; }
    #newsletter .form button.normal  { border-radius: 6px; margin-top: 8px; }
}

@media (max-width: 480px) {
    .pro-container  { grid-template-columns: 1fr; }
    .category-filter { gap: 8px; }
    .category-btn   { padding: 7px 16px; font-size: 12px; }
}
</style>