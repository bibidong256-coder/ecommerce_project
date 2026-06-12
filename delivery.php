<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Information - Kisken Trends Duuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }
        
        body {
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        
        /* Header styles */
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 80px;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
            z-index: 999;
            position: sticky;
            top: 0;
            left: 0;
        }
        
        .navbar-container ul {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar-container li {
            list-style: none;
            padding: 0 20px;
            position: relative;
        }
        
        .navbar-container a {
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            transition: 0.3s ease;
        }
        
        .navbar-container a:hover,
        .navbar-container a.active {
            color: #088178;
        }
        
        /* Page Header */
                        .close-btn {
            position: absolute;
            top: 20px;
            right: 100px;
            background: none;
            border: none;
            font-size: 30px;
            color: #080808;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 10;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-btn:hover {
            color: #333;
            background-color: #f0f0f0;
        }
        .close-btn{
            position: fixed;
        }
        .page-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 60px 80px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 42px;
            color: #222;
            margin-bottom: 15px;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .page-header p {
            font-size: 18px;
            color: #465b52;
            max-width: 700px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.2s;
            opacity: 0;
        }
        
        /* Delivery Info Section */
        .delivery-info {
            padding: 80px 80px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .info-card:nth-child(1) { animation-delay: 0.3s; }
        .info-card:nth-child(2) { animation-delay: 0.4s; }
        .info-card:nth-child(3) { animation-delay: 0.5s; }
        .info-card:nth-child(4) { animation-delay: 0.6s; }
        .info-card:nth-child(5) { animation-delay: 0.7s; }
        .info-card:nth-child(6) { animation-delay: 0.8s; }
        
        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .info-card i {
            font-size: 40px;
            color: #088178;
            margin-bottom: 20px;
        }
        
        .info-card h3 {
            font-size: 22px;
            color: #222;
            margin-bottom: 15px;
        }
        
        .info-card p {
            color: #465b52;
            margin-bottom: 15px;
        }
        
        .info-card ul {
            list-style-type: none;
            margin-left: 0;
        }
        
        .info-card li {
            padding: 5px 0;
            color: #465b52;
            position: relative;
            padding-left: 25px;
        }
        
        .info-card li:before {
            content: "✓";
            color: #088178;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        /* Delivery Timeline */
        .delivery-timeline {
            background: white;
            border-radius: 10px;
            padding: 40px;
            margin-top: 50px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.9s;
        }
        
        .delivery-timeline h2 {
            text-align: center;
            font-size: 32px;
            color: #222;
            margin-bottom: 40px;
        }
        
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #088178;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }
        
        .timeline-item {
            padding: 10px 40px;
            position: relative;
            width: 50%;
            box-sizing: border-box;
            margin-bottom: 30px;
        }
        
        .timeline-item:nth-child(odd) {
            left: 0;
        }
        
        .timeline-item:nth-child(even) {
            left: 50%;
        }
        
        .timeline-content {
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .timeline-item:nth-child(odd) .timeline-content:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            right: -10px;
            background-color: white;
            top: 20px;
            border-radius: 50%;
            box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
        }
        
        .timeline-item:nth-child(even) .timeline-content:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            left: -10px;
            background-color: white;
            top: 20px;
            border-radius: 50%;
            box-shadow: -1px 1px 1px rgba(0, 0, 0, 0.2);
        }
        
        .timeline-content h3 {
            margin-top: 0;
            color: #088178;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 60px 80px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .faq-section h2 {
            text-align: center;
            font-size: 32px;
            color: #222;
            margin-bottom: 40px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 1s;
        }
        
        .faq-item {
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .faq-item:nth-child(2) { animation-delay: 1.1s; }
        .faq-item:nth-child(3) { animation-delay: 1.2s; }
        .faq-item:nth-child(4) { animation-delay: 1.3s; }
        .faq-item:nth-child(5) { animation-delay: 1.4s; }
        
        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        
        .faq-question:hover {
            background-color: #f8f9fa;
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-question i {
            transition: transform 0.3s ease;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 80px 80px;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 36px;
            color: #222;
            margin-bottom: 20px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .cta-section p {
            font-size: 18px;
            color: #465b52;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.2s;
        }
        
        .cta-button {
            background-color: #088178;
            color: white;
            border: 0;
            padding: 14px 30px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.4s;
            box-shadow: 0 4px 15px rgba(8, 129, 120, 0.3);
            text-decoration: none;
            display: inline-block;
        }
        
        .cta-button:hover {
            background-color: #06665f;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(8, 129, 120, 0.4);
        }
        
        /* Footer */
        footer {
            background-color: #222;
            color: white;
            padding: 60px 80px;
            text-align: center;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #088178;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column li {
            margin-bottom: 10px;
        }
        
        .footer-column a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-column a:hover {
            color: #088178;
        }
        
        .copyright {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #aaa;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 799px) {
            #header, .page-header, .delivery-info, .faq-section, .cta-section, footer {
                padding: 40px 20px;
            }
            
            .page-header h1 {
                font-size: 32px;
            }
            
            .timeline:before {
                left: 31px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
            }
            
            .timeline-item:nth-child(even) {
                left: 0;
            }
            
            .timeline-item:nth-child(odd) .timeline-content:after,
            .timeline-item:nth-child(even) .timeline-content:after {
                left: -50px;
                right: auto;
            }
        }
        
        @media (max-width: 477px) {
            #header, .page-header, .delivery-info, .faq-section, .cta-section, footer {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- <section id="header">
        <a href="#" style="text-decoration: none; display: flex; flex-direction: column; align-items: center;">
            <img src="/shoes images/xxxxx/KSD Broken Face Logo Design.png" style="width: 50px; height: 50px;" alt="Company Logo">
            <span style="font-size: 14px; font-weight: bold; color: black; margin-top: 5px;">KISKEN TRENDS DUUKA</span>
        </a>
        
        <div class="navbar-container">
            <div>
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li id="lg-bag"><a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></li>
                    <li><a href="login.php"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                </ul>
                <a href="#" id="close"><i class="fas fa-times"></i></a>
            </div>
            <div id="mobile">
                <a href="cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
                <i id="bar" class="fas fa-outdent"></i>
            </div>
        </div>
    </section> -->

    <section class="page-header">
                <a href="Shop.php "><button class="close-btn" id="closeBtn">×</button></a>
        <h1>Delivery Information</h1>
        <p>Learn about our delivery options, shipping times, and policies to get your products as quickly as possible</p>
    </section>

    <section class="delivery-info">
        <div class="info-grid">
            <div class="info-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Standard Delivery</h3>
                <p>Our standard delivery option gets your order to you within 3-5 business days.</p>
                <ul>
                    <li>Free on orders over $50</li>
                    <li>$4.99 for orders under $50</li>
                    <li>3-5 business days</li>
                    <li>Trackable service</li>
                </ul>
            </div>
            
            <div class="info-card">
                <i class="fas fa-rocket"></i>
                <h3>Express Delivery</h3>
                <p>Need your items faster? Choose our express delivery for next-day service.</p>
                <ul>
                    <li>$9.99 flat rate</li>
                    <li>Next business day delivery</li>
                    <li>Order before 2pm</li>
                    <li>Real-time tracking</li>
                </ul>
            </div>
            
            <div class="info-card">
                <i class="fas fa-store"></i>
                <h3>Click & Collect</h3>
                <p>Order online and pick up from our store at your convenience.</p>
                <ul>
                    <li>Free service</li>
                    <li>Ready in 2 hours</li>
                    <li>Convenient store locations</li>
                    <li>Extended pickup hours</li>
                </ul>
            </div>
            
            <div class="info-card">
                <i class="fas fa-globe-africa"></i>
                <h3>International Delivery</h3>
                <p>We ship worldwide with competitive international rates.</p>
                <ul>
                    <li>7-14 business days</li>
                    <li>Customs fees may apply</li>
                    <li>Fully tracked service</li>
                    <li>Insurance included</li>
                </ul>
            </div>
        </div>
        
        <div class="delivery-timeline">
            <h2>Delivery Process</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Order Placed</h3>
                        <p>Once you complete your purchase, we immediately begin processing your order.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Order Processing</h3>
                        <p>We verify your order details and prepare your items for shipment (1-2 business days).</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Shipped</h3>
                        <p>Your order is packed and handed over to our delivery partner with tracking information.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>In Transit</h3>
                        <p>Your package is on its way to your specified delivery address.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3>Delivered</h3>
                        <p>Your order arrives at your doorstep. You'll receive a delivery confirmation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="faq-section">
        <h2>Frequently Asked Questions</h2>
        
        <div class="faq-item">
            <div class="faq-question">
                <span>How can I track my order?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Once your order has been shipped, you will receive a tracking number via email. You can use this tracking number on our website or the carrier's website to monitor your package's journey.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <span>What if I'm not home when my order arrives?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>For standard delivery, the carrier will attempt delivery up to 3 times. After that, your package will be held at a local depot for collection. For express delivery, you can provide special instructions during checkout.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <span>Do you deliver on weekends?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Yes, we offer weekend delivery for an additional fee of $5.99. This service is available for express delivery orders placed before 12pm on Friday.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <span>Can I change my delivery address after ordering?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>You can change your delivery address within 2 hours of placing your order by contacting our customer service team. After that, changes may not be possible as your order enters the processing stage.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <span>What is your returns policy for delivered items?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>We offer a 30-day return policy for all items in their original condition. Return shipping is free for items that are faulty or incorrect. For change of mind returns, return shipping costs are the responsibility of the customer.</p>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <h2>Would you like to Place New Orders?. Place the Button Below</h2>
        <p>Browse our collection and enjoy fast, reliable delivery to your doorstep</p>
        <a href="shop.html" class="cta-button">Shop Now</a>
    </section>
    
    <script>
        // FAQ toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>