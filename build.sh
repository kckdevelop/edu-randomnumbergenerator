#!/usr/bin/env bash
# Exit on error
set -e

echo "==> [1/5] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> [2/5] Building frontend assets with Vite..."
npm install
npm run build

echo "==> [3/5] Preparing database & storage directories..."
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
    mkdir -p database
    touch database/database.sqlite
fi

echo "==> [4/5] Running database migrations & seeders..."
CACHE_STORE=file php artisan migrate --force
CACHE_STORE=file php artisan db:seed --force

echo "==> [5/5] Optimizing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Deployment build completed successfully!"
