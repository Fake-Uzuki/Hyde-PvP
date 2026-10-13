#!/usr/bin/env bash
set -e

# Default PORT to 10000 if not set by Render
export PORT=${PORT:-10000}

echo "==> Configuring Nginx on PORT $PORT..."
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# If SQLite is selected, create database file if needed
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        echo "==> Initializing SQLite database file..."
        touch /var/www/html/database/database.sqlite
        chown www-data:www-data /var/www/html/database/database.sqlite
    fi
fi

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force || echo "Migration skipped or failed (check DB credentials)."

# Link storage
echo "==> Linking storage directory..."
php artisan storage:link || true

# Cache configs and routes for performance
echo "==> Caching application configurations and routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting Web Service (PHP-FPM + Nginx via Supervisord)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
