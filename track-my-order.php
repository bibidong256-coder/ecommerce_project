<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order | YourStore</title>
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
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
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


        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #4a6cf7;
        }

        .logo span {
            color: #ff6b6b;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #4a6cf7;
        }

        .track-order-section {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #4a6cf7;
        }

        .track-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .track-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .track-btn {
            background-color: #4a6cf7;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .track-btn:hover {
            background-color: #3a5bd9;
        }

        .order-status {
            margin: 30px 0;
        }

        .status-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 0;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            z-index: 1;
        }

        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            color: #fff;
        }

        .status-step.active .status-icon {
            background-color: #4a6cf7;
        }

        .status-step.completed .status-icon {
            background-color: #4caf50;
        }

        .status-label {
            font-size: 14px;
            text-align: center;
            color: #777;
        }

        .status-step.active .status-label {
            color: #4a6cf7;
            font-weight: 600;
        }

        .status-step.completed .status-label {
            color: #4caf50;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .detail-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #4a6cf7;
        }

        .detail-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #555;
        }

        .detail-content {
            font-size: 18px;
            color: #333;
        }

        .order-items {
            margin-top: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items-table th {
            background-color: #f1f5fd;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
        }

        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .help-section {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 30px;
        }

        .help-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .help-option {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .help-option:hover {
            transform: translateY(-5px);
        }

        .help-icon {
            width: 50px;
            height: 50px;
            background-color: #4a6cf7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 20px;
        }

        .help-text h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .help-text p {
            font-size: 14px;
            color: #777;
        }

        footer {
            background-color: #2d3748;
            color: #fff;
            padding: 40px 0;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
            }
            
            nav ul li {
                margin: 0 10px;
            }
            
            .track-form {
                flex-direction: column;
            }
            
            .status-timeline {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .status-step {
                margin: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <section class="track-order-section">
        <a href="Shop.html"><button class="close-btn" id="closeBtn">×</button></a>
            <h1 class="section-title"><i class="fas fa-shipping-fast"></i> Track Your Order</h1>
            <p>Enter your order number and email to check the status of your order</p>
            
            <form class="track-form">
                <input type="text" class="track-input" placeholder="Order Number (e.g., ORD-123456)" required>
                <input type="email" class="track-input" placeholder="Email Address" required>
                <button type="submit" class="track-btn">Track Order</button>
            </form>
            
            <div class="order-status">
                <h2>Order Status: <span style="color: #4a6cf7;">Shipped</span></h2>
                <p>Estimated Delivery: <strong>October 28, 2023</strong></p>
                
                <div class="status-timeline">
                    <div class="status-step completed">
                        <div class="status-icon"><i class="fas fa-check"></i></div>
                        <div class="status-label">Order Placed</div>
                        <div class="status-date">Oct 20</div>
                    </div>
                    <div class="status-step completed">
                        <div class="status-icon"><i class="fas fa-box"></i></div>
                        <div class="status-label">Processing</div>
                        <div class="status-date">Oct 21</div>
                    </div>
                    <div class="status-step completed">
                        <div class="status-icon"><i class="fas fa-shipping-fast"></i></div>
                        <div class="status-label">Shipped</div>
                        <div class="status-date">Oct 23</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon"><i class="fas fa-truck"></i></div>
                        <div class="status-label">Out for Delivery</div>
                        <div class="status-date">Oct 28</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon"><i class="fas fa-home"></i></div>
                        <div class="status-label">Delivered</div>
                        <div class="status-date">--</div>
                    </div>
                </div>
            </div>
            
            <!-- <div class="order-details">
                <div class="detail-card">
                    <div class="detail-title">Order Information</div>
                    <div class="detail-content">
                        <p><strong>Order Number:</strong> ORD-789123</p>
                        <p><strong>Order Date:</strong> October 20, 2023</p>
                        <p><strong>Order Total:</strong> $147.99</p>
                    </div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-title">Shipping Address</div>
                    <div class="detail-content">
                        <p>John Smith</p>
                        <p>123 Main Street</p>
                        <p>New York, NY 10001</p>
                        <p>United States</p>
                    </div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-title">Shipping Details</div>
                    <div class="detail-content">
                        <p><strong>Carrier:</strong> FedEx</p>
                        <p><strong>Tracking Number:</strong> 789012345678</p>
                        <p><strong>Status:</strong> In Transit</p>
                    </div>
                </div>
            </div>
             -->
        </section>
        
        <section class="help-section">
            <h2 class="section-title"><i class="fas fa-question-circle"></i> Need Help With Your Order?</h2>
            <div class="help-options">
                <div class="help-option">
                    <div class="help-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="help-text">
                        <h3>Call Us</h3>
                        <p>+256 701 649961</p>
                    </div>
                </div>
                <div class="help-option">
                    <div class="help-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="help-text">
                        <h3>Email Support</h3>
                        <p>kiskentrends@gmail.com</p>
                    </div>
                </div>
                <div class="help-option">
                    <div class="help-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="help-text">
                        <h3>Live Chat</h3>
                        <p>Available 24/7</p>
                    </div>
                </div>
                <div class="help-option">
                    <div class="help-icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="help-text">
                        <h3>FAQ</h3>
                        <p>Common questions answered</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    

    <script>
        // Simple form submission handler
        document.querySelector('.track-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const orderNumber = document.querySelector('.track-input[type="text"]').value;
            const email = document.querySelector('.track-input[type="email"]').value;
            
            if(orderNumber && email) {
                alert(`Tracking order: ${orderNumber}\nA tracking update has been sent to ${email}`);
                // In a real application, you would make an API call here to fetch order details
            }
        });
    </script>
</body>
</html>