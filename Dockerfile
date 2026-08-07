FROM php:8.4-cli
# تثبيت الحزم وإضافات PHP المطلوبة
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# جلب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# force rebuild trigger
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . .

# تثبيت مكتبات PHP
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]