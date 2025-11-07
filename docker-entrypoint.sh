#!/bin/sh
set -e

# Set PORT from environment (Render provides this)
export PORT=${PORT:-10000}

# Configure Apache to listen on the PORT
echo "Listen $PORT" > /etc/apache2/ports.conf

# Update Apache virtual host to use PORT
sed -i "s/\${PORT:-10000}/$PORT/g" /etc/apache2/sites-available/000-default.conf

# Generate application key if not set (will fail gracefully if already set)
php artisan key:generate --force || true

# Cache configuration
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations (only if not already run)
php artisan migrate --force || true
php artisan migrate --database=guest_sqlite --force || true

# Start Apache
exec apache2-foreground

