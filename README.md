# Triple T Shop (Tung Tung Tung Sahur)

A fully-featured WordPress + WooCommerce e-commerce site for Sahur (pre-dawn Ramadan meal) essentials. Features a custom 2FA login system, AI chatbot, product search, and a warm earthy-themed design.

---

## Fresh Machine Setup (Nothing Installed)

This guide assumes you have a **brand new Mac with nothing installed**. Follow every step in order.

### Step 1: Install Homebrew

Homebrew installs everything else. Paste this in Terminal:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

After it finishes, follow the "Next Steps" it prints (adds brew to your PATH).

### Step 2: Install PHP, MySQL, Apache & Git

```bash
brew install php mysql httpd git
```

### Step 3: Start MySQL & Apache

```bash
brew services start mysql
brew services start httpd
```

### Step 4: Create the Database

```bash
mysql -u root -e "CREATE DATABASE wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 5: Clone and Setup the Project

```bash
git clone https://github.com/amartono/triple-t-shop.git
cd triple-t-shop
./setup.sh
```

### Step 6: Edit Database Config

Open `wp-config.php` and update these lines:

```php
define( 'DB_NAME', 'wordpress' );   // keep as-is
define( 'DB_USER', 'root' );        // keep as-is (default macOS MySQL)
define( 'DB_PASSWORD', '' );        // keep empty (default macOS MySQL has no password)
define( 'DB_HOST', 'localhost' );   // keep as-is
```

### Step 7: Import the Database

```bash
mysql -u root wordpress < triple-t-shop-dump.sql
```

### Step 8: Point Apache to the Project

Edit Apache config to serve from this folder:

```bash
# Open config
nano /opt/homebrew/etc/httpd/httpd.conf

# Find "DocumentRoot" and change it to:
DocumentRoot "/path/to/triple-t-shop"

# Also find <Directory> and change the path to match

# Then enable mod_rewrite by uncommenting this line:
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so

# Restart Apache
brew services restart httpd
```

### Step 9: Open the Site

Go to **http://localhost:8080** in your browser.

---

## Quick Reference (After First Setup)

If you've already installed everything and just need to restart:

```bash
# Start services
brew services start mysql
brew services start httpd

# Stop services
brew services stop mysql
brew services stop httpd
```

---

## Admin Login

- **URL:** http://localhost:8080/wp-admin
- **Username:** `admin`
- **Password:** Reset it with:

```bash
wp user update 1 --user_pass=yournewpassword
```

---

## Features

| Feature | Description |
|---------|-------------|
| **16 Products** | Sahur-themed essentials (Platriple T, Tungbler, Sahur Box, etc.) |
| **2FA Login** | Email OTP verification via Gmail SMTP |
| **AI Chatbot** | Floating chat button — ask about products, add to cart |
| **Product Search** | AJAX autocomplete with partial matching |
| **7 Categories** | Kitchen & Dining, Drinkware, Food Storage, Apparel, etc. |
| **Coupons** | SAHUR10 (10%), TRIPLET20 (20%), FREESHIP |
| **Payment Methods** | Bank transfer, check, cash on delivery |
| **Free Shipping** | USA zone |
| **Checkout Gate** | Guests must login to checkout |
| **Dashboard** | Sidebar layout (orders, addresses, downloads) |
| **Carousel** | Blurred background image slider on homepage |

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| **"command not found: brew"** | Homebrew didn't install. Go back to Step 1. |
| **"command not found: mysql"** | Run `brew install mysql` |
| **MySQL won't start** | Run `brew services restart mysql` |
| **Apache won't start** | Run `brew services restart httpd` |
| **White screen on site** | Check `wp-config.php` has correct DB credentials |
| **Database import fails** | Make sure MySQL is running: `brew services start mysql` |
| **404 on pages** | Enable mod_rewrite in Apache config (Step 8) |
| **"Error establishing database connection"** | MySQL isn't running or DB credentials are wrong |
| **Images broken** | Complete WordPress install — visit http://localhost:8080/wp-admin once |
