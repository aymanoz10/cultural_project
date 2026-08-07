FROM php:8.4-cli

# تثبيت الملحقات والأدوات المطلوبة للـ Laravel ودعم PostgreSQL (libpq-dev)
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev curl \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# تثبيت Node.js و NPM لبناء ملفات Vite/Tailwind
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# تثبيت حزم PHP
RUN composer install --no-dev --optimize-autoloader

# تثبيت وبناء ملفات الواجهات (CSS / JS)
RUN npm install
RUN npm run build

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]