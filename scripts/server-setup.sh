#!/bin/bash
# Riwaq — Server Setup Script
# Run once on a fresh Ubuntu 24.04 server
# Usage: bash server-setup.sh

set -e

echo "=== Riwaq Server Setup ==="

# 1. System updates
apt-get update && apt-get upgrade -y

# 2. Install PHP 8.2 + extensions
apt-get install -y \
    php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
    php8.2-gd php8.2-bcmath php8.2-intl

# 3. Install MySQL 8
apt-get install -y mysql-server-8.0
systemctl enable mysql

# 4. Install Redis
apt-get install -y redis-server
systemctl enable redis-server

# 5. Install Nginx
apt-get install -y nginx certbot python3-certbot-nginx
systemctl enable nginx

# 6. Install Supervisor
apt-get install -y supervisor
systemctl enable supervisor

# 7. Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 8. Create application user and directory
useradd -r -s /bin/bash www-Riwaq || true
mkdir -p /var/www/Riwaq
chown www-data:www-data /var/www/Riwaq

# 9. MySQL setup
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS Riwaq_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'Riwaq_user'@'localhost' IDENTIFIED BY '${DB_PASSWORD:-CHANGE_ME}';
GRANT ALL PRIVILEGES ON Riwaq_production.* TO 'Riwaq_user'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "=== Server setup complete ==="
echo "Next: Clone repo to /var/www/Riwaq and run:"
echo "  cp .env.production.example .env"
echo "  nano .env  # fill in all values"
echo "  php artisan app:setup-production"