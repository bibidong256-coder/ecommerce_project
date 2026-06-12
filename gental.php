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
                <li> <a class="active" href="shop.php">shop</a></li>
                <li> <a href="blogs.php">Blog</a></li>
                <li> <a href="about-us.php">About</a></li>
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
    <h2>Men's Shoes</h2>
    <p>Your Selection is our Command</p> <br>

        <div class="product-controls">
    <div class="category-filter">
        
        <a href="men.php" style="text-decoration: none;">
            <button class="category-btn active" data-filter="all">
                All Shoes
            </button>
        </a>
        
        
        <a href="gental.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="men">
                Gentals
            </button>
        </a>
        
        
        <a href="sneakers.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="women">
                Sneakers
            </button>
        </a>
        
        
        <a href="Men_boots.php" style="text-decoration: none;">
            <button class="category-btn" data-filter="kids">
                boots
            </button>
        </a>
    </div>
</div> 


    <div class="pro-container" data-page="8">
    </div>

    <!-- View More Button -->
    <div class="btn-container">
        <a href="men.php" class="view-more-btn">back</a>
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
        <img class="logo" src="shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
        <h4>Contact</h4>
        <p><span>Address</span>: kaguje Road, street 32, kampala</p>
        <p><strong>Phone:</strong>+256789340639</p>
        <p><strong>Hours:</strong>10:00-18:00, Mon - Sat</p>
        <div class="follow">
            <h4>Follow us</h4>
            <div class="icon">
                <i class="fab fa-facebook"></i>
                <i class="fab fa-Twitter"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-youtub"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <h4>About</h4>
        <a href="about-us.php">About us</a>
        <a href="delivery.php">Delivery Information</a>
        <a href="privacy-policy.php">Privacy & Policy</a>
        <a href="terms.php">Terms & Conditions</a>
        <a href="contact.php">Contact us</a>
    </div>
        <div class="col">
        <h4>My Account</h4>
        <a href="login.php">Sign in</a>
        <a href="cart.php">Veiw Cart</a>
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
    <p>Secured Payment Getways</p>
    <img src="shoes images/payement/download (9).jpeg" width="50px" alt="">
    </div>
    <div class="copyright">
    <p>&copy; 2025, Bibidong Tech Ug</p>
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

/* ─── CSS Variables ─────────────────────────────────── */
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

/* ─── Reset & Base ───────────────────────────────────── */
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

/* ─── Header ─────────────────────────────────────────── */
#header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 8%;
  background: var(--white);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

#navbar {
  display: flex;
  align-items: center;
  gap: 6px;
  list-style: none;
}

#navbar li a {
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text);
  padding: 7px 14px;
  border-radius: 8px;
  transition: background var(--transition), color var(--transition);
}

#navbar li a:hover,
#navbar li a.active {
  background: var(--accent);
  color: var(--primary);
}

#navbar li a i { font-size: 1rem; }

/* Mobile nav */
#mobile {
  display: none;
  align-items: center;
  gap: 16px;
  font-size: 1.1rem;
  color: var(--text);
}

#mobile a { color: var(--text); text-decoration: none; }

#close {
  display: none;
  position: absolute;
  top: 18px;
  right: 20px;
  font-size: 1.3rem;
  color: var(--text);
  text-decoration: none;
  z-index: 201;
}

#bar { cursor: pointer; }

@media (max-width: 768px) {
  #mobile { display: flex; }

  #navbar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 260px;
    height: 100vh;
    background: var(--white);
    flex-direction: column;
    align-items: flex-start;
    padding: 60px 24px 24px;
    gap: 4px;
    box-shadow: var(--shadow-lg);
    transition: left 0.3s ease;
    z-index: 200;
    overflow-y: auto;
  }

  #navbar.active { left: 0; }

  #navbar li { width: 100%; }
  #navbar li a { display: block; width: 100%; padding: 10px 14px; }

  #close { display: block; }
}

/* ─── Product Section ────────────────────────────────── */
#product.section-p1 {
  padding: 50px 8%;
  background: var(--bg);
}

#product h2 {
  font-size: clamp(1.6rem, 3.5vw, 2.4rem);
  font-weight: 800;
  color: var(--text);
  text-align: center;
  margin-bottom: 6px;
  position: relative;
}

#product h2::after {
  content: '';
  display: block;
  width: 50px;
  height: 3px;
  background: var(--primary);
  border-radius: 2px;
  margin: 10px auto 0;
}

#product > p {
  text-align: center;
  font-size: 1rem;
  color: var(--text-light);
  margin-top: 10px;
  margin-bottom: 30px;
}

/* ─── Category Filter ────────────────────────────────── */
.product-controls {
  display: flex;
  justify-content: center;
  margin-bottom: 10px;
}

.category-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}

.category-btn {
  padding: 9px 22px;
  background: var(--white);
  color: var(--text);
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.8px;
  border: 2px solid var(--border);
  border-radius: 30px;
  cursor: pointer;
  transition: all var(--transition);
}

.category-btn:hover {
  border-color: var(--primary);
  color: var(--primary);
  background: var(--accent);
}

.category-btn.active {
  background: var(--primary);
  color: var(--white);
  border-color: var(--primary);
  box-shadow: var(--shadow-sm);
}

/* ─── Product Grid ───────────────────────────────────── */
.pro-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
  gap: 24px;
  margin-top: 10px;
  margin-bottom: 40px;
}

