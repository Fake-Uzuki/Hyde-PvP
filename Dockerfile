# ====================================================
# Stage 1: Build Frontend Assets (Vite & Tailwind)
# ====================================================
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --prefer-offline --no-audit || npm install
COPY resources resources/
COPY public public/
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# ====================================================
# Stage 2: Install Composer Dependencies
# ====================================================
FROM composer:2.8 AS composer-builder
WORKDIR /app
COPY composer*.json ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ====================================================
# Stage 3: Production Image (PHP 8.3-FPM + Nginx + Supervisor)
# ====================================================
FROM php:8.3-fpm-bookworm

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    gettext-base \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install official PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    pdo_pgsql \
    mbstring \
    openssl \
    tokenizer \
    xml \
    ctype \
    json \
    curl \
    bcmath \
    gd \
    intl \
    zip \
    opcache

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy production PHP vendor dependencies
COPY --from=composer-builder /app/vendor /var/www/html/vendor

# Copy compiled frontend assets from Vite build
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Setup Nginx, Supervisor, and startup script
COPY docker/render/nginx.conf /etc/nginx/conf.d/default.conf.template
COPY docker/render/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/render/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Set storage and cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 10000 80

CMD ["/usr/local/bin/start.sh"]
