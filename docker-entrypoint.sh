#!/bin/bash
set -e

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Initialize SQLite database if it doesn't exist (for Render ephemeral storage)
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/html/database/database.sqlite
    chmod 777 /var/www/html/database/database.sqlite
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Start Apache in foreground
exec apache2-foreground