/* ─── Product Card ───────────────────────────────────── */
.pro {
  background: var(--white);
  border-radius: var(--radius);
  border: 1.5px solid var(--border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
  position: relative;
}

.pro:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-md);
  border-color: var(--primary);
}

.pro > img {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.pro:hover > img {
  transform: scale(1.05);
}

/* Description */
.des {
  padding: 12px 14px 8px;
  flex: 1;
}

.des span {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--primary);
  background: var(--accent);
  padding: 3px 10px;
  border-radius: 20px;
  margin-bottom: 6px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.des h5 {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text);
  line-height: 1.4;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.des .star {
  display: flex;
  gap: 2px;
  margin-bottom: 6px;
}

.des .star i {
  font-size: 0.75rem;
  color: #f5a623;
}

.des h4 {
  font-size: 1rem;
  font-weight: 800;
  color: var(--primary);
}

/* Product actions */
.product-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px 14px;
  gap: 10px;
  border-top: 1px solid var(--border);
  background: var(--bg);
}

.cart-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: var(--accent);
  color: var(--primary);
  font-size: 0.95rem;
  text-decoration: none;
  border: 1.5px solid transparent;
  flex-shrink: 0;
  transition: all var(--transition);
}

.cart-link:hover {
  background: var(--primary);
  color: var(--white);
  border-color: var(--primary);
  transform: scale(1.1);
}

.view-details-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: var(--primary);
  color: var(--white);
  font-size: 0.82rem;
  font-weight: 700;
  text-decoration: none;
  border-radius: 8px;
  flex: 1;
  justify-content: center;
  white-space: nowrap;
  transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
}

.view-details-btn:hover {
  background: var(--dark);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.view-details-btn i { font-size: 0.8rem; }

/* ─── Back Button ────────────────────────────────────── */
.btn-container {
  display: flex;
  justify-content: center;
  padding: 10px 0 20px;
}

.view-more-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 36px;
  background: transparent;
  color: var(--primary);
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-decoration: none;
  border: 2px solid var(--primary);
  border-radius: var(--radius);
  transition: background var(--transition), color var(--transition),
              transform var(--transition), box-shadow var(--transition);
}

.view-more-btn:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.view-more-btn:active {
  transform: translateY(0);
  box-shadow: none;
}

/* ─── Newsletter ─────────────────────────────────────── */
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

.newstext {
  text-align: center;
  max-width: 560px;
  position: relative;
  z-index: 1;
}

.newstext h4 {
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.75);
  margin-bottom: 10px;
}

.newstext p {
  font-size: clamp(1.1rem, 2.5vw, 1.6rem);
  font-weight: 700;
  color: var(--white);
  margin-bottom: 28px;
  line-height: 1.4;
}

.newstext p span {
  color: #a8e6e2;
  font-style: italic;
}

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
  padding: 14px 24px;
  font-size: 0.9rem;
  background: var(--dark);
  color: var(--white);
  border: none;
  font-weight: 700;
  cursor: pointer;
  flex-shrink: 0;
  white-space: nowrap;
  transition: background var(--transition);
}

.newstext .form button.normal:hover { background: #032e2b; }

/* ─── Footer ─────────────────────────────────────────── */
footer.section-p1 {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  padding: 50px 8% 30px;
  background: #1a1a2e;
  color: #ccc;
}

footer .col {
  flex: 1 1 160px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

footer .col img.logo {
  margin-bottom: 8px;
  border-radius: 8px;
}

footer .col h4 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--white);
  margin-bottom: 6px;
  letter-spacing: 0.5px;
}

footer .col p {
  font-size: 0.87rem;
  color: #aaa;
  line-height: 1.6;
}

footer .col p strong { color: #ccc; }

footer .col a {
  font-size: 0.87rem;
  color: #aaa;
  text-decoration: none;
  transition: color var(--transition);
  width: fit-content;
}

footer .col a:hover { color: var(--primary); }

footer .follow h4 { margin-top: 14px; }

footer .icon {
  display: flex;
  gap: 12px;
  margin-top: 6px;
}

footer .icon i {
  font-size: 1.2rem;
  color: #aaa;
  cursor: pointer;
  transition: color var(--transition), transform var(--transition);
}

footer .icon i:hover {
  color: var(--primary);
  transform: scale(1.2);
}

footer .col.install .row {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

footer .col.install .row img,
footer .col.install > img {
  border-radius: 6px;
  object-fit: cover;
}

footer .copyright {
  width: 100%;
  text-align: center;
  padding-top: 22px;
  border-top: 1px solid #2e2e4a;
  font-size: 0.82rem;
  color: #666;
}

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 900px) {
  #product.section-p1 { padding: 40px 5%; }
  #newsletter { padding: 50px 5%; }
  footer.section-p1 { padding: 40px 5% 24px; }
}

@media (max-width: 640px) {
  .pro-container {
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 16px;
  }

  .product-actions { flex-wrap: wrap; }

  .view-details-btn { flex: 1 1 100%; }

  .newstext .form {
    flex-direction: column;
    overflow: visible;
    box-shadow: none;
    gap: 10px;
  }

  .newstext .form input {
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
  }

  .newstext .form button.normal {
    border-radius: var(--radius);
    width: 100%;
  }

  footer.section-p1 { padding: 36px 5% 20px; }
  footer .col { flex: 1 1 140px; }
}
</style>