FROM php:8.2-apache

# Install sistem dependencies yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Aktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur folder kerja (working directory)
WORKDIR /var/www/html

# Salin semua file dari proyek repositori
COPY . /var/www/html

# Install package Laravel
RUN composer install --no-dev --optimize-autoloader

# Beri izin (permission) akses untuk folder storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ubah settingan Apache agar mengarah ke folder /public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Buka port 80
EXPOSE 80

# Jalankan script awal (entrypoint)
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]