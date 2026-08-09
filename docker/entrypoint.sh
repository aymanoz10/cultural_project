#!/usr/bin/env sh
# سكربت إقلاع الحاوية على Render
set -e

echo "→ caching config & views..."
php artisan config:cache
php artisan view:cache
# ملاحظة: لا نُخزّن المسارات (route:cache) لوجود مسارات Closure في routes/web.php

echo "→ linking storage (harmless when using R2)..."
php artisan storage:link || true

echo "→ running database migrations (Neon)..."
php artisan migrate --force

echo "→ starting Laravel on 0.0.0.0:${PORT:-10000} ..."
exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"
