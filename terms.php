<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins','Segoe UI',sans-serif; }
        body { line-height:1.6; color:#333; overflow-x:hidden; background-color:#f8f9fa; }

        #header { display:flex; justify-content:space-between; align-items:center; padding:20px 80px; background-color:#fff; box-shadow:0 5px 15px rgba(0,0,0,.06); z-index:999; position:sticky; top:0; left:0; }
        .navbar-container ul { display:flex; align-items:center; justify-content:center; }
        .navbar-container li { list-style:none; padding:0 20px; position:relative; }
        .navbar-container a { text-decoration:none; font-size:16px; font-weight:600; color:#1a1a1a; transition:.3s ease; }
        .navbar-container a:hover, .navbar-container a.active { color:#088178; }

        .close-btn { position:fixed; top:20px; right:100px; background:none; border:none; font-size:30px; color:#080808; cursor:pointer; transition:color .3s; z-index:10; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
        .close-btn:hover { color:#333; background-color:#f0f0f0; }

        .page-header { background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%); padding:60px 80px; text-align:center; }
        .page-header h1 { font-size:42px; color:#222; margin-bottom:15px; animation:fadeInUp .8s ease forwards; }
        .page-header p { font-size:18px; color:#465b52; max-width:700px; margin:0 auto; animation:fadeInUp .8s ease forwards; animation-delay:.2s; opacity:0; }

        .terms-content { padding:80px; max-width:1200px; margin:0 auto; }
        .terms-intro { text-align:center; margin-bottom:60px; opacity:0; animation:fadeInUp .8s ease forwards; animation-delay:.3s; }
        .terms-intro p { font-size:18px; color:#465b52; max-width:800px; margin:0 auto; }

        .terms-section { background:#fff; border-radius:10px; padding:40px; margin-bottom:30px; box-shadow:0 5px 15px rgba(0,0,0,.05); transition:transform .3s ease,box-shadow .3s ease; opacity:0; animation:fadeInUp .8s ease forwards; }
        .terms-section:nth-child(2){animation-delay:.4s} .terms-section:nth-child(3){animation-delay:.5s} .terms-section:nth-child(4){animation-delay:.6s} .terms-section:nth-child(5){animation-delay:.7s} .terms-section:nth-child(6){animation-delay:.8s} .terms-section:nth-child(7){animation-delay:.9s} .terms-section:nth-child(8){animation-delay:1s} .terms-section:nth-child(9){animation-delay:1.1s} .terms-section:nth-child(10){animation-delay:1.2s} .terms-section:nth-child(11){animation-delay:1.3s} .terms-section:nth-child(12){animation-delay:1.4s}
        .terms-section:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,.08); }
        .terms-section h2 { font-size:24px; color:#088178; margin-bottom:20px; display:flex; align-items:center; }
        .terms-section h2 i { margin-right:15px; font-size:28px; }
        .terms-section h3 { font-size:20px; color:#222; margin:25px 0 15px; }
        .terms-section p { margin-bottom:15px; color:#465b52; }
        .terms-section ul, .terms-section ol { margin-left:25px; margin-bottom:20px; }
        .terms-section li { margin-bottom:10px; color:#465b52; }
        .terms-section table { width:100%; border-collapse:collapse; margin:20px 0; }
        .terms-section th, .terms-section td { border:1px solid #e0e0e0; padding:12px 15px; text-align:left; }
        .terms-section th { background-color:#f5f7fa; font-weight:600; }
        .terms-section tr:nth-child(even) { background-color:#f9f9f9; }

        .highlight-box { background-color:#f0f7f6; border-left:4px solid #088178; padding:20px; margin:20px 0; border-radius:0 5px 5px 0; }
        .warning-box   { background-color:#fff3e0; border-left:4px solid #ff9800; padding:20px; margin:20px 0; border-radius:0 5px 5px 0; }

        /* Contact form */
        .contact-form { margin-top:24px; }
        .form-row { display:flex; gap:16px; flex-wrap:wrap; }
        .form-group { flex:1; min-width:220px; margin-bottom:16px; display:flex; flex-direction:column; }
        .form-group label { font-size:14px; font-weight:600; margin-bottom:6px; color:#333; }
        .form-group input, .form-group textarea, .form-group select { padding:10px 14px; border:1px solid #ddd; border-radius:6px; font-size:15px; font-family:inherit; transition:border-color .2s; }
        .form-group input:focus, .form-group textarea:focus { outline:none; border-color:#088178; }
        .form-group textarea { resize:vertical; min-height:120px; }
        .btn-submit { background:#088178; color:#fff; border:none; padding:12px 32px; border-radius:6px; font-size:16px; font-weight:600; cursor:pointer; transition:background .2s; }
        .btn-submit:hover { background:#066b63; }
        .btn-submit:disabled { background:#aaa; cursor:not-allowed; }
        .form-feedback { margin-top:12px; padding:12px 16px; border-radius:6px; font-size:14px; display:none; }
        .form-feedback.success { background:#e8f5e9; color:#2e7d32; display:block; }
        .form-feedback.error   { background:#ffebee; color:#c62828; display:block; }

        .contact-info { display:flex; flex-wrap:wrap; gap:30px; margin-top:20px; }
        .contact-method { flex:1; min-width:250px; background:#f8f9fa; padding:20px; border-radius:8px; text-align:center; }
        .contact-method i { font-size:24px; color:#088178; margin-bottom:10px; }
        .contact-method h4 { margin-bottom:10px; color:#222; }

        .quick-nav { position:sticky; top:100px; background:#fff; border-radius:10px; padding:20px; box-shadow:0 5px 15px rgba(0,0,0,.05); margin-bottom:30px; opacity:0; animation:fadeInUp .8s ease forwards; animation-delay:.3s; }
        .quick-nav h3 { margin-bottom:15px; color:#088178; }
        .quick-nav ul { list-style:none; }
        .quick-nav li { margin-bottom:10px; }
        .quick-nav a { color:#465b52; text-decoration:none; transition:color .3s ease; display:flex; align-items:center; }
        .quick-nav a i { margin-right:10px; font-size:12px; color:#088178; }
        .quick-nav a:hover { color:#088178; }

        /* Acceptance section */
        .acceptance-section { background:#f0f7f6; border-radius:10px; padding:30px; margin-top:40px; text-align:center; opacity:0; animation:fadeInUp .8s ease forwards; animation-delay:1.5s; }
        .acceptance-section h3 { color:#088178; margin-bottom:15px; }
        .acceptance-section p { color:#465b52; margin-bottom:20px; }
        .btn-accept { background:#088178; color:#fff; border:none; padding:14px 40px; border-radius:8px; font-size:17px; font-weight:600; cursor:pointer; transition:background .2s,transform .1s; }
        .btn-accept:hover { background:#066b63; transform:translateY(-1px); }
        .btn-accept:disabled { background:#aaa; cursor:default; transform:none; }
        .accept-feedback { margin-top:14px; font-size:15px; color:#2e7d32; min-height:22px; }

        .last-updated { text-align:center; margin-top:40px; padding:20px; background:#f8f9fa; border-radius:8px; opacity:0; animation:fadeInUp .8s ease forwards; animation-delay:1.6s; }

        footer { background-color:#222; color:#fff; padding:60px 80px; text-align:center; }
        .footer-content { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:40px; max-width:1200px; margin:0 auto; }
        .footer-column h3 { font-size:20px; margin-bottom:20px; color:#088178; }
        .footer-column ul { list-style:none; }
        .footer-column li { margin-bottom:10px; }
        .footer-column a { color:#ddd; text-decoration:none; transition:color .3s ease; }
        .footer-column a:hover { color:#088178; }
        .copyright { margin-top:40px; padding-top:20px; border-top:1px solid #444; color:#aaa; }

        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }

        @media(max-width:799px){ #header,.page-header,.terms-content,footer{ padding:40px 20px; } .page-header h1{ font-size:32px; } .contact-info{ flex-direction:column; } }
        @media(max-width:477px){ #header,.page-header,.terms-content,footer{ padding:20px; } .page-header h1{ font-size:28px; } .terms-section{ padding:25px; } .terms-section table{ font-size:14px; } .terms-section th,.terms-section td{ padding:8px 10px; } }
    </style>
</head>
<body>

<section class="page-header">
    <a href="index.php"><button class="close-btn" id="closeBtn">×</button></a>
    <h1>Terms and Conditions</h1>
    <p>Please read these terms carefully before using our website or making a purchase</p>
</section>

<section class="terms-content"> 
    <div class="terms-intro">
        <p>Welcome to Kisken Trends Duuka. These terms and conditions outline the rules and regulations for the use of our website and services. By accessing this website, we assume you accept these terms and conditions in full.</p>
    </div>

    <div class="quick-nav">
        <h3>Quick Navigation</h3>
        <ul>
            <li><a href="#acceptance"><i class="fas fa-chevron-right"></i> Acceptance of Terms</a></li>
            <li><a href="#account"><i class="fas fa-chevron-right"></i> Account Registration</a></li>
            <li><a href="#orders"><i class="fas fa-chevron-right"></i> Orders and Payments</a></li>
            <li><a href="#shipping"><i class="fas fa-chevron-right"></i> Shipping & Delivery</a></li>
            <li><a href="#returns"><i class="fas fa-chevron-right"></i> Returns & Refunds</a></li>
            <li><a href="#intellectual"><i class="fas fa-chevron-right"></i> Intellectual Property</a></li>
            <li><a href="#prohibited"><i class="fas fa-chevron-right"></i> Prohibited Uses</a></li>
            <li><a href="#liability"><i class="fas fa-chevron-right"></i> Limitation of Liability</a></li>
            <li><a href="#changes"><i class="fas fa-chevron-right"></i> Changes to Terms</a></li>
            <li><a href="#contact"><i class="fas fa-chevron-right"></i> Contact Information</a></li>
        </ul>
    </div>

    <div class="terms-section" id="acceptance">
        <h2><i class="fas fa-check-circle"></i> Acceptance of Terms</h2>
        <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement. Additionally, when using this website's particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p>
        <div class="highlight-box">
            <p><strong>Important:</strong> If you do not agree to these terms, please do not use our website or services. We reserve the right to update or change these Terms and Conditions at any time without prior notice.</p>
        </div>
    </div>

    <div class="terms-section" id="account">
        <h2><i class="fas fa-user"></i> Account Registration</h2>
        <p>To access certain features of our website, you may be required to register for an account. When registering, you agree to provide accurate, current, and complete information.</p>
        <h3>Account Responsibilities</h3>
        <ul>
            <li>You are responsible for maintaining the confidentiality of your account and password</li>
            <li>You agree to accept responsibility for all activities that occur under your account</li>
            <li>You must notify us immediately of any unauthorized use of your account</li>
            <li>We reserve the right to refuse service, terminate accounts, or remove content at our discretion</li>
        </ul>
        <div class="warning-box">
            <p><strong>Warning:</strong> Sharing your account credentials with others may result in account suspension or termination. You are solely responsible for all activities conducted through your account.</p>
        </div>
    </div>

    <div class="terms-section" id="orders">
        <h2><i class="fas fa-shopping-cart"></i> Orders and Payments</h2>
        <p>All orders placed through our website are subject to product availability and our acceptance of the order.</p>
        <h3>Order Process</h3>
        <ol>
            <li>You place an order on our website</li>
            <li>We send an order confirmation email (this is not acceptance of your order)</li>
            <li>We process your payment and verify stock availability</li>
            <li>We send a shipping confirmation when your order is dispatched</li>
            <li>The contract is formed when we dispatch your order</li>
        </ol>
        <h3>Pricing and Payment</h3>
        <ul>
            <li>All prices are in US Dollars (USD) unless otherwise stated</li>
            <li>We reserve the right to change prices at any time without notice</li>
            <li>Payment must be made in full before orders are processed</li>
            <li>We accept various payment methods including credit cards and PayPal</li>
            <li>Your card may be pre-authorized when you place an order</li>
        </ul>
        <table>
            <thead><tr><th>Payment Method</th><th>Processing Time</th><th>Additional Fees</th></tr></thead>
            <tbody>
                <tr><td>Credit/Debit Card</td><td>Immediate</td><td>No additional fees</td></tr>
                <tr><td>PayPal</td><td>Immediate</td><td>No additional fees</td></tr>
                <tr><td>Bank Transfer</td><td>2-3 business days</td><td>May incur bank charges</td></tr>
            </tbody>
        </table>
    </div>

    <div class="terms-section" id="shipping">
        <h2><i class="fas fa-shipping-fast"></i> Shipping & Delivery</h2>
        <p>We aim to process and ship orders as quickly as possible. Please refer to our Delivery Information page for detailed shipping options and timeframes.</p>
        <h3>Delivery Timeframes</h3>
        <ul>
            <li><strong>Standard Shipping:</strong> 3-5 business days</li>
            <li><strong>Express Shipping:</strong> 1-2 business days</li>
            <li><strong>International Shipping:</strong> 7-14 business days</li>
        </ul>
        <h3>Delivery Issues</h3>
        <p>If you experience any issues with delivery, please contact us within 14 days of the expected delivery date. We are not responsible for delays caused by:</p>
        <ul>
            <li>Incorrect shipping address provided</li>
            <li>Customs clearance delays for international orders</li>
            <li>Weather conditions or other force majeure events</li>
            <li>Carrier delays beyond our control</li>
        </ul>
    </div>

    <div class="terms-section" id="returns">
        <h2><i class="fas fa-undo"></i> Returns & Refunds</h2>
        <p>We want you to be completely satisfied with your purchase. Please review our return policy below.</p>
        <h3>Return Eligibility</h3>
        <ul>
            <li>Items must be returned within 30 days of delivery</li>
            <li>Products must be in original condition with tags attached</li>
            <li>Footwear must be tried on carpeted surfaces only</li>
            <li>Customized or personalized items cannot be returned</li>
            <li>Sale items may have different return conditions</li>
        </ul>
        <h3>Refund Process</h3>
        <ol>
            <li>Contact our customer service to initiate a return</li>
            <li>Receive return authorization and instructions</li>
            <li>Ship items back to us using provided shipping label</li>
            <li>We inspect returned items upon receipt</li>
            <li>Refund is processed to original payment method within 5-7 business days</li>
        </ol>
        <div class="highlight-box">
            <p><strong>Note:</strong> Return shipping costs are the responsibility of the customer unless the return is due to our error or defective products.</p>
        </div>
    </div>

    <div class="terms-section" id="intellectual">
        <h2><i class="fas fa-copyright"></i> Intellectual Property</h2>
        <p>All content included on this website, such as text, graphics, logos, images, audio clips, digital downloads, and software, is the property of Kisken Trends Duuka or its content suppliers and protected by international copyright laws.</p>
        <h3>Permitted Use</h3>
        <ul>
            <li>You may view, download, and print pages from the website for your personal use</li>
            <li>You may share content via social media with proper attribution</li>
        </ul>
        <h3>Prohibited Use</h3>
        <ul>
            <li>Modify, copy, or distribute website content for commercial purposes</li>
            <li>Use our trademarks or logos without express written permission</li>
            <li>Use any data mining, robots, or similar data gathering tools</li>
            <li>Reproduce, duplicate, or exploit any portion of the website</li>
        </ul>
    </div>

    <div class="terms-section" id="prohibited">
        <h2><i class="fas fa-ban"></i> Prohibited Uses</h2>
        <p>In addition to other prohibitions as set forth in the Terms and Conditions, you are prohibited from using the site or its content:</p>
        <ul>
            <li>For any unlawful purpose</li>
            <li>To solicit others to perform or participate in any unlawful acts</li>
            <li>To violate any international, federal, provincial or state regulations, rules, laws, or local ordinances</li>
            <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
            <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
            <li>To submit false or misleading information</li>
            <li>To upload or transmit viruses or any other type of malicious code</li>
            <li>To collect or track the personal information of others</li>
            <li>To spam, phish, pharm, pretext, spider, crawl, or scrape</li>
            <li>For any obscene or immoral purpose</li>
            <li>To interfere with or circumvent the security features of the Service</li>
        </ul>
    </div>

    <div class="terms-section" id="liability">
        <h2><i class="fas fa-balance-scale"></i> Limitation of Liability</h2>
        <p>To the fullest extent permitted by applicable law, Kisken Trends Duuka shall not be liable for any indirect, incidental, special, consequential or punitive damages, resulting from:</p>
        <ul>
            <li>Your use or inability to use the service</li>
            <li>Any conduct or content of any third party on the service</li>
            <li>Any content obtained from the service</li>
            <li>Unauthorized access, use or alteration of your transmissions or content</li>
        </ul>
        <div class="warning-box">
            <p><strong>Important:</strong> Our total cumulative liability to you for all claims shall not exceed the amount you paid to us in the 12 months preceding the claim.</p>
        </div>
    </div>

    <div class="terms-section" id="changes">
        <h2><i class="fas fa-sync-alt"></i> Changes to Terms</h2>
        <p>We reserve the right, at our sole discretion, to update, change or replace any part of these Terms and Conditions by posting updates and changes to our website.</p>
        <p>Your continued use of or access to our website following the posting of any changes constitutes acceptance of those changes.</p>
        <h3>Notification of Changes</h3>
        <ul>
            <li>We will notify users of material changes via email</li>
            <li>The "Last Updated" date at the bottom of this page will be revised</li>
            <li>Continued use after changes constitutes acceptance of new terms</li>
        </ul>
    </div>

    <!-- ── Contact Section with form ── -->
    <div class="terms-section" id="contact">
        <h2><i class="fas fa-envelope"></i> Contact Information</h2>
        <p>Questions about the Terms and Conditions should be sent to us using the form below or through our contact details.</p>

        <div class="contact-info">
            <div class="contact-method">
                <i class="fas fa-envelope"></i>
                <h4>Email</h4>
                <p>@kiskentrendsduuka.com</p>
            </div>
            <div class="contact-method">
                <i class="fas fa-phone"></i>
                <h4>Phone</h4>
                <p>+1 (256) 78340639</p>
            </div>
            <div class="contact-method">
                <i class="fas fa-map-marker-alt"></i>
                <h4>Address</h4>
                <p>kaguje<br>kampala</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h3 style="margin-top:30px;margin-bottom:16px;">Send Us a Message</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_name">Full Name <span style="color:#e53935">*</span></label>
                    <input type="text" id="contact_name" placeholder="Your name">
                </div>
                <div class="form-group">
                    <label for="contact_email">Email Address <span style="color:#e53935">*</span></label>
                    <input type="email" id="contact_email" placeholder="your@email.com">
                </div>
            </div>
            <div class="form-group">
                <label for="contact_subject">Subject</label>
                <input type="text" id="contact_subject" placeholder="What is your message about?">
            </div>
            <div class="form-group">
                <label for="contact_message">Message <span style="color:#e53935">*</span></label>
                <textarea id="contact_message" placeholder="Describe your question or concern..."></textarea>
            </div>
            <button class="btn-submit" id="contactSubmitBtn" onclick="submitContact()">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
            <div class="form-feedback" id="contactFeedback"></div>
        </div>
    </div>

    <!-- ── Acceptance Section ── -->
    <div class="acceptance-section">
        <h3>By using our website, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</h3>
        <p>If you do not agree to these terms, please discontinue use of our website and services immediately.</p>
        <button class="btn-accept" id="acceptBtn" onclick="acceptTerms()">
            <i class="fas fa-check"></i> I Accept These Terms
        </button>
        <div class="accept-feedback" id="acceptFeedback"></div>
    </div>

    <div class="last-updated">
        <p><strong>Last Updated:</strong> October 18, 2023</p>
    </div>
</section>

<script>
    // ── Smooth scroll ────────────────────────────────────────────────────────
    document.querySelectorAll('.quick-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) window.scrollTo({ top: target.offsetTop - 100, behavior: 'smooth' });
        });
    });

    // ── Accept Terms ─────────────────────────────────────────────────────────
    function acceptTerms() {
        const btn      = document.getElementById('acceptBtn');
        const feedback = document.getElementById('acceptFeedback');

        btn.disabled    = true;
        btn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> Recording...';
        feedback.textContent = '';

        const body = new FormData();
        body.append('action', 'accept_terms');
        // If you have a logged-in user ID available via PHP session, echo it here:
        // body.append('user_id', '<?php echo isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : ""; ?>');

        fetch('terms_handler.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML   = '<i class="fas fa-check-double"></i> Terms Accepted!';
                    btn.style.background = '#2e7d32';
                    feedback.textContent = '✓ Your acceptance has been recorded. Thank you!';
                    feedback.style.color = '#2e7d32';
                } else {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> I Accept These Terms';
                    feedback.textContent = 'Something went wrong. Please try again.';
                    feedback.style.color = '#c62828';
                }
            })
            .catch(() => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-check"></i> I Accept These Terms';
                feedback.textContent = 'Network error. Please try again.';
                feedback.style.color = '#c62828';
            });
    }

    // ── Contact Form ─────────────────────────────────────────────────────────
    function submitContact() {
        const btn      = document.getElementById('contactSubmitBtn');
        const feedback = document.getElementById('contactFeedback');

        const name    = document.getElementById('contact_name').value.trim();
        const email   = document.getElementById('contact_email').value.trim();
        const subject = document.getElementById('contact_subject').value.trim();
        const message = document.getElementById('contact_message').value.trim();

        feedback.className = 'form-feedback';
        feedback.style.display = 'none';

        // Client-side validation
        if (!name || !email || !message) {
            feedback.textContent = 'Please fill in all required fields (Name, Email, Message).';
            feedback.className   = 'form-feedback error';
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            feedback.textContent = 'Please enter a valid email address.';
            feedback.className   = 'form-feedback error';
            return;
        }

        btn.disabled  = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        const body = new FormData();
        body.append('action',  'submit_contact');
        body.append('name',    name);
        body.append('email',   email);
        body.append('subject', subject);
        body.append('message', message);

        fetch('terms_handler.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                if (data.success) {
                    feedback.textContent = '✓ ' + data.message;
                    feedback.className   = 'form-feedback success';
                    // Clear fields
                    ['contact_name','contact_email','contact_subject','contact_message'].forEach(id => {
                        document.getElementById(id).value = '';
                    });
                } else {
                    const errs = data.errors ? data.errors.join(' ') : 'Something went wrong.';
                    feedback.textContent = errs;
                    feedback.className   = 'form-feedback error';
                }
            })
            .catch(() => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                feedback.textContent = 'Network error. Please try again.';
                feedback.className   = 'form-feedback error';
            });
    }
</script>
</body>
</html>