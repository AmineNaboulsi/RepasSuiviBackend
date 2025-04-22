# FROM php:8.2-apache

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     libpq-dev

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions
# RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# # Set working directory
# WORKDIR /var/www/html

# # Install composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copy application files
# COPY . .

# # Install dependencies
# RUN composer install --no-dev --optimize-autoloader

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html \
#     && chmod -R 755 /var/www/html/storage

# # Configure Apache
# RUN a2enmod rewrite

# # Expose port 80
# EXPOSE 80

# RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# # Start Apache
# CMD ["apache2-foreground"]

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

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache DocumentRoot to /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides in Apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Optional: Add ServerName to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose Apache's port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
