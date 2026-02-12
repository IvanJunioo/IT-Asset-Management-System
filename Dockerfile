# syntax=docker/dockerfile:1

FROM php:8.5-apache

# Setup PHP extensions and system configurations
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set workdir to web root
WORKDIR /var/www/html

# Get composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files to workdir
COPY . .

# Install system dependencies
RUN apt-get update && apt-get install -y \
  unzip \
  git \
  && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
  unzip \
  git \
  libzip-dev \
  && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
# Run composer
RUN composer install --no-dev --optimize-autoloader

# Fix permissions for the web server
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
