# Use the latest version of PHP
FROM php:8.1.0-apache

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application files to the container
COPY . .

# Install the dependencies for Laravel
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Set up the permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Set the environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Enable Apache modules
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Run the Apache server
CMD ["apache2-foreground"]
