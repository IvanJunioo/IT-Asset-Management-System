# syntax=docker/dockerfile:1

FROM php:8.2-apache

# Install system dependencies (NO zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Set Apache server name (avoids warnings)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (Google OAuth, Dompdf, etc.)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application source
COPY . .

# Fix permissions for Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80