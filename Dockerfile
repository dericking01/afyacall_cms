# Use official PHP image
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql zip gd pcntl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist

# Set proper permissions
RUN chown -R www-data:www-data /var/www

# Expose the port Laravel runs on
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
