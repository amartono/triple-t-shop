# Triple T Shop (Tung Tung Tung Sahur)

A full-featured Sahur (pre-dawn Ramadan meal) e-commerce website built on WordPress + WooCommerce.

## One-Click Setup

Clone and run on any fresh Mac:

```bash
git clone https://github.com/amartono/triple-t-shop.git
cd triple-t-shop
bash install.sh
```

The script auto-installs everything — Homebrew, PHP, MySQL, Apache, WordPress, WooCommerce, all plugins, products, and content. No configuration needed.

When it finishes:

- **Website:** http://localhost:8080
- **Admin panel:** http://localhost:8080/wp-admin

## Start / Stop

```bash
brew services start mysql && brew services start httpd   # Start
brew services stop mysql && brew services stop httpd      # Stop
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

Reset the password:

```bash
wp user update 1 --user_pass=yournewpassword
```

## Requirements

- macOS (Apple Silicon or Intel)
- Internet connection (pulls Homebrew + dependencies)
- 2GB+ disk space
