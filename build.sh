#!/usr/bin/env bash
# exit on error
set -o errexit

echo "==> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Installing Node.js dependencies (including dev dependencies for build)..."
npm ci

echo "==> Building frontend assets..."
npm run build

# Verify build succeeded
if [ ! -f "public/build/manifest.json" ]; then
    echo "ERROR: Build failed - manifest.json not found"
    exit 1
fi

echo "==> Build verification passed"

echo "==> Setting up storage directories..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "==> Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "==> Running database migrations..."
php artisan migrate --force --no-interaction

echo "==> Creating guest database..."
# Check if using PostgreSQL for guest data (production) or SQLite (development)
if [ "${GUEST_DB_CONNECTION}" = "guest_pgsql" ]; then
    echo "Using PostgreSQL for guest data..."
    php artisan migrate --database=guest_pgsql --force --no-interaction
else
    echo "Using SQLite for guest data..."
    touch database/guest_database.sqlite
    chmod 664 database/guest_database.sqlite
    php artisan migrate --database=guest_sqlite --force --no-interaction
fi

echo "==> Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:clear

echo "==> Build completed successfully!"

