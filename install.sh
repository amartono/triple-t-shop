#!/bin/bash
# ============================================================
# Triple T Shop — One-Command Installer
# ============================================================
# Runs on a fresh Mac. Installs everything and launches the site.
# Usage: curl -sSL https://raw.githubusercontent.com/amartono/triple-t-shop/main/install.sh | bash
#    or: git clone https://github.com/amartono/triple-t-shop.git && cd triple-t-shop && bash install.sh
# ============================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}"
echo "╔══════════════════════════════════════════╗"
echo "║   Triple T Shop — One-Command Installer  ║"
echo "║   Tung Tung Tung Sahur                   ║"
echo "╚══════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

# ── 1. Install Homebrew ──────────────────────────
if ! command -v brew &> /dev/null; then
    echo -e "${YELLOW}[1/7] Installing Homebrew...${NC}"
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    eval "$(/opt/homebrew/bin/brew shellenv)"
else
    echo -e "${GREEN}[1/7] Homebrew already installed${NC}"
fi

# ── 2. Install dependencies ──────────────────────
echo -e "${YELLOW}[2/7] Installing PHP, MySQL, Apache, Git...${NC}"
brew install php mysql httpd git 2>/dev/null || true
echo -e "${GREEN}[2/7] Dependencies ready${NC}"

# ── 3. Start services ────────────────────────────
echo -e "${YELLOW}[3/7] Starting MySQL & Apache...${NC}"
brew services start mysql 2>/dev/null || true
brew services start httpd 2>/dev/null || true
sleep 2
echo -e "${GREEN}[3/7] Services running${NC}"

# ── 4. Clone or use current directory ────────────
REPO_URL="https://github.com/amartono/triple-t-shop.git"
PROJECT_DIR="$HOME/triple-t-shop"

if [ -f "wp-config.example.php" ]; then
    PROJECT_DIR="$(pwd)"
    echo -e "${GREEN}[4/7] Already in project directory${NC}"
else
    echo -e "${YELLOW}[4/7] Cloning Triple T Shop...${NC}"
    git clone "$REPO_URL" "$PROJECT_DIR"
    cd "$PROJECT_DIR"
    echo -e "${GREEN}[4/7] Project cloned${NC}"
fi

# ── 5. Setup config files ────────────────────────
echo -e "${YELLOW}[5/7] Creating configuration files...${NC}"
bash setup.sh 2>/dev/null || true

# Auto-configure wp-config.php for default macOS MySQL
if [ -f wp-config.php ]; then
    sed -i '' "s/define( 'DB_PASSWORD', .*/define( 'DB_PASSWORD', '' );/" wp-config.php
    echo -e "${GREEN}[5/7] wp-config.php configured for local MySQL${NC}"
else
    echo -e "${RED}[5/7] wp-config.php not found — check setup.sh${NC}"
    exit 1
fi

# ── 6. Create database & import ──────────────────
echo -e "${YELLOW}[6/7] Setting up database...${NC}"
mysql -u root -e "CREATE DATABASE IF NOT EXISTS wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
mysql -u root wordpress < triple-t-shop-dump.sql 2>/dev/null
echo -e "${GREEN}[6/7] Database imported (16 products, 7 pages)${NC}"

# ── 7. Configure Apache ──────────────────────────
echo -e "${YELLOW}[7/7] Configuring Apache...${NC}"

HTTPD_CONF="/opt/homebrew/etc/httpd/httpd.conf"

if [ -f "$HTTPD_CONF" ]; then
    # Enable mod_rewrite
    sed -i '' 's/#LoadModule rewrite_module/LoadModule rewrite_module/' "$HTTPD_CONF" 2>/dev/null || true

    # Create a VirtualHost for the project
    VHOST_CONF="/opt/homebrew/etc/httpd/extra/httpd-vhosts.conf"
    if [ -f "$VHOST_CONF" ]; then
        cat > "$VHOST_CONF" << APACHEEOF
<VirtualHost *:8080>
    DocumentRoot "$PROJECT_DIR"
    <Directory "$PROJECT_DIR">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog "/opt/homebrew/var/log/httpd/triple-t-error.log"
    CustomLog "/opt/homebrew/var/log/httpd/triple-t-access.log" common
</VirtualHost>
APACHEEOF
        # Uncomment vhosts include
        sed -i '' 's|#Include /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf|Include /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf|' "$HTTPD_CONF" 2>/dev/null || true
    fi

    # Listen on port 8080
    if ! grep -q "Listen 8080" "$HTTPD_CONF"; then
        echo "Listen 8080" >> "$HTTPD_CONF"
    fi

    brew services restart httpd 2>/dev/null || true
    echo -e "${GREEN}[7/7] Apache configured${NC}"
else
    echo -e "${YELLOW}[7/7] Apache config not found — using PHP built-in server${NC}"
    echo "Starting PHP server on http://localhost:8080 ..."
    php -S localhost:8080 &
fi

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Triple T Shop is now running!          ║${NC}"
echo -e "${GREEN}║                                          ║${NC}"
echo -e "${GREEN}║   🌐 http://localhost:8080                ║${NC}"
echo -e "${GREEN}║   🔑 Admin: http://localhost:8080/wp-admin ║${NC}"
echo -e "${GREEN}║   👤 Username: admin                      ║${NC}"
echo -e "${GREEN}║   🔒 Reset password:                      ║${NC}"
echo -e "${GREEN}║      wp user update 1 --user_pass=YOURPASS║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""
echo "To stop: brew services stop httpd && brew services stop mysql"
