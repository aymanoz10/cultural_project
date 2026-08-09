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

# إنشاء مشرف عام تلقائياً عند أول إقلاع (إن ضُبطت SEED_ADMIN_* ولم يوجد مشرف بعد) —
# مفيد على المنصّات بلا Shell مثل Hugging Face Spaces. env() تُقرأ داخل PHP فلا يظهر السرّ في الأمر.
echo "→ ensuring super admin..."
php artisan tinker --execute="if (env('SEED_ADMIN_PHONE') && ! \App\Models\Admin::where('role','super')->exists()) { \App\Models\Admin::create(['name'=>'مدير النظام','phone'=>env('SEED_ADMIN_PHONE'),'password'=>bcrypt(env('SEED_ADMIN_PASSWORD','password')),'role'=>'super']); echo 'super admin created'; }" || true

echo "→ starting Laravel on 0.0.0.0:${PORT:-7860} ..."
exec php artisan serve --host 0.0.0.0 --port "${PORT:-7860}"
