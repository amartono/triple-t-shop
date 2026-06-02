#!/bin/bash
# ============================================================
# Triple T Shop (Tung Tung Tung Sahur) — Setup Script
# ============================================================
# Run this after cloning the repository to set up the site.
# Usage: ./setup.sh
# ============================================================

set -e
echo "========================================"
echo " Triple T Shop — Setup"
echo "========================================"
echo ""

# --- 1. Create config files from templates ---
if [ ! -f wp-config.php ]; then
    cp wp-config.example.php wp-config.php
    echo "[✓] Created wp-config.php from template"
    echo "    → Edit wp-config.php with your database credentials"
else
    echo "[i] wp-config.php already exists — skipping"
fi

if [ ! -f wp-content/mu-plugins/smtp.php ]; then
    if [ -f wp-content/mu-plugins/smtp.php.example ]; then
        cp wp-content/mu-plugins/smtp.php.example wp-content/mu-plugins/smtp.php
        echo "[✓] Created smtp.php from template"
        echo "    → Edit smtp.php with your Gmail credentials (optional)"
    fi
else
    echo "[i] smtp.php already exists — skipping"
fi

if [ ! -f wp-content/themes/twentytwentyfive/assets/js/chatbot.js ]; then
    if [ -f wp-content/themes/twentytwentyfive/assets/js/chatbot.js.example ]; then
        cp wp-content/themes/twentytwentyfive/assets/js/chatbot.js.example wp-content/themes/twentytwentyfive/assets/js/chatbot.js
        echo "[✓] Created chatbot.js from template"
        echo "    → Edit chatbot.js with your Groq API key (optional)"
    fi
else
    echo "[i] chatbot.js already exists — skipping"
fi

# --- 2. Set file permissions ---
chmod -R 755 wp-content 2>/dev/null
echo "[✓] Permissions set"

echo ""
echo "========================================"
echo " Setup Complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo ""
echo "1. Create the database:"
echo "   mysql -u root -p -e \"CREATE DATABASE wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
echo ""
echo "2. Import the database dump:"
echo "   mysql -u root -p wordpress < triple-t-shop-dump.sql"
echo ""
echo "3. Edit wp-config.php with your database credentials:"
echo "   DB_NAME, DB_USER, DB_PASSWORD, DB_HOST"
echo ""
echo "4. Start your web server:"
echo "   brew services start httpd    (macOS Apache)"
echo "   php -S localhost:8080        (PHP built-in, dev only)"
echo ""
echo "5. Open http://localhost:8080 in your browser"
echo ""
echo "6. Login to admin at http://localhost:8080/wp-admin"
echo "   Username: admin"
echo "   Reset password with: wp user update 1 --user_pass=newpassword"
echo ""
echo "Optional:"
echo "• Edit smtp.php for email (2FA codes)"
echo "• Edit chatbot.js for AI chatbot (Groq API key)"
echo "• Update localhost URLs if using a different port/domain"
