# Kisken Trends Duuka — E-Commerce Platform

**Kisken Trends Duuka (KSD)** is a full-featured shoe e-commerce web application built with PHP, MySQL (PDO), HTML/CSS, and JavaScript. It supports customer shopping, order management, a blog, and a comprehensive admin panel.

---

## Table of Contents

- [Features](#features)
- [Project Structure](#project-structure)
- [Tech Stack](#tech-stack)
- [Installation & Setup](#installation--setup)
- [Database Configuration](#database-configuration)
- [User Roles](#user-roles)
- [Pages & Modules](#pages--modules)
- [API Endpoints](#api-endpoints)
- [Security Notes](#security-notes)
- [Contributing](#contributing)

---

## Features

### Customer-Facing

- Browse products by category: Men, Women, Kids, Sneakers, Heels, Flats, Boots, Formal
- Add to cart and update quantities
- User registration and login with session management
- Place orders and track order status
- Newsletter subscription
- Blog with sneaker articles and read-more posts
- Contact form with message submission
- Delivery info, Privacy Policy, and Terms pages

### Admin Panel

- Admin dashboard with revenue stats, total orders, product count, and recent orders
- Orders-per-day and revenue-per-day charts (last 30 days)
- Product management: add, edit, delete products with image upload
- Order management: view, edit status, delete orders
- Blog management: create and manage blog posts
- Shop and homepage content management
- Contact messages management

---

## Project Structure

```
ecommerce/
├── config/
│   └── db.php                  # PDO database connection
├── classes/
│   ├── product.php             # Product class (add, edit, delete, slugify)
│   └── category.php            # Category class
├── api/
│   ├── login.php               # Login handler
│   ├── register.php            # Registration handler
│   ├── add_to_cart.php         # Cart addition
│   ├── update_cart.php         # Cart update
│   ├── remove_from_cart.php    # Cart removal
│   ├── cart.php                # Cart data fetch
│   ├── get_products.php        # Product listing API
│   ├── get_single_product.php  # Single product API
│   └── subscribe.php           # Newsletter subscription
├── index.php                   # Home page (dynamic sections from DB)
├── Shop.php                    # Main shop/product listing
├── product-details.php         # Single product detail page
├── cart.php                    # Shopping cart
├── checkout.php                # Checkout page
├── payment.php                 # Payment page
├── payment_callback.php        # Payment callback handler
├── process_order.php           # Order processing
├── place_order.php             # Order placement
├── orders.php                  # Customer order history
├── track-my-order.php          # Order tracking
├── view_receipt.php            # Order receipt
├── login.php                   # Login page
├── register.php                # Registration page
├── logout.php                  # Session logout
├── About.php                   # About page
├── contact.php                 # Contact page
├── Contact_submit.php          # Contact form handler
├── blogs.php                   # Blog listing
├── post.php                    # Individual blog post
├── read-more.php               # Blog read-more
├── delivery.php                # Delivery information
├── privacy-policy.php          # Privacy policy
├── terms.php                   # Terms and conditions
├── help.html                   # Help page
├── men.php                     # Men's shoes
├── women.php                   # Women's shoes
├── kids.php                    # Kids' shoes
├── sneakers.php                # Sneakers
├── Heels.php                   # Heels
├── Flats.php                   # Flats
├── gental.php                  # Formal / Gentle shoes
├── Men_boots.php               # Men's boots
├── Women_boots.php             # Women's boots
├── admin_dashboard.php         # Admin dashboard
├── admin_index.php             # Admin homepage manager
├── admin_products.php          # Admin product list
├── add_product.php             # Add new product
├── edit_product.php            # Edit product
├── delete_product.php          # Delete product
├── admin_orders.php            # Admin order manager
├── edit_order.php              # Edit order
├── delete_order.php            # Delete order
├── update_order_status.php     # Update order status
├── admin_shop.php              # Admin shop manager
├── admin_contact.php           # Admin contact messages
├── Admin_about.php             # Admin about page manager
├── blog_admin.php              # Admin blog manager
├── blog_functions.php          # Blog helper functions
├── images/                     # General images
├── blog/                       # Blog post images (sneakers1–8)
├── men-shoes/                  # Men's shoe images
├── shoes images/               # Main product images
└── kids images/                # Kids' shoe images
```

---

## Tech Stack

| Layer    | Technology                        |
| -------- | --------------------------------- |
| Backend  | PHP 7.4+ with PDO                 |
| Database | MySQL                             |
| Frontend | HTML5, CSS3, JavaScript (vanilla) |
| Icons    | Font Awesome 6                    |
| Auth     | PHP Sessions                      |
| Payment  | Custom callback handler           |

---

## Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- A local server environment: **XAMPP**, **WAMP**, or **Laragon**

### Steps

1. **Clone or extract** the project into your server's web root:

   ```
   /xampp/htdocs/ecommerce/
   ```

2. **Create the database** in phpMyAdmin or MySQL CLI:

   ```sql
   CREATE DATABASE ecommerce;
   ```

3. **Import the SQL schema** (if a `.sql` dump is available) or create tables manually based on the queries referenced in the PHP files.

4. **Configure the database connection** in `config/db.php`:

   ```php
   $host     = "localhost";
   $dbname   = "ecommerce";
   $username = "root";
   $password = "Bibidong256#";
   ```

5. **Start** Apache and MySQL services, then visit:
   ```
   http://localhost/ecommerce/index.php
   ```

---

## Database Configuration

The file `config/db.php` establishes a PDO connection used throughout the application. Key database tables referenced include:

| Table                 | Purpose                                   |
| --------------------- | ----------------------------------------- |
| `users`               | Customer accounts and roles               |
| `products`            | Product listings with price, stock, image |
| `categories`          | Product categories                        |
| `product_categories`  | Product–category relationships            |
| `orders`              | Customer orders                           |
| `order_items`         | Individual items within orders            |
| `hero`                | Homepage hero section content             |
| `featured_products`   | Featured products on homepage             |
| `home_categories`     | Homepage category and trend sections      |
| `trending_tags`       | Trending tags displayed on homepage       |
| `feature_boxes`       | Homepage feature highlight boxes          |
| `main_banner`         | Main promotional banner                   |
| `small_banners`       | Small promotional banners                 |
| `seasonal_banners`    | Seasonal campaign banners                 |
| `newsletter_settings` | Newsletter section config                 |

> ⚠️ **Security:** Remove or rotate the hardcoded database password in `config/db.php` before deploying to any public or production server.

---

## User Roles

| Role     | Access                                                      |
| -------- | ----------------------------------------------------------- |
| Guest    | Browse products, view pages, view blog                      |
| Customer | Register/login, cart, checkout, orders, order tracking      |
| Admin    | Full admin panel — products, orders, blog, homepage content |

Admin access is enforced via PHP session check:

```php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}
```

---

## Pages & Modules

### Public Pages

| Page           | File                  | Description                              |
| -------------- | --------------------- | ---------------------------------------- |
| Home           | `index.php`           | Dynamic hero, featured products, banners |
| Shop           | `Shop.php`            | Full product listing                     |
| Product Detail | `product-details.php` | Individual product page                  |
| Cart           | `cart.php`            | Shopping cart                            |
| Checkout       | `checkout.php`        | Order checkout                           |
| Payment        | `payment.php`         | Payment form & summary                   |
| Orders         | `orders.php`          | Customer order history                   |
| Track Order    | `track-my-order.php`  | Order status tracker                     |
| Blog           | `blogs.php`           | Blog listing                             |
| Contact        | `contact.php`         | Contact form                             |
| About          | `About.php`           | About the store                          |

### Category Pages

Men · Women · Kids · Sneakers · Heels · Flats · Boots · Formal

### Admin Pages

Dashboard · Products · Orders · Shop Manager · Blog · Contact Messages · About Manager

---

## API Endpoints

All endpoints are located in the `/api/` directory and handle POST requests.

| Endpoint                     | Method | Description                 |
| ---------------------------- | ------ | --------------------------- |
| `api/login.php`              | POST   | Authenticate user           |
| `api/register.php`           | POST   | Register new user           |
| `api/add_to_cart.php`        | POST   | Add product to cart         |
| `api/update_cart.php`        | POST   | Update cart item quantity   |
| `api/remove_from_cart.php`   | POST   | Remove item from cart       |
| `api/cart.php`               | GET    | Fetch current cart contents |
| `api/get_products.php`       | GET    | Fetch product list          |
| `api/get_single_product.php` | GET    | Fetch single product by ID  |
| `api/subscribe.php`          | POST   | Subscribe to newsletter     |

---

## Security Notes

> These are recommended improvements before any public deployment:

- **Change the database password** — `config/db.php` currently contains a plaintext credential.
- **Use environment variables** (e.g., `.env` file with `vlucas/phpdotenv`) instead of hardcoded credentials.
- **Add CSRF protection** to all forms.
- **Validate and sanitize** all user inputs server-side.
- **Restrict admin routes** further using middleware or a central auth check file.
- **Use HTTPS** in production.
- **Store uploaded images** outside the web root or restrict file types strictly.

---

## Contributing

1. Fork or clone the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit changes: `git commit -m "Add your feature"`
4. Push and open a pull request

---

> Built with ❤️ — Kisken Trends Duuka | A sneaker & shoe e-commerce platform
