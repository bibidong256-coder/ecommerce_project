<?php
session_start();
require "config/db.php";

$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisken Trends Duuka | Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #333; }

        /* ── HEADER ── */
        #header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 5%; background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            position: sticky; top: 0; z-index: 999;
        }
        #header a.brand { text-decoration: none; display: flex; flex-direction: column; align-items: center; }
        #header a.brand img { width: 50px; height: 50px; }
        #header a.brand span { font-size: 13px; font-weight: 700; color: #111; margin-top: 4px; }
        #navbar { list-style: none; display: flex; gap: 24px; align-items: center; }
        #navbar li a { text-decoration: none; color: #333; font-weight: 500; font-size: 14px; transition: color .2s; }
        #navbar li a:hover { color: #088178; }
        #mobile { display: none; align-items: center; gap: 16px; font-size: 20px; }
        #mobile a { color: #333; }
        #bar { cursor: pointer; }
        #close { display: none; cursor: pointer; }

        /* ── PAGE HEADER ── */
        #page-header {
            background: linear-gradient(135deg, #088178 0%, #04534e 100%);
            color: #fff; text-align: center; padding: 50px 5%;
        }
        #page-header h2 { font-size: 28px; letter-spacing: 1px; }
        #page-header p { margin-top: 8px; opacity: .85; }

        /* ── CART TABLE ── */
        #cart { padding: 40px 5%; }
        .cart-table {
            width: 100%; border-collapse: collapse; background: #fff;
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .cart-table thead tr { background: #088178; color: #fff; }
        .cart-table th, .cart-table td { padding: 14px 16px; text-align: center; font-size: 14px; }
        .cart-table tbody tr { border-bottom: 1px solid #f0f0f0; transition: background .15s; }
        .cart-table tbody tr:hover { background: #f6fffe; }
        .cart-table tbody tr:last-child { border-bottom: none; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }

        /* quantity controls */
        .qty-controls { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .qty-btn {
            width: 30px; height: 30px;
            border: 1.5px solid #088178; border-radius: 50%;
            color: #088178; font-weight: 700; font-size: 18px;
            background: none; cursor: pointer; transition: all .2s;
            display: flex; align-items: center; justify-content: center;
        }
        .qty-btn:hover:not(:disabled) { background: #088178; color: #fff; }
        .qty-btn:disabled { opacity: .35; cursor: not-allowed; }
        .qty-display { font-weight: 700; min-width: 28px; text-align: center; font-size: 16px; }

        /* size select */
        .size-select {
            padding: 6px 10px; border: 1.5px solid #088178;
            border-radius: 6px; color: #088178; font-weight: 600;
            background: #e8f5f4; cursor: pointer; font-size: 13px;
            outline: none; transition: border-color .2s;
        }
        .size-select:focus { border-color: #04534e; }

        /* remove btn */
        .remove-btn {
            color: #e74c3c; font-size: 22px;
            background: none; border: none; cursor: pointer;
            transition: transform .2s;
        }
        .remove-btn:hover { transform: scale(1.2); }

        .price-cell { color: #088178; font-weight: 700; }

        /* toast */
        #toast {
            position: fixed; bottom: 30px; right: 30px;
            background: #088178; color: #fff;
            padding: 12px 22px; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            opacity: 0; transform: translateY(10px);
            transition: all .3s; pointer-events: none; z-index: 9999;
        }
        #toast.show { opacity: 1; transform: translateY(0); }

        /* empty state */
        .empty-cart {
            text-align: center; padding: 60px 20px;
            background: #fff; border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .empty-cart i { font-size: 60px; color: #ccc; margin-bottom: 20px; display: block; }
        .empty-cart h3 { color: #888; font-size: 20px; margin-bottom: 12px; }
        .empty-cart a {
            display: inline-block; margin-top: 16px; padding: 12px 28px;
            background: #088178; color: #fff; border-radius: 6px;
            text-decoration: none; font-weight: 600;
        }

        /* ── CART TOTALS ── */
        #cart-add {
            display: flex; justify-content: flex-end;
            padding: 0 5% 60px; gap: 20px; flex-wrap: wrap;
        }
        #Subtotal {
            background: #fff; border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            padding: 28px 32px; min-width: 300px;
        }
        #Subtotal h3 {
            font-size: 18px; color: #088178; margin-bottom: 18px;
            padding-bottom: 10px; border-bottom: 2px solid #e8f5f4;
        }
        #Subtotal table { width: 100%; border-collapse: collapse; }
        #Subtotal td { padding: 10px 0; font-size: 14px; }
        #Subtotal td:last-child { text-align: right; font-weight: 600; }
        #Subtotal tr:last-child td {
            border-top: 1px solid #eee; padding-top: 14px;
            font-size: 16px; color: #088178;
        }
        .checkout-btn {
            display: block; margin-top: 20px; width: 100%; padding: 14px;
            background: linear-gradient(135deg, #088178, #04534e);
            color: #fff; border: none; border-radius: 8px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            text-align: center; text-decoration: none; transition: opacity .2s;
        }
        .checkout-btn:hover { opacity: .88; }

        /* ── FOOTER ── */
        footer {
            background: #222; color: #ccc; padding: 50px 5% 20px;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 30px;
        }
        footer h4 { color: #fff; margin-bottom: 12px; font-size: 15px; }
        footer a { display: block; color: #aaa; text-decoration: none; font-size: 13px; margin-bottom: 6px; }
        footer a:hover { color: #088178; }
        footer p { font-size: 13px; margin-bottom: 6px; }
        footer .icon { display: flex; gap: 12px; margin-top: 8px; font-size: 18px; }
        footer .icon i:hover { color: #088178; cursor: pointer; }
        footer .row { display: flex; gap: 10px; margin: 10px 0; }
        footer .copyright {
            grid-column: 1 / -1; text-align: center;
            border-top: 1px solid #444; padding-top: 16px;
            font-size: 12px; color: #666;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            #navbar {
                display: none; flex-direction: column;
                position: fixed; top: 0; left: 0;
                width: 70%; height: 100vh; background: #fff;
                padding: 80px 30px 30px;
                box-shadow: 4px 0 20px rgba(0,0,0,.15);
                z-index: 1000; gap: 20px;
            }
            #navbar.active { display: flex; }
            #close { display: block !important; position: absolute; top: 20px; right: 20px; font-size: 22px; }
            #mobile { display: flex; }
            .cart-table thead { display: none; }
            .cart-table, .cart-table tbody, .cart-table tr, .cart-table td { display: block; width: 100%; }
            .cart-table tr {
                background: #fff; margin-bottom: 16px; border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,.06); padding: 16px;
            }
            .cart-table td { text-align: left; padding: 6px 0; font-size: 13px; }
            .cart-table td::before { content: attr(data-label); font-weight: 700; display: inline-block; width: 90px; color: #088178; }
            .cart-table img { width: 70px; height: 70px; }
            .qty-controls { justify-content: flex-start; }
            #cart-add { justify-content: stretch; }
            #Subtotal { width: 100%; min-width: unset; }
        }
    </style>
</head>
<body>

<!-- ══ HEADER ══ -->
<section id="header">
    <a href="index.php" class="brand">
        <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" alt="Logo">
        <span>KISKEN TRENDS DUUKA</span>
    </a>
    <div class="navbar-container">
        <ul id="navbar">
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="blogs.php">Blog</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
            <li><a href="login.php"><i class="fa fa-user"></i></a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>
    <div id="mobile">
        <a href="cart.php"><i class="fa fa-shopping-cart"></i></a>
        <i id="bar" class="fas fa-outdent"></i>
    </div>
</section>

<!-- ══ PAGE HEADER ══ -->
<section id="page-header">
    <h2>#Your_Cart</h2>
    <p>Review your items before checkout</p>
</section>

<!-- ══ CART BODY ══ -->
<section id="cart">
<?php if (empty($cart)): ?>
    <div class="empty-cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="shop.php">Continue Shopping</a>
    </div>

<?php else:
    $ids          = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt         = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products     = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    $sizes = ["30","31","32","33","34","35","37","38","39","40","41","42","43","45","46"];
?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Remove</th><th>Image</th><th>Product</th>
                <th>Size</th><th>Price</th><th>Quantity</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody id="cart-body">
        <?php foreach ($products as $row):
            $id       = $row['id'];
            $cartItem = $cart[$id];
            $qty      = is_array($cartItem) ? (int)($cartItem['qty'] ?? 1) : (int)$cartItem;
            $size     = is_array($cartItem) ? ($cartItem['size'] ?? '') : '';
            $subtotal = $qty * $row['price'];
            $total   += $subtotal;
        ?>
            <tr id="row-<?= $id ?>">
                <td data-label="Remove">
                    <button class="remove-btn" data-id="<?= $id ?>">
                        <i class="far fa-times-circle"></i>
                    </button>
                </td>
                <td data-label="Image">
                    <img src="shoes images/<?= htmlspecialchars($row['image']) ?>"
                         alt="<?= htmlspecialchars($row['name']) ?>">
                </td>
                <td data-label="Product"><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                <td data-label="Size">
                    <select class="size-select" data-id="<?= $id ?>">
                        <option value="">Select size</option>
                        <?php foreach ($sizes as $s): ?>
                            <option value="<?= $s ?>" <?= $size === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td data-label="Price" class="price-cell">
                    Shs <?= number_format($row['price']) ?>
                </td>
                <td data-label="Quantity">
                    <div class="qty-controls">
                        <button class="qty-btn decrease-btn"
                                data-id="<?= $id ?>"
                                data-price="<?= $row['price'] ?>"
                                <?= $qty <= 1 ? 'disabled' : '' ?>>−</button>
                        <span class="qty-display" id="qty-<?= $id ?>"><?= $qty ?></span>
                        <button class="qty-btn increase-btn"
                                data-id="<?= $id ?>"
                                data-price="<?= $row['price'] ?>">+</button>
                    </div>
                </td>
                <td data-label="Subtotal" class="price-cell"
                    id="subtotal-<?= $id ?>"
                    data-raw="<?= $subtotal ?>">
                    Shs <?= number_format($subtotal) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</section>

<!-- ══ TOTALS ══ -->
<?php if (!empty($cart)): ?>
<section id="cart-add">
    <div id="Subtotal">
        <h3>Cart Totals</h3>
        <table>
            <tr>
                <td>Cart Subtotal</td>
                <td id="summary-subtotal">Shs <?= number_format($total) ?></td>
            </tr>
            <tr><td>Shipping</td><td>Free</td></tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong id="summary-total">Shs <?= number_format($total) ?></strong></td>
            </tr>
        </table>
        <a href="process_order.php" class="checkout-btn">PLACE ORDER</a>    
</div>
</section>
<?php endif; ?>

<!-- ══ FOOTER ══ -->
<footer>
    <div class="col">
        <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" width="50px" alt="Footer Logo">
        <h4>Contact</h4>
        <p>Address: Kaguje Road, Street 32, Kampala</p>
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
        <img src="shoes images/payement/download (9).jpeg" width="50px" alt="Payments">
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?>, Bibidong Tech Ug</p>
    </div>
</footer>

<div id="toast"></div>

<script>
 // ── Helpers ────────────────────────────────────────────────────────────────────
 function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = isError ? '#e74c3c' : '#088178';
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
 }

 function fmt(n) {
    return 'Shs ' + Math.round(n).toLocaleString();
 }

 function recalcTotal() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(cell => {
        total += parseFloat(cell.dataset.raw || 0);
    });
    document.getElementById('summary-subtotal').textContent = fmt(total);
    document.getElementById('summary-total').textContent    = fmt(total);
 }

 // ── Central AJAX function — calls update_cart.php, returns JSON ────────────────
 async function cartAjax(params) {
    const url = 'api/update_cart.php?' + new URLSearchParams(params);
    const res  = await fetch(url);
    if (!res.ok) throw new Error('Server error ' + res.status);
    return res.json();
 } 

 // ── Quantity: + / − ────────────────────────────────────────────────────────────
 document.querySelectorAll('.increase-btn, .decrease-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id     = btn.dataset.id;
        const price  = parseFloat(btn.dataset.price);
        const action = btn.classList.contains('increase-btn') ? 'increase' : 'decrease';

        const row   = document.getElementById('row-' + id);
        const btns  = row.querySelectorAll('.qty-btn');
        btns.forEach(b => b.disabled = true);

        try {
            const data = await cartAjax({ id, action });

            if (data.removed) {
                row.remove();
                showToast('Item removed');
                if (!document.querySelector('#cart-body tr')) location.reload();
                return;
            }

            const qtyEl = document.getElementById('qty-' + id);
            qtyEl.textContent = data.qty;

            const sub     = price * data.qty;
            const subCell = document.getElementById('subtotal-' + id);
            subCell.textContent = fmt(sub);
            subCell.dataset.raw = sub;

            row.querySelector('.decrease-btn').disabled = (data.qty <= 1);
            row.querySelector('.increase-btn').disabled = false;

            recalcTotal();
        } catch (e) {
            showToast('Something went wrong. Try again.', true);
            btns.forEach(b => b.disabled = false);
        }
    });
 });

 // ── Remove ─────────────────────────────────────────────────────────────────────
 document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id  = btn.dataset.id;
        btn.disabled = true;

        try {
            await cartAjax({ id, action: 'remove' });
            document.getElementById('row-' + id).remove();
            showToast('Item removed');
            if (!document.querySelector('#cart-body tr')) location.reload();
            else recalcTotal();
        } catch (e) {
            showToast('Could not remove item.', true);
            btn.disabled = false;
        }
    });
 });

 // ── Size ───────────────────────────────────────────────────────────────────────
 document.querySelectorAll('.size-select').forEach(sel => {
    sel.addEventListener('change', async () => {
        const id   = sel.dataset.id;
        const size = sel.value;

        try {
            await cartAjax({ id, action: 'size', size });
            showToast('Size saved ✓');
        } catch (e) {
            showToast('Could not save size.', true);
        }
    });
 });

 // ── Mobile nav ─────────────────────────────────────────────────────────────────
 const bar   = document.getElementById('bar');
 const close = document.getElementById('close');
 const nav   = document.getElementById('navbar');
 
 if (bar) bar.addEventListener('click', e => {
    e.stopPropagation();
    nav.classList.add('active');
    document.body.style.overflow = 'hidden';
  });
 if (close) close.addEventListener('click', e => {
    e.stopPropagation();
    nav.classList.remove('active');
    document.body.style.overflow = '';
 });
 document.addEventListener('click', e => {
    if (window.innerWidth <= 768 && nav.classList.contains('active')
        && !nav.contains(e.target) && bar && !bar.contains(e.target)) {
        nav.classList.remove('active');
        document.body.style.overflow = '';
    }
  });
 window.addEventListener('resize', () => {
    if (window.innerWidth > 768) { nav.classList.remove('active'); document.body.style.overflow = ''; }
  });
</script>

</body>
</html>