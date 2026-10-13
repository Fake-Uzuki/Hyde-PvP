#!/usr/bin/env bash
set -e

# Default PORT to 10000 if not set by Render
export PORT=${PORT:-10000}

echo "==> Configuring Nginx on PORT $PORT..."
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Remove composer platform check if present to prevent version lock halts
rm -f /var/www/html/vendor/composer/platform_check.php

# Ensure .env file exists
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Ensure directories and permissions exist
mkdir -p /var/www/html/database
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Configure HTTPS APP_URL and ASSET_URL for Render
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
    export ASSET_URL="$RENDER_EXTERNAL_URL"
elif [ -z "$APP_URL" ] || [ "$APP_URL" = "http://localhost" ]; then
    export APP_URL="https://hyde-pvp.onrender.com"
    export ASSET_URL="https://hyde-pvp.onrender.com"
fi

# Default to SQLite if DB_CONNECTION is empty or sqlite
export DB_CONNECTION=${DB_CONNECTION:-sqlite}
if [ "$DB_CONNECTION" = "sqlite" ]; then
    export DB_DATABASE=${DB_DATABASE:-/var/www/html/database/database.sqlite}
    if [ ! -f "$DB_DATABASE" ]; then
        echo "==> Initializing SQLite database at $DB_DATABASE..."
        touch "$DB_DATABASE"
    fi
    chown www-data:www-data "$DB_DATABASE"
fi

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Link storage
echo "==> Linking storage directory..."
php artisan storage:link || true

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force || echo "Migration warning: check DB configuration."

# Cache configs and routes for performance
echo "==> Optimizing application caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting Web Service (PHP-FPM + Nginx via Supervisord)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
