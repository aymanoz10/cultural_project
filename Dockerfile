FROM php:8.4-cli

# تثبيت الملحقات والأدوات المطلوبة للـ Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# تثبيت حزم PHP
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

# صيغة Array المفضلة لتفادي التحذير
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]