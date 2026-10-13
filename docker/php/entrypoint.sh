#!/bin/sh
set -e

# Ensure storage and bootstrap/cache directory structure exists
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

# Fix permissions dynamically for dev environment across Windows, Mac, and Linux
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Check if .env file exists; if not, create one from .env.docker.example or .env.example
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.docker.example ]; then
        echo "Creating .env from .env.docker.example..."
        cp /var/www/html/.env.docker.example /var/www/html/.env
    elif [ -f /var/www/html/.env.example ]; then
        echo "Creating .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# Execute the main container command
exec "$@"
