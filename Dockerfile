FROM php:8.2-apache

# 1. Install system dependencies & Node.js (untuk Vite build)
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    # Install ekstensi PHP untuk MySQL dan PostgreSQL (jika pakai DB dari Render)
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip

# 2. Aktifkan modul rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# 3. Ubah DocumentRoot Apache ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Render akan memberikan port secara dinamis lewat variabel environment PORT
RUN sed -i 's/Listen 80/Listen ${PORT:-80}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# 5. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Copy seluruh file aplikasi ke dalam container
COPY . .

# 7. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 8. Install Node dependencies & Build aset frontend (Vite)
RUN npm install
RUN npm run build

# 9. Atur permission untuk folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Bersihkan cache konfigurasi bawaan
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# 11. Jalankan auto-migrate saat container menyala dan jalankan Apache
CMD php artisan migrate --force && apache2-foreground