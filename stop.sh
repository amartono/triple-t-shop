#!/bin/bash
# Stop all services for Triple T Shop
sudo service mysql stop 2>/dev/null || sudo service mariadb stop
sudo service php8.*-fpm stop 2>/dev/null || sudo service php-fpm stop
sudo service nginx stop
echo "Triple T Shop stopped"
