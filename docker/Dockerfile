# Use PHP-FPM image
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and required libraries
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libmagickwand-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install \
       pdo \
       pdo_mysql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
       zip \
       curl \
       intl \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel source code
COPY . .

# Fix the Git "dubious ownership" issue
RUN git config --global --add safe.directory /var/www/html

# 1. Require Ignition first (before install!)
RUN composer require facade/ignition --ignore-platform-reqs && \
    composer require brick/math:0.10.2 --ignore-platform-reqs --with-all-dependencies

# Install dependencies
RUN composer update --no-dev --optimize-autoloader && \
    composer install --no-dev --optimize-autoloader

# Add Horizon Assets
RUN php artisan horizon:publish

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create supervisor log directory
RUN mkdir -p /var/log/supervisor

# Copy supervisor config and startup script
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# Expose port
EXPOSE 9000

# Start supervisor
CMD ["/usr/local/bin/start-container"]
