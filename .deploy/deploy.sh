#!/usr/bin/env bash
# ForgeDesk Studio - Production Deployment Script
# Run as the web server user (e.g. www-data) or use sudo where needed
set -euo pipefail

APP_ROOT="/var/www/forgedesk"
WEB_USER="www-data"

echo "=== ForgeDesk Studio Deployment ==="

# 1. Set correct file ownership
echo "[1/6] Setting file ownership..."
sudo chown -R "$WEB_USER:$WEB_USER" "$APP_ROOT"

# 2. Set secure file permissions
echo "[2/6] Setting file permissions..."
find "$APP_ROOT" -type f -exec chmod 644 {} \;
find "$APP_ROOT" -type d -exec chmod 755 {} \;
chmod -R 775 "$APP_ROOT/storage"
chmod -R 775 "$APP_ROOT/bootstrap/cache"
chmod +x artisan

# 3. Install dependencies (production only)
echo "[3/6] Installing composer dependencies..."
cd "$APP_ROOT"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 4. Run database migrations
echo "[4/6] Running database migrations..."
php artisan migrate --force --no-interaction

# 5. Build caches
echo "[5/6] Building caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart queue workers
echo "[6/6] Restarting queue workers..."
php artisan queue:restart

echo "=== Deployment complete ==="
