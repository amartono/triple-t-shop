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
RED='\033[0;31m'
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

# ── 2. Initialize MariaDB ──────────────────────
echo -e "${YELLOW}[2/5] Setting up MariaDB...${NC}"
sudo service mariadb start 2>/dev/null || sudo service mysql start 2>/dev/null || {
    echo -e "${YELLOW}Initializing MariaDB...${NC}"
    sudo mysql_install_db 2>/dev/null || true
    sudo service mariadb start 2>/dev/null || sudo mysqld_safe --skip-grant-tables &
    sleep 3
}

# Make sure root can access without password
sudo mysql -u root -e "SELECT 1;" 2>/dev/null || {
    echo -e "${YELLOW}Configuring MariaDB root access...${NC}"
    sudo service mariadb stop 2>/dev/null
    sudo mysqld_safe --skip-grant-tables &
    sleep 3
    sudo mysql -u root -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY ''; FLUSH PRIVILEGES;" 2>/dev/null || true
    sudo killall mysqld 2>/dev/null
    sleep 2
    sudo service mariadb start 2>/dev/null || sudo service mysql start 2>/dev/null
}

echo -e "${GREEN}[2/5] MariaDB running${NC}"

# ── 3. Start PHP & Nginx ──────────────────────
echo -e "${YELLOW}[3/5] Starting services...${NC}"
PHP_VER=$(ls /etc/php/ 2>/dev/null | head -1)
sudo service php${PHP_VER}-fpm start 2>/dev/null || sudo service php-fpm start 2>/dev/null || true
sudo service nginx start 2>/dev/null || true

# ── 4. Move project ────────────────────────────
echo -e "${YELLOW}[4/5] Installing Triple T Shop...${NC}"
sudo mkdir -p /var/www
sudo rm -rf "$WWW_DIR" 2>/dev/null
sudo cp -r "$PROJECT_DIR" "$WWW_DIR"
sudo chown -R www-data:www-data "$WWW_DIR"

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

echo -e "${GREEN}[4/5] WordPress installed${NC}"

# ── 5. Configure Nginx ─────────────────────────
echo -e "${YELLOW}[5/5] Configuring Nginx...${NC}"

PHP_SOCK=$(find /var/run/php/ -name "*.sock" 2>/dev/null | head -1)
[ -z "$PHP_SOCK" ] && PHP_SOCK="unix:/var/run/php/php${PHP_VER}-fpm.sock"

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
        fastcgi_pass $PHP_SOCK;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINXEOF

sudo ln -sf /etc/nginx/sites-available/triple-t-shop /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo service nginx restart 2>/dev/null || sudo nginx -s reload 2>/dev/null || true

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Triple T Shop is now running!          ║${NC}"
echo -e "${GREEN}║                                          ║${NC}"
echo -e "${GREEN}║   🌐 http://localhost                     ║${NC}"
echo -e "${GREEN}║   🔑 Admin: http://localhost/wp-admin      ║${NC}"
echo -e "${GREEN}║   👤 Username: admin                      ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
