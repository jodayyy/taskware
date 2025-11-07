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

# Ensure .env file exists (Laravel needs this to read APP_KEY)
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file..."
    touch /var/www/html/.env
    chown www-data:www-data /var/www/html/.env
    chmod 644 /var/www/html/.env
fi

# Generate and set APP_KEY if not already set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating new key..."
    # Generate a base64 encoded 32-byte key (Laravel format)
    GENERATED_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
    export APP_KEY="$GENERATED_KEY"
    echo "Generated APP_KEY"
    
    # Write to .env file (Laravel reads from here)
    if grep -q "APP_KEY=" /var/www/html/.env; then
        sed -i "s|APP_KEY=.*|APP_KEY=$GENERATED_KEY|" /var/www/html/.env
    else
        echo "APP_KEY=$GENERATED_KEY" >> /var/www/html/.env
    fi
    chown www-data:www-data /var/www/html/.env || true
    chmod 644 /var/www/html/.env || true
    echo "APP_KEY written to .env file"
else
    echo "APP_KEY already set from environment"
    # Ensure it's also in .env file (Laravel reads from here)
    if grep -q "APP_KEY=" /var/www/html/.env; then
        sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" /var/www/html/.env
    else
        echo "APP_KEY=$APP_KEY" >> /var/www/html/.env
    fi
    chown www-data:www-data /var/www/html/.env || true
    chmod 644 /var/www/html/.env || true
fi

# Clear config cache to ensure new key is loaded
php artisan config:clear || true

# Verify APP_KEY is in .env file
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "ERROR: APP_KEY is not properly set in .env file!"
    echo "Current .env APP_KEY line:"
    grep "APP_KEY" /var/www/html/.env || echo "APP_KEY line not found"
    exit 1
fi
echo "APP_KEY verified in .env file"

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

