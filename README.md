# Triple T Shop (Tung Tung Tung Sahur)

A fully-featured WordPress + WooCommerce e-commerce site for Sahur (pre-dawn Ramadan meal) essentials. Features a custom 2FA login system, AI chatbot, product search, and a warm earthy-themed design.

---

## Prerequisites

- **PHP** 7.4+ (8.0+ recommended)
- **MySQL** 5.7+ or MariaDB 10.3+
- **Apache** with `mod_rewrite` enabled (or Nginx)
- **Composer** (optional, for future package management)

## Quick Start

### 1. Clone the Repository
```bash
git clone <repo-url> triple-t-shop
cd triple-t-shop
```

### 2. Run the Setup Script
```bash
./setup.sh
```
This will:
- Copy `wp-config.example.php` → `wp-config.php`
- Copy `smtp.php.example` → `wp-content/mu-plugins/smtp.php`
- Copy `chatbot.js.example` → `wp-content/themes/twentytwentyfive/assets/js/chatbot.js`

### 3. Configure Database Credentials
Edit `wp-config.php` and update:
```php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'your_password_here' );
define( 'DB_HOST', 'localhost' );
```

### 4. Create & Import Database
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the database dump
mysql -u root -p wordpress < triple-t-shop-dump.sql
```

### 5. Configure Web Server
Point your Apache/Nginx document root to this directory:
```
DocumentRoot /path/to/triple-t-shop
```

Enable `mod_rewrite` in Apache:
```bash
sudo a2enmod rewrite
```

### 6. Start the Server
```bash
# Apache (macOS Homebrew)
brew services start httpd

# OR use PHP's built-in server (development only)
php -S localhost:8080
```

### 7. Visit the Site
Open `http://localhost:8080` in your browser.

### 8. Admin Login
- **URL:** `http://localhost:8080/wp-admin`
- **Username:** `admin`
- **Password:** *(change after first login via database or WP-CLI)*
```bash
wp user update 1 --user_pass=newpassword
```

---

## Project Structure

```
triple-t-shop/
├── wp-content/
│   ├── themes/twentytwentyfive/    # Customized Twenty Twenty-Five theme
│   │   ├── functions.php           # All custom PHP code
│   │   ├── style.css               # Custom CSS (~800 lines)
│   │   ├── templates/              # Custom block templates
│   │   ├── patterns/               # Header/footer patterns
│   │   ├── woocommerce/            # WooCommerce template overrides
│   │   └── assets/js/              # JavaScript files
│   ├── mu-plugins/                 # Must-use plugins
│   │   └── smtp.php                # Gmail SMTP configuration
│   └── plugins/woocommerce/        # WooCommerce plugin
├── setup.sh                        # Setup script
├── wp-config.example.php           # Config template
└── triple-t-shop-dump.sql          # Database dump
```

---

## Features

| Feature | Description |
|---------|-------------|
| **16 Products** | Sahur-themed essentials with creative names (Platriple T, Tungbler, Sahur Box) |
| **2FA Login** | Email OTP verification via Gmail SMTP |
| **AI Chatbot** | Groq-powered chatbot for product questions and cart operations |
| **Product Search** | AJAX autocomplete with partial matching |
| **Product Categories** | 7 categories (Kitchen & Dining, Drinkware, Food Storage, etc.) |
| **Coupons** | SAHUR10 (10%), TRIPLET20 (20%), FREESHIP |
| **Payment Methods** | Direct bank transfer, check payments, cash on delivery |
| **Shipping** | Free shipping (USA zone) |
| **Checkout Gate** | Guests redirected to login before checkout |
| **Dashboard** | Custom flexbox sidebar layout |
| **Homepage Carousel** | Blurred background image slider |
| **Responsive** | Mobile-friendly design |

---

## Optional Configuration

### SMTP (Email)
Edit `wp-content/mu-plugins/smtp.php`:
```php
$phpmailer->Username = 'your-email@gmail.com';
$phpmailer->Password = 'your-app-password';
```

### AI Chatbot
Get a free API key at [console.groq.com](https://console.groq.com/keys), then edit:
```javascript
// wp-content/themes/twentytwentyfive/assets/js/chatbot.js
var GROQ_KEY = 'your-groq-api-key';
```

### Custom Domain
Update all `http://localhost:8080` references in:
- `wp-content/themes/twentytwentyfive/patterns/header.php`
- `wp-content/themes/twentytwentyfive/patterns/footer.php`
- `wp-content/themes/twentytwentyfive/templates/home.html`

---

## Stopping the Server

```bash
# Apache
brew services stop httpd

# PHP built-in server
# Press Ctrl+C in the terminal window
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| **White screen** | Check `wp-config.php` DB credentials are correct |
| **404 on pages** | Enable `mod_rewrite` and restart Apache |
| **No emails sent** | Edit `smtp.php` with valid Gmail app password |
| **Chatbot not working** | Add Groq API key to `chatbot.js` |
| **Database import fails** | Ensure MySQL is running: `brew services start mysql` |
| **Images missing** | WordPress uploads are at `wp-content/uploads/` — included in git |

---

## Tech Stack

- **CMS:** WordPress 6.7+
- **E-commerce:** WooCommerce 10.7+
- **AI:** Groq API (Llama 3.3 70B)
- **Email:** Gmail SMTP
- **Font:** Manrope (variable weight)
- **Theme:** Twenty Twenty-Five (heavily customized)
