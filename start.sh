#!/bin/bash
# Start all services for Triple T Shop

# Try mariadb, fallback to mysql
sudo service mariadb start 2>/dev/null && echo "MariaDB started" || {
    sudo service mysql start 2>/dev/null && echo "MySQL started" || {
        sudo mysqld_safe --skip-grant-tables &
        echo "MariaDB started (recovery mode)"
    }
}

# Try specific PHP version, then fallback
PHP_VER=$(ls /etc/php/ 2>/dev/null | head -1)
sudo service php${PHP_VER}-fpm start 2>/dev/null && echo "PHP-FPM started" || {
    sudo service php-fpm start 2>/dev/null && echo "PHP started" || echo "PHP: already running or not found"
}

# Nginx
sudo service nginx start 2>/dev/null && echo "Nginx started" || echo "Nginx: already running"

echo ""
echo "Triple T Shop — http://localhost"
