#!/usr/bin/env bash
# ForgeDesk Studio - Cloud Host Setup Script
# Run on a fresh Ubuntu 22.04/24.04 VPS as root
# Usage: bash setup.sh
set -euo pipefail

APP_ROOT="/var/www/forgedesk"
DOMAIN="${1:-example.com}"

echo "=== ForgeDesk Studio Server Setup ==="
echo "Domain: $DOMAIN"
echo ""

# --------- Step 1: System packages ---------
echo "[1/8] Installing system packages..."
apt-get update -qq
apt-get install -y -qq \
    nginx \
    php8.3-fpm \
    php8.3-cli \
    php8.3-sqlite3 \
    php8.3-mysql \
    php8.3-pgsql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-intl \
    php8.3-bcmath \
    php8.3-redis \
    php8.3-imagick \
    composer \
    supervisor \
    certbot python3-certbot-nginx \
    unzip git sqlite3

# --------- Step 2: PHP config ---------
echo "[2/8] Configuring PHP..."
PHP_CLI="/etc/php/8.3/cli/php.ini"
PHP_FPM="/etc/php/8.3/fpm/php.ini"
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 12M/' "$PHP_FPM"
sed -i 's/post_max_size = .*/post_max_size = 12M/' "$PHP_FPM"
sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_FPM"
sed -i 's/;opcache.enable=1/opcache.enable=1/' "$PHP_FPM"
systemctl restart php8.3-fpm

# --------- Step 3: Clone repo ---------
echo "[3/8] Cloning project..."
if [ -d "$APP_ROOT" ]; then
    echo "Directory exists, pulling updates..."
    cd "$APP_ROOT" && git pull origin main
else
    git clone https://github.com/NosaibaWebDev/forgedesk.git "$APP_ROOT"
fi

# --------- Step 4: Permissions ---------
echo "[4/8] Setting permissions..."
chown -R www-data:www-data "$APP_ROOT"
find "$APP_ROOT" -type f -exec chmod 644 {} \;
find "$APP_ROOT" -type d -exec chmod 755 {} \;
chmod -R 775 "$APP_ROOT/storage"
chmod -R 775 "$APP_ROOT/bootstrap/cache"
chmod +x "$APP_ROOT/artisan"

# --------- Step 5: Install deps ---------
echo "[5/8] Installing dependencies..."
cd "$APP_ROOT"
sudo -u www-data composer install --no-dev -q --optimize-autoloader --no-interaction

# --------- Step 6: Environment ---------
echo "[6/8] Setting up environment..."
if [ ! -f "$APP_ROOT/.env" ]; then
    # Generate app key
    APP_KEY=$(sudo -u www-data php "$APP_ROOT/artisan" key:generate --show --no-interaction)
    cp "$APP_ROOT/.env.production.example" "$APP_ROOT/.env"
    sed -i "s|YOUR_APP_KEY_HERE|$APP_KEY|" "$APP_ROOT/.env"
    sed -i "s|forgedesk.example.com|$DOMAIN|" "$APP_ROOT/.env"
    echo ""
    echo "=== MANUAL STEP ==="
    echo "Edit $APP_ROOT/.env and fill in:"
    echo "  - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
    echo "  - MAIL_* settings"
    echo "Then run: php artisan migrate --force"
fi

# Create storage symlink
sudo -u www-data php "$APP_ROOT/artisan" storage:link 2>/dev/null || true

# --------- Step 7: Build caches ---------
echo "[7/8] Building caches..."
sudo -u www-data php "$APP_ROOT/artisan" config:cache -q
sudo -u www-data php "$APP_ROOT/artisan" route:cache -q
sudo -u www-data php "$APP_ROOT/artisan" view:cache -q
sudo -u www-data php "$APP_ROOT/artisan" event:cache -q

# --------- Step 8: Nginx + SSL + Worker ---------
echo "[8/8] Configuring services..."

# Copy nginx config
cp "$APP_ROOT/.deploy/nginx.conf" "/etc/nginx/sites-available/forgedesk"
sed -i "s/forgedesk.example.com/$DOMAIN/g" "/etc/nginx/sites-available/forgedesk"
ln -sf "/etc/nginx/sites-available/forgedesk" "/etc/nginx/sites-enabled/"
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# Copy supervisor config
cp "$APP_ROOT/.deploy/forgedesk-worker.conf" "/etc/supervisor/conf.d/"
supervisorctl reread && supervisorctl update

# SSL certificate
echo ""
echo "=== SSL SETUP ==="
echo "After DNS propagates, run:"
echo "  certbot --nginx -d $DOMAIN"
echo "  systemctl reload nginx"
echo ""

echo "=== SETUP COMPLETE ==="
echo ""
echo "Next steps:"
echo "  1. Edit $APP_ROOT/.env with your DB/MAIL credentials"
echo "  2. Run: cd $APP_ROOT && php artisan migrate --force"
echo "  3. Create admin: php artisan app:create-admin"
echo "  4. Run certbot for SSL"
echo "  5. Verify: https://$DOMAIN"
