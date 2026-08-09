# دليل نشر «منظومة الثقافة» مجاناً — Render + Neon + Cloudflare R2

هذا الدليل يعلّمك نشر المشروع على ثلاث خدمات مجانية متكاملة:

| الجزء | الخدمة | الدور |
|------|--------|-------|
| التطبيق (PHP) | **Render** | يشغّل Laravel داخل حاوية Docker |
| قاعدة البيانات | **Neon** | PostgreSQL مجاني لا ينتهي |
| ملفات الرفع | **Cloudflare R2** | تخزين S3 (الأغلفة + ملفات PDF) — لأن قرص Render **مؤقّت** |

> **لماذا R2 ضروري؟** قرص Render يُمحى عند كل نشر، فلو خزّنت الملفات محلياً تختفي. الكود هنا **محايد عن القرص**، فيكفي ضبط متغيّرات البيئة ليتحوّل التخزين إلى R2 تلقائياً — بلا تعديل كود.

الملفات الجاهزة في المستودع: `Dockerfile` · `docker/entrypoint.sh` · `render.yaml` · `.env.production.example`.

---

## الخطوة 0 — تجهيز الكود
1. ارفع المشروع إلى مستودع **GitHub** (خاص أو عام).
2. ولّد مفتاح التطبيق محلياً واحتفظ به:
   ```bash
   php artisan key:generate --show
   ```
   انسخ الناتج (`base64:...`) — ستضعه في Render كـ `APP_KEY`.

---

## الخطوة 1 — قاعدة البيانات على Neon
1. أنشئ حساباً على **[neon.tech](https://neon.tech)** → **Create Project** (اختر منطقة قريبة).
2. بعد الإنشاء افتح **Connection Details**، واختر وضع **Pooled connection** (مهم — يدعم IPv4 وعدداً كبيراً من الاتصالات).
3. من سلسلة الاتصال استخرج القيم وضعها لاحقاً في Render:
   - `DB_HOST` = المضيف المنتهي بـ `-pooler...neon.tech`
   - `DB_PORT` = `5432`
   - `DB_DATABASE` = عادةً `neondb`
   - `DB_USERNAME` / `DB_PASSWORD`
   - `DB_SSLMODE` = `require`

> الهجرات (`migrate`) تعمل تلقائياً عند أول إقلاع للحاوية (انظر `entrypoint.sh`).

---

## الخطوة 2 — التخزين على Cloudflare R2
1. أنشئ حساباً على **[Cloudflare](https://dash.cloudflare.com)** → من القائمة **R2**.
2. أنشئ **bucketين**:
   - `cultural-public` — للأغلفة والصور.
   - `cultural-private` — لملفات PDF.
3. فعّل الوصول العام للـ bucket العام: افتح `cultural-public` → **Settings → Public access → Allow / r2.dev subdomain** → انسخ الرابط `https://pub-xxxx.r2.dev` (سيكون `PUBLIC_DISK_URL`).
4. أنشئ مفاتيح S3: **R2 → Manage R2 API Tokens → Create API Token** (صلاحية Object Read & Write) → احفظ:
   - `R2_ACCESS_KEY_ID`
   - `R2_SECRET_ACCESS_KEY`
   - `R2_ENDPOINT` = `https://<account-id>.r2.cloudflarestorage.com` (معرّف الحساب يظهر في صفحة R2).

---

## الخطوة 3 — نشر التطبيق على Render
### الطريقة الأسهل (Blueprint)
1. في **[render.com](https://render.com)** → **New + → Blueprint** → اربط مستودع GitHub.
2. سيقرأ Render ملف `render.yaml` ويُنشئ الخدمة تلقائياً.
3. سيطلب القيم المعلّمة `sync:false` — ألصق قيم Neon وR2 وAPP_KEY وAPP_URL.

### أو يدوياً (Web Service)
1. **New + → Web Service** → اربط المستودع → **Runtime: Docker** → **Plan: Free**.
2. **Health Check Path:** `/up`.
3. من تبويب **Environment** أضِف كل المفاتيح من `.env.production.example` (القاعدة + R2 + APP_*).
4. **Create Web Service** — سيبني الحاوية (composer + npm build) ثم يشغّلها.

> عنوان خدمتك سيكون `https://<name>.onrender.com` — ضعه في `APP_URL`، واستخدمه كعنوان API لتطبيق Flutter.

---

## الخطوة 4 — بعد أول نشر
- **الهجرات:** تُنفَّذ تلقائياً. راقب سجلّ Render (تبويب Logs) لرسائل `→ running migrations`.
- **أنشئ مشرفاً عاماً:** افتح **Render → Shell** (تبويب Shell للخدمة) ونفّذ tinker، أو أضِف Seeder. مثال سريع في Shell:
  ```bash
  php artisan tinker --execute="\App\Models\Admin::create(['name'=>'مدير','phone'=>'0100000000','password'=>bcrypt('اختر-كلمة-قوية'),'role'=>'super']);"
  ```
- **تحقّق:** افتح `https://<name>.onrender.com/login` وسجّل الدخول، ثم جرّب رفع كتاب/غلاف — يجب أن يُخزَّن على R2 ويظهر.

---

## ملاحظات وحلول الأخطاء الشائعة
- **الخدمة تنام بعد ~15 دقيقة خمول** (خطة Render المجانية) — أول طلب بعدها بطيء (~30 ثانية). طبيعي للعرض المجاني.
- **Neon يوقف المشروع بعد خمول طويل** — يستيقظ تلقائياً عند أول اتصال (قد يتأخّر أول طلب ثوانٍ).
- **خطأ اتصال قاعدة:** تأكّد أنك استخدمت مضيف **Pooler** (لا المباشر IPv6) و`DB_SSLMODE=require`.
- **الصور لا تظهر:** تأكّد أن `cultural-public` عام، وأن `PUBLIC_DISK_URL` = رابط r2.dev الصحيح، و`FILESYSTEM_DISK=public` و`PUBLIC_DISK_DRIVER=s3`.
- **ملفات PDF لا تُحمّل:** `BOOKS_DISK_DRIVER=s3` والـ bucket الخاص + المفاتيح صحيحة (تُقدَّم عبر روابط موقّتة تلقائياً).
- **تعطّل عند `config:cache`:** تأكّد من ضبط كل متغيّرات البيئة قبل النشر.

## ترقية لاحقة (عند الحاجة)
- بديل `php artisan serve` بخادم إنتاجي (Nginx + PHP-FPM أو FrankenPHP) لأداء أعلى.
- إضافة **Cron** على Render لمهام مجدولة (ترقية قوائم الانتظار، تنظيف الإشعارات).
- نطاق مخصّص + شهادة HTTPS (مجانية على Render).
