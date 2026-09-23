#!/bin/sh
set -e

# If running as production, warm up Laravel caches for maximum performance
if [ "$APP_ENV" = "production" ]; then
    echo "Warming Laravel production caches..."
    php /var/www/html/artisan package:discover --ansi || true
    php /var/www/html/artisan config:cache || true
    php /var/www/html/artisan route:cache || true
    php /var/www/html/artisan view:cache || true
fi

# Ensure storage directories exist with correct permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting Supervisord (Nginx + PHP-FPM + Queue Worker)..."
exec "$@"
