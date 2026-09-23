# ==============================================================================
# STAGE 1: FRONTEND ASSET COMPILATION (Node.js & Vite)
# ==============================================================================
FROM node:20-alpine AS node_builder
WORKDIR /app

# Copy dependency manifests first for maximum Docker layer caching
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# Copy frontend source files and compile Vite production bundle
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/
COPY public/ public/
RUN npm run build

# ==============================================================================
# STAGE 2: PHP DEPENDENCIES (Composer)
# ==============================================================================
FROM composer:2 AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
# Install production dependencies only, skipping development tools (Pint, Sail, PHPUnit)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs

# ==============================================================================
# STAGE 3: PRODUCTION RUNTIME (PHP 8.3 FPM + Nginx + Supervisor on Alpine)
# ==============================================================================
FROM php:8.3-fpm-alpine AS runtime

# Install system utilities, web server, and supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs

# Install production PHP extensions via the trusted official installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    pcntl \
    redis

# Configure PHP, Nginx, and Supervisord
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled artifacts from builder stages (overwriting raw dev sources)
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

# Set correct ownership for www-data user
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose HTTP port
EXPOSE 80

# Health check to ensure Nginx and PHP-FPM are responding
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://127.0.0.1/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
