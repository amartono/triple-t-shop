# Triple T Shop (Tung Tung Tung Sahur)

A full-featured Sahur (pre-dawn Ramadan meal) e-commerce website built on WordPress + WooCommerce.

## One-Click Setup

### macOS

```bash
git clone https://github.com/amartono/triple-t-shop.git
cd triple-t-shop
bash install.sh
```

The script auto-installs Homebrew, PHP, MySQL, Apache, WordPress, WooCommerce, all plugins, products, and content. No configuration needed.

- **Website:** http://localhost:8080

### Windows (WSL2 Ubuntu)

```bash
git clone https://github.com/amartono/triple-t-shop.git
cd triple-t-shop
bash install-windows.sh
```

The script auto-installs Nginx, PHP, MariaDB, WordPress, WooCommerce, all plugins, products, and content. No configuration needed.

- **Website:** http://localhost

## Start / Stop

**macOS:**
```bash
brew services start mysql && brew services start httpd   # Start
brew services stop mysql && brew services stop httpd      # Stop
```

**Windows (WSL):**
```bash
bash start.sh     # Start nginx, MariaDB, PHP
bash stop.sh      # Stop all services
```

## Features

- 16 Sahur-themed products with playful names (Platriple T, Tungbler, Sahur Box)
- 2FA login with email OTP verification (Gmail SMTP)
- AI chatbot for product questions and shopping help
- AJAX product search with autocomplete
- Carousel homepage with blurred background images
- 7 product categories (Kitchen, Drinkware, Food Storage, Apparel, etc.)
- Coupons: SAHUR10 (10%), TRIPLET20 (20%), FREESHIP
- Payment: bank transfer, check, cash on delivery
- Free shipping (USA zone)
- Login required for checkout
- Responsive warm earthy theme (brown, amber, gold)

## Admin Account

| Field | Value |
|---|---|
| Username | `admin` |
| Password | *(reset after first login)* |

**macOS:**
```bash
wp user update 1 --user_pass=yournewpassword
```

**Windows (WSL):**
```bash
sudo -u www-data wp user update 1 --user_pass=yournewpassword
```

## Requirements

- macOS (Apple Silicon or Intel) — or — Windows 10/11 with WSL2 Ubuntu
- Internet connection (pulls dependencies)
- 2GB+ disk space
