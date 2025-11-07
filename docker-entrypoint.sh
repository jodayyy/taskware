#!/bin/bash
set -e

# Set PORT from environment (Render provides this)
export PORT=${PORT:-10000}

# Configure Apache to listen on the PORT
echo "Listen $PORT" > /etc/apache2/ports.conf

# Update Apache virtual host to use PORT
sed -i "s/\${PORT:-10000}/$PORT/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage directories exist and have proper permissions
# Run as root to ensure we can set permissions
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Create the log file if it doesn't exist (to ensure it has correct permissions)
touch /var/www/html/storage/logs/laravel.log

# Set ownership and permissions (must run as root)
# Use 777 for storage to ensure write access (can be tightened later if needed)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Verify permissions (for debugging)
echo "Storage permissions:"
ls -la /var/www/html/storage/logs/ || true
echo "Bootstrap cache permissions:"
ls -la /var/www/html/bootstrap/cache/ || true

# Generate application key if not set (will fail gracefully if already set)
php artisan key:generate --force || true

# Cache configuration (will create cache files with proper permissions due to 777 on directories)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Ensure all files created by artisan are owned by www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Run migrations (only if not already run)
php artisan migrate --force || true
php artisan migrate --database=guest_sqlite --force || true

# Final permission check and fix (in case migrations created files)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Start Apache
exec apache2-foreground

