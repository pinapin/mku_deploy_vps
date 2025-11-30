FROM php:8.3-fpm-alpine

# Set Arguments
ARG USER_ID=1000
ARG GROUP_ID=1000

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    bash \
    vim \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN addgroup -g ${GROUP_ID} laravel \
    && adduser -D -u ${USER_ID} -G laravel laravel \
    && mkdir -p /var/log/php-fpm \
    && chown -R laravel:laravel /var/log/php-fpm

# Copy application files
COPY --chown=laravel:laravel . /var/www/html

# Copy PHP-FPM configs
COPY docker/php/php-fpm-global.conf /usr/local/etc/php-fpm.conf
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Install composer dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Create storage link before switching user
RUN php artisan storage:link || true

# Set permissions - CRITICAL for Laravel
RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public/storage \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/storage -type f -exec chmod 664 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;

# Switch to non-root user
USER laravel

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]