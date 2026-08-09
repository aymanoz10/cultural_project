# syntax=docker/dockerfile:1
# ============================================================================
#  حاوية إنتاج لمشروع "منظومة الثقافة" (Laravel 12 + PostgreSQL + Vite/Tailwind)
#  للنشر على Render (Docker). البناء على مرحلتين لتصغير الصورة النهائية.
# ============================================================================

# ---------- المرحلة 1: بناء أصول الواجهة (Vite + Tailwind v4) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
# نُصدّر resources كاملاً لأن Tailwind يمسح قوالب Blade عبر @source
COPY resources ./resources
RUN npm run build
# المخرجات: /app/public/build (CSS/JS مُصغّر + خطوط مستضافة محلياً)

# ---------- المرحلة 2: تطبيق PHP (Laravel) ----------
FROM php:8.4-cli AS app

# إضافات PHP المطلوبة: PostgreSQL + الصور + الضغط + الرياضيات
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pgsql gd zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Composer (من صورته الرسمية)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# تثبيت اعتماديات PHP أولاً للاستفادة من التخزين المؤقت للطبقات
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# نسخ بقية الكود + الأصول المبنية من المرحلة 1
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && chmod -R 775 storage bootstrap/cache

# سكربت الإقلاع (هجرات + تخزين مؤقت للإعدادات + تشغيل)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# المنفذ 7860 هو منفذ Hugging Face Spaces الافتراضي (منصّات أخرى تُمرّر PORT خاصاً بها)
ENV PORT=7860
EXPOSE 7860
CMD ["/usr/local/bin/entrypoint.sh"]
