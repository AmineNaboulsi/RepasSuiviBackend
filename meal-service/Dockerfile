FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd
# RUN pecl install redis && docker-php-ext-enable redis


# Set working directory
WORKDIR /var/www/html

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Configure Apache
RUN a2enmod rewrite

# Expose port 8000
EXPOSE 8000

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Start Apache
CMD ["apache2-foreground"]