#!/bin/bash
# Triple T Shop - Setup script for new deployments
# Run this after cloning the repo

echo "=== Triple T Shop Setup ==="

# Copy example config files
if [ ! -f wp-config.php ]; then
    cp wp-config.example.php wp-config.php
    echo "Created wp-config.php from template - please edit it with your DB credentials"
fi

if [ ! -f wp-content/mu-plugins/smtp.php ]; then
    cp wp-content/mu-plugins/smtp.php.example wp-content/mu-plugins/smtp.php
    echo "Created smtp.php from template - please add your Gmail credentials"
fi

if [ ! -f wp-content/themes/twentytwentyfive/assets/js/chatbot.js ]; then
    cp wp-content/themes/twentytwentyfive/assets/js/chatbot.js.example wp-content/themes/twentytwentyfive/assets/js/chatbot.js
    echo "Created chatbot.js from template - please add your Groq API key"
fi

# Set permissions
chmod -R 755 wp-content
chmod 644 wp-config.php 2>/dev/null

echo ""
echo "Setup complete! Next steps:"
echo "1. Edit wp-config.php with your database credentials"
echo "2. Edit wp-content/mu-plugins/smtp.php with your Gmail credentials"
echo "3. Edit wp-content/themes/twentytwentyfive/assets/js/chatbot.js with your Groq API key"
echo "4. Import the database from db-dump.sql (if available)"
echo "5. Visit http://localhost:8080"
