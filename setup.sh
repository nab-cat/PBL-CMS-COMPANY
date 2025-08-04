#!/bin/bash

set -e

echo "=== Laravel Project Requirements Setup for Ubuntu ==="

# Pilih web server: nginx atau apache2
read -p "Pilih web server (nginx/apache) [nginx]: " WEBSERVER
WEBSERVER=${WEBSERVER:-nginx}

echo "[1/7] Update package list..."
sudo apt-get update

echo "[2/7] Install curl, unzip, git, software-properties-common..."
sudo apt-get install -y curl unzip git software-properties-common

echo "[3/7] Install PHP 8.3 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php8.3 php8.3-cli php8.3-fpm php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-mysql php8.3-sqlite3

echo "[4/7] Install Composer..."
EXPECTED_SIGNATURE="$(curl -s https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    echo 'ERROR: Invalid Composer installer signature'
    rm composer-setup.php
    exit 1
fi
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

echo "[5/7] Install Database (MySQL server & SQLite)..."
sudo apt-get install -y mysql-server sqlite3

echo "[6/7] Install Node.js (latest LTS) & npm..."
# Official NodeSource - always fetches latest LTS/Current
curl -fsSL https://deb.nodesource.com/setup_current.x | sudo -E bash -
sudo apt-get install -y nodejs

echo "[7/7] Install Web Server ($WEBSERVER)..."
if [[ "$WEBSERVER" == "apache" ]]; then
    sudo apt-get install -y apache2 libapache2-mod-php8.3
else
    sudo apt-get install -y nginx
fi

echo ""
echo "=== Semua software requirement untuk Laravel berhasil diinstall! ==="
echo ""
echo "Versi PHP:"; php -v | head -n 1
echo "Versi Composer:"; composer --version
echo "Versi Node.js:"; node -v
echo "Versi npm:"; npm -v
echo "Versi MySQL:"; mysql --version
echo "Versi SQLite:"; sqlite3 --version
echo "Web server terinstall: $WEBSERVER"
echo ""
echo "Silakan lanjutkan dengan setup project Laravel Anda."