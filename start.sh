#!/bin/sh
set -e

echo "Starting Taskware application..."

# Run migrations
php artisan migrate --force --no-interaction

# Run guest database migrations based on configuration
if [ "${GUEST_DB_CONNECTION}" = "guest_pgsql" ]; then
    php artisan migrate --database=guest_pgsql --force --no-interaction
else
    php artisan migrate --database=guest_sqlite --force --no-interaction
fi

# Clear all caches first to ensure fresh component registration
php artisan optimize:clear

# Cache configuration (this will re-register components via AppServiceProvider)
php artisan config:cache
php artisan route:cache

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground
echo "Application started successfully!"
exec nginx -g "daemon off;"

