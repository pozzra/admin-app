#!/bin/bash
set -e

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache in foreground
exec apache2-foreground
