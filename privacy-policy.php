<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Kisken Trends Duuka</title>
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
        
        /* Privacy Policy Content */
        .privacy-content {
            padding: 80px 80px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .privacy-intro {
            text-align: center;
            margin-bottom: 60px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.3s;
        }
        
        .privacy-intro p {
            font-size: 18px;
            color: #465b52;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .policy-section {
            background: white;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .policy-section:nth-child(2) { animation-delay: 0.4s; }
        .policy-section:nth-child(3) { animation-delay: 0.5s; }
        .policy-section:nth-child(4) { animation-delay: 0.6s; }
        .policy-section:nth-child(5) { animation-delay: 0.7s; }
        .policy-section:nth-child(6) { animation-delay: 0.8s; }
        .policy-section:nth-child(7) { animation-delay: 0.9s; }
        .policy-section:nth-child(8) { animation-delay: 1s; }
        .policy-section:nth-child(9) { animation-delay: 1.1s; }
        .policy-section:nth-child(10) { animation-delay: 1.2s; }
        
        .policy-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        
        .policy-section h2 {
            font-size: 24px;
            color: #088178;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .policy-section h2 i {
            margin-right: 15px;
            font-size: 28px;
        }
        
        .policy-section p {
            margin-bottom: 15px;
            color: #465b52;
        }
        
        .policy-section ul {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        
        .policy-section li {
            margin-bottom: 10px;
            color: #465b52;
        }
        
        .policy-section table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .policy-section th, .policy-section td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
        }
        
        .policy-section th {
            background-color: #f5f7fa;
            font-weight: 600;
        }
        
        .policy-section tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .highlight-box {
            background-color: #f0f7f6;
            border-left: 4px solid #088178;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 20px;
        }
        
        .contact-method {
            flex: 1;
            min-width: 250px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .contact-method i {
            font-size: 24px;
            color: #088178;
            margin-bottom: 10px;
        }
        
        .contact-method h4 {
            margin-bottom: 10px;
            color: #222;
        }
        
        /* Quick Navigation */
        .quick-nav {
            position: sticky;
            top: 100px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.3s;
        }
        
        .quick-nav h3 {
            margin-bottom: 15px;
            color: #088178;
        }
        
        .quick-nav ul {
            list-style: none;
        }
        
        .quick-nav li {
            margin-bottom: 10px;
        }
        
        .quick-nav a {
            color: #465b52;
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .quick-nav a i {
            margin-right: 10px;
            font-size: 12px;
            color: #088178;
        }
        
        .quick-nav a:hover {
            color: #088178;
        }
        
        /* Last Updated */
        .last-updated {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 1.3s;
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
            #header, .page-header, .privacy-content, footer {
                padding: 40px 20px;
            }
            
            .page-header h1 {
                font-size: 32px;
            }
            
            .contact-info {
                flex-direction: column;
            }
        }
        
        @media (max-width: 477px) {
            #header, .page-header, .privacy-content, footer {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .policy-section {
                padding: 25px;
            }
            
            .policy-section table {
                font-size: 14px;
            }
            
            .policy-section th, .policy-section td {
                padding: 8px 10px;
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
                        <a href="index.php"><button class="close-btn" id="closeBtn">×</button></a>

        <h1>Privacy Policy</h1>
        <p>Your privacy is important to us. Learn how we collect, use, and protect your personal information.</p>
    </section>

    <section class="privacy-content">
        <div class="privacy-intro">
            <p>At Kisken Trends Duuka, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a purchase from us.</p>
        </div>
        
        <div class="quick-nav">
            <h3>Quick Navigation</h3>
            <ul>
                <li><a href="#information-collection"><i class="fas fa-chevron-right"></i> Information We Collect</a></li>
                <li><a href="#information-use"><i class="fas fa-chevron-right"></i> How We Use Your Information</a></li>
                <li><a href="#information-sharing"><i class="fas fa-chevron-right"></i> Information Sharing</a></li>
                <li><a href="#cookies"><i class="fas fa-chevron-right"></i> Cookies & Tracking</a></li>
                <li><a href="#data-security"><i class="fas fa-chevron-right"></i> Data Security</a></li>
                <li><a href="#your-rights"><i class="fas fa-chevron-right"></i> Your Rights</a></li>
                <li><a href="#policy-changes"><i class="fas fa-chevron-right"></i> Policy Changes</a></li>
                <li><a href="#contact"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
            </ul>
        </div>
        
        <div class="policy-section" id="information-collection">
            <h2><i class="fas fa-info-circle"></i> Information We Collect</h2>
            <p>We collect several types of information from and about users of our website, including:</p>
            
            <h3>Personal Information</h3>
            <ul>
                <li><strong>Contact Information:</strong> Name, email address, phone number, shipping and billing addresses</li>
                <li><strong>Account Information:</strong> Username, password, profile information</li>
                <li><strong>Payment Information:</strong> Credit card details, billing address (processed securely through our payment providers)</li>
                <li><strong>Purchase History:</strong> Details of products you've purchased from us</li>
            </ul>
            
            <h3>Automatically Collected Information</h3>
            <ul>
                <li><strong>Device Information:</strong> IP address, browser type, operating system, device type</li>
                <li><strong>Usage Information:</strong> Pages visited, time spent on site, clickstream data</li>
                <li><strong>Location Information:</strong> General location based on IP address or precise location with your consent</li>
            </ul>
        </div>
        
        <div class="policy-section" id="information-use">
            <h2><i class="fas fa-cogs"></i> How We Use Your Information</h2>
            <p>We use the information we collect for various purposes, including:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Purpose</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Order Processing</td>
                        <td>To process and fulfill your orders, send order confirmations, and provide customer support</td>
                    </tr>
                    <tr>
                        <td>Account Management</td>
                        <td>To create and manage your account, authenticate users, and provide personalized experiences</td>
                    </tr>
                    <tr>
                        <td>Communication</td>
                        <td>To send transactional emails, respond to inquiries, and send marketing communications (with consent)</td>
                    </tr>
                    <tr>
                        <td>Website Improvement</td>
                        <td>To analyze website usage, improve our products and services, and enhance user experience</td>
                    </tr>
                    <tr>
                        <td>Security</td>
                        <td>To detect and prevent fraud, protect our systems, and ensure the security of your information</td>
                    </tr>
                    <tr>
                        <td>Legal Compliance</td>
                        <td>To comply with legal obligations, enforce our terms, and protect our rights</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="policy-section" id="information-sharing">
            <h2><i class="fas fa-share-alt"></i> Information Sharing and Disclosure</h2>
            <p>We do not sell your personal information to third parties. We may share your information in the following circumstances:</p>
            
            <ul>
                <li><strong>Service Providers:</strong> With trusted third parties who assist us in operating our website, conducting business, or servicing you, so long as those parties agree to keep this information confidential</li>
                <li><strong>Payment Processors:</strong> With payment service providers to process your transactions securely</li>
                <li><strong>Shipping Partners:</strong> With shipping companies to deliver your orders</li>
                <li><strong>Legal Requirements:</strong> When required by law or to respond to legal process, protect our rights, or ensure the safety of our users</li>
                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of all or a portion of our assets</li>
            </ul>
            
            <div class="highlight-box">
                <p><strong>Note:</strong> We require all third parties to respect the security of your personal data and to treat it in accordance with the law. We do not allow our third-party service providers to use your personal data for their own purposes.</p>
            </div>
        </div>
        
        <div class="policy-section" id="cookies">
            <h2><i class="fas fa-cookie-bite"></i> Cookies and Tracking Technologies</h2>
            <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information.</p>
            
            <h3>Types of Cookies We Use</h3>
            <ul>
                <li><strong>Essential Cookies:</strong> Necessary for the website to function properly</li>
                <li><strong>Performance Cookies:</strong> Help us understand how visitors interact with our website</li>
                <li><strong>Functionality Cookies:</strong> Allow the website to remember choices you make</li>
                <li><strong>Targeting Cookies:</strong> Used to deliver ads relevant to you and your interests</li>
            </ul>
            
            <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.</p>
        </div>
        
        <div class="policy-section" id="data-security">
            <h2><i class="fas fa-shield-alt"></i> Data Security</h2>
            <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            
            <ul>
                <li>We use SSL encryption to protect data transmission</li>
                <li>We regularly monitor our systems for possible vulnerabilities and attacks</li>
                <li>We restrict access to personal information to employees who need to know that information</li>
                <li>We store personal information on secure servers with limited access</li>
            </ul>
            
            <div class="highlight-box">
                <p><strong>Important:</strong> While we strive to use commercially acceptable means to protect your personal information, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security.</p>
            </div>
        </div>
        
        <div class="policy-section" id="your-rights">
            <h2><i class="fas fa-user-check"></i> Your Rights</h2>
            <p>Depending on your location, you may have the following rights regarding your personal information:</p>
            
            <ul>
                <li><strong>Access:</strong> The right to request copies of your personal information</li>
                <li><strong>Rectification:</strong> The right to request correction of inaccurate information</li>
                <li><strong>Erasure:</strong> The right to request deletion of your personal information</li>
                <li><strong>Restriction:</strong> The right to request limiting how we use your information</li>
                <li><strong>Portability:</strong> The right to request transfer of your data to another organization</li>
                <li><strong>Objection:</strong> The right to object to our processing of your personal information</li>
                <li><strong>Withdraw Consent:</strong> The right to withdraw your consent at any time</li>
            </ul>
            
            <p>To exercise any of these rights, please contact us using the information provided in the "Contact Us" section.</p>
        </div>
        
        <div class="policy-section" id="policy-changes">
            <h2><i class="fas fa-sync-alt"></i> Changes to This Privacy Policy</h2>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
            
            <p>We will let you know via email and/or a prominent notice on our website prior to the change becoming effective. You are advised to review this Privacy Policy periodically for any changes.</p>
        </div>
        
        <div class="policy-section" id="contact">
            <h2><i class="fas fa-envelope"></i> Contact Us</h2>
            <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
            
            <div class="contact-info">
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <h4>Email</h4>
                    <p>privacy@kiskentrendsduuka.com</p>
                </div>
                
                <div class="contact-method">
                    <i class="fas fa-phone"></i>
                    <h4>Phone</h4>
                    <p>+1 (555) 123-4567</p>
                </div>
                
                <div class="contact-method">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>Address</h4>
                    <p>123 Fashion Street<br>New York, NY 10001</p>
                </div>
            </div>
        </div>
        
        <div class="last-updated">
            <p><strong>Last Updated:</strong> October 18, 2023</p>
        </div>
    </section>
    

    <script>
        // Smooth scrolling for quick navigation
        document.addEventListener('DOMContentLoaded', function() {
            const quickNavLinks = document.querySelectorAll('.quick-nav a');
            
            quickNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>