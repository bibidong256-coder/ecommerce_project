<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - YourWebsite</title>
        <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Hero Section */
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

        .hero {
            text-align: center;
            padding: 60px 0;
            background-color: white;
            margin-bottom: 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .hero p {
            font-size: 18px;
            color: #7f8c8d;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        /* Search Section */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .search-button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .search-button:hover {
            background: #2980b9;
        }
        
        /* Main Content */
        .main-content {
            padding: 20px 0 50px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #3498db;
            margin: 10px auto;
            border-radius: 2px;
        }
        
        /* Help Sections */
        .help-section {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .help-section:hover {
            transform: translateY(-5px);
        }
        
        .help-section h2 {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .help-section h2 i {
            margin-right: 10px;
            color: #3498db;
            font-size: 24px;
        }
        
        /* FAQ Section */
        .faq-container {
            margin-top: 20px;
        }
        
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 18px 20px;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .faq-question:hover {
            background-color: #e9ecef;
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            background-color: white;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }
        
        .faq-toggle {
            font-size: 20px;
            transition: transform 0.3s;
            color: #3498db;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }
        
        /* Contact Options */
        .contact-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .contact-option {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e1e8ed;
        }
        
        .contact-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .contact-icon {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 20px;
        }
        
        .contact-option h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .contact-option p {
            margin-bottom: 20px;
            color: #7f8c8d;
        }
        
        .contact-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        
        .contact-button:hover {
            background-color: #2980b9;
        }
        
        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .quick-link {
            display: flex;
            align-items: center;
            padding: 18px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            border: 1px solid #e1e8ed;
        }
        
        .quick-link:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
        }
        
        .quick-link-icon {
            margin-right: 15px;
            font-size: 24px;
            color: #3498db;
            width: 40px;
            text-align: center;
        }
        
        /* Footer Styles */
        footer {
            /* background-color: #2c3e50; */
            color: #ecf0f1;
            padding: 40px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #3498db;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #3498db;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #34495e;
            color: white;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: #3498db;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
            font-size: 14px;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }
            
            .nav-links {
                margin-top: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-links li {
                margin: 5px 10px;
            }
            
            .hero h1 {
                font-size: 32px;
            }
            
            .help-section {
                padding: 20px;
            }
            
            .contact-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
             <a href="index.php"><button class="close-btn" id="closeBtn">×</button></a>

    <section class="hero">
        <div class="container">
            <h1>How can we help you?</h1>
            <p>Find answers to common questions, troubleshoot issues, or get in touch with our support team.</p>
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search for answers...">
                <button class="search-button"><i class="fas fa-search"></i> Search</button>
            </div>
            <p style="margin-top: 15px; font-size: 14px;">Popular searches: <a href="#" style="color: #3498db;">account settings</a>, <a href="#" style="color: #3498db;">billing</a>, <a href="#" style="color: #3498db;">password reset</a>, <a href="#" style="color: #3498db;">privacy</a></p>
        </div>
    </section>

    <!-- Main Content Section -->
    <main class="main-content">
        <div class="container">
            <h2 class="section-title">Help & Support Resources</h2>
            
            <!-- FAQ Section -->
            <div class="help-section">
                <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
                <p>Find quick answers to the most common questions we receive from users.</p>
                
                <div class="faq-container">
                    <div class="faq-item active">
                        <div class="faq-question">
                            How do I reset my password?
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>To reset your password, go to the login page and click on "Forgot Password". Enter your email address and we'll send you a link to reset your password. If you don't receive the email within 5 minutes, check your spam folder.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How can I update my account information?
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>You can update your account information by logging in and navigating to your Account Settings. From there, you can edit your personal details, change your password, and update your communication preferences.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            What payment methods do you accept?
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers for annual plans. All payments are processed securely through our encrypted payment gateway.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I cancel my subscription?
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>To cancel your subscription, go to your Account Settings, select the Billing tab, and click on "Cancel Subscription". Your account will remain active until the end of your current billing period.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            Is there a mobile app available?
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! We have mobile apps for both iOS and Android devices. You can download them from the App Store or Google Play Store. The mobile app includes all the core features of our web platform.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Options -->
            <div class="help-section">
                <h2><i class="fas fa-headset"></i> Contact Our Support Team</h2>
                <p>Can't find what you're looking for? Our support team is here to help.</p>
                
                <div class="contact-options">
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Live Chat</h3>
                        <p>Get immediate help from our support team</p>
                        <p><strong>Available 24/7</strong></p>
                        <a href="#" class="contact-button">Start Chat</a>
                    </div>
                    
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email Support</h3>
                        <p>Send us a detailed message and we'll respond within 24 hours</p>
                        <p><strong>support@yourwebsite.com</strong></p>
                        <a href="mailto:support@yourwebsite.com" class="contact-button">Send Email</a>
                    </div>
                    
                    <div class="contact-option">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>Phone Support</h3>
                        <p>Speak directly with our support team</p>
                        <p><strong>1-800-123-4567</strong></p>
                        <p>Mon-Fri: 9am-6pm EST</p>
                        <a href="tel:1-800-123-4567" class="contact-button">Call Now</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="help-section">
                <h2><i class="fas fa-link"></i> Quick Help Resources</h2>
                <p>Access our most popular help resources and documentation.</p>
                
                <div class="quick-links">
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <span>Getting Started Guide</span>
                    </a>
                    
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <span>Video Tutorials</span>
                    </a>
                    
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <span>Download Resources</span>
                    </a>
                    
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Community Forum</span>
                    </a>
                    
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <span>API Documentation</span>
                    </a>
                    
                    <a href="#" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-bug"></i>
                        </div>
                        <span>Report a Bug</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
   <footer class="section-p1">
    <div class="col">
        <img class="logo" src="/shoes images/KSD Broken Face Logo Design.png" width="50px" alt="">
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
        <a href="About.php">About us</a>
        <a href="#">Delivery Infomation</a>
        <a href="#">Privacy & Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="contact.php">Contact us</a>
    </div>
        <div class="col">
        <h4>My Account</h4>
        <a href="login.php">Sign in</a>
        <a href="cart.php">Veiw Cart</a>
        <a href="#">Track My order</a>
        <a href="help.html">Help</a>
    </div>
<div class="col install">
    <h4>Install App</h4>
    <p>From App store or Google Play</p>
    <div class="row">
        <img src="/shoes images/apps/download.jpeg" width="50px" alt="">
        <img src="/shoes images/apps/images (4).jpeg" width="50px" alt="">
    </div>
    <p>Secured Payment Getways</p>
    <img src="/shoes images/payement/download (9).jpeg" width="50px" alt="">
</div>
<div class="copyright">
    <p>&copy; 2025, Bibidong Tech Ug</p>
</div>
   </footer>

    <script>
        // FAQ Toggle Functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // If the clicked item wasn't active, open it
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
        
        // Search Functionality
        const searchInput = document.querySelector('.search-input');
        const searchButton = document.querySelector('.search-button');
        
        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        function performSearch() {
            const query = searchInput.value.trim();
            if (query) {
                // In a real implementation, this would search the knowledge base
                // For demo purposes, we'll just show an alert
                alert(`Searching for: "${query}"\n\nThis is a demo. In a real implementation, this would search the knowledge base and display results.`);
                
                // Highlight matching FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    const question = item.querySelector('.faq-question').textContent.toLowerCase();
                    if (question.includes(query.toLowerCase())) {
                        item.style.border = '2px solid #3498db';
                        item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        item.style.border = '1px solid #e1e8ed';
                    }
                });
            }
        }
        
        // Live Chat Demo
        document.querySelectorAll('.contact-button').forEach(button => {
            if (button.textContent.includes('Chat')) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    alert('Starting live chat...\n\nThis is a demo. In a real implementation, this would open a chat window with a support agent.');
                });
            }
        });
    </script>
</body>
</html>