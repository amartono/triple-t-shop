#!/bin/bash
# ============================================================
# Triple T Shop — One-Command Installer (Windows WSL / Linux)
# ============================================================
# Usage: git clone https://github.com/amartono/triple-t-shop.git
#        cd triple-t-shop && bash install-windows.sh
# ============================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}"
echo "╔══════════════════════════════════════════╗"
echo "║  Triple T Shop — Installer (WSL/Linux)   ║"
echo "║  Tung Tung Tung Sahur                    ║"
echo "╚══════════════════════════════════════════╝"
echo -e "${NC}"

PROJECT_DIR="$(pwd)"
WWW_DIR="/var/www/triple-t-shop"

# ── 1. Install packages ────────────────────────
echo -e "${YELLOW}[1/5] Installing Nginx, PHP, MariaDB...${NC}"
sudo apt update -y
sudo apt install -y nginx php-fpm php-mysql php-curl php-xml php-mbstring php-zip php-gd mariadb-server git curl unzip

# ── 2. Start services ──────────────────────────
echo -e "${YELLOW}[2/5] Starting services...${NC}"
sudo service mysql start 2>/dev/null || sudo service mariadb start
sudo service php8.*-fpm start 2>/dev/null || sudo service php-fpm start
sudo service nginx start

# ── 3. Move project to /var/www ────────────────
echo -e "${YELLOW}[3/5] Moving project to /var/www...${NC}"
sudo mkdir -p /var/www
sudo rm -rf "$WWW_DIR" 2>/dev/null
sudo cp -r "$PROJECT_DIR" "$WWW_DIR"
sudo chown -R www-data:www-data "$WWW_DIR"

# ── 4. Setup WordPress ─────────────────────────
echo -e "${YELLOW}[4/5] Setting up WordPress...${NC}"
cd "$WWW_DIR"

# Create wp-config.php
sudo -u www-data cp wp-config.example.php wp-config.php
sudo -u www-data sed -i "s/define( 'DB_PASSWORD', .*/define( 'DB_PASSWORD', '' );/" wp-config.php

# Create and import database
sudo mysql -u root -e "CREATE DATABASE IF NOT EXISTS wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
sudo mysql -u root wordpress < triple-t-shop-dump.sql 2>/dev/null

# Fix permissions
sudo chown -R www-data:www-data "$WWW_DIR"
sudo chmod -R 755 "$WWW_DIR/wp-content"

# ── 5. Configure Nginx ─────────────────────────
echo -e "${YELLOW}[5/5] Configuring Nginx...${NC}"

sudo tee /etc/nginx/sites-available/triple-t-shop > /dev/null << NGINXEOF
server {
    listen 80;
    server_name localhost;
    root $WWW_DIR;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.*-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINXEOF

sudo ln -sf /etc/nginx/sites-available/triple-t-shop /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo service nginx restart

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Triple T Shop is now running!          ║${NC}"
echo -e "${GREEN}║                                          ║${NC}"
echo -e "${GREEN}║   🌐 http://localhost                     ║${NC}"
echo -e "${GREEN}║   🔑 Admin: http://localhost/wp-admin      ║${NC}"
echo -e "${GREEN}║   👤 Username: admin                      ║${NC}"
echo -e "${GREEN}║   🔒 Run this to reset password:           ║${NC}"
echo -e "${GREEN}║      sudo -u www-data wp user update 1 --user_pass=YOURPASS ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
