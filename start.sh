#!/bin/bash
# Start all services for Triple T Shop
sudo service mysql start 2>/dev/null || sudo service mariadb start
sudo service php8.*-fpm start 2>/dev/null || sudo service php-fpm start
sudo service nginx start
echo "Triple T Shop started — http://localhost"
