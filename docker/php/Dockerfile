FROM php:8.3-fpm-bookworm

# Build arguments for cross-platform UID/GID mapping
ARG WWWUSER=1000
ARG WWWGROUP=1000

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install official PHP extension installer helper (supports both amd64 and arm64 Apple Silicon)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
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
    opcache \
    redis \
    pcntl \
    sockets

# Install official Composer 2
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy custom PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy entrypoint script
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose PHP-FPM FastCGI port
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
