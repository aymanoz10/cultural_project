# تقرير مشروع المركز الثقافي
## Laravel REST API — ملخص التنفيذ

| البند | التفاصيل |
|-------|----------|
| **المشروع** | cultural_project-main |
| **الإطار** | Laravel + Sanctum |
| **تاريخ التقرير** | 24 يونيو 2026 |
| **الحالة** | مكتمل — جاهز للتجربة المحلية |

---

## 1. المقدمة

تم تطوير وإعادة هيكلة مشروع **المركز الثقافي** ليعمل كـ **REST API** متوافق مع المواصفات اليدوية. يشمل المشروع إدارة المراكز الثقافية، الأنشطة، الحجوزات، التطوع، الاقتراحات، الإعلانات، ونظام إشعارات متكامل مع واجهات تجريبية للاختبار.

---

## 2. أهداف العمل

1. بناء نظام مصادقة للمستخدم عبر **OTP + WhatsApp** (بدون كلمة مرور).
2. الإبقاء على مصادقة الأدmin عبر **هاتف + كلمة مرور**.
3. مواءمة جميع الكيانات مع المواصفات اليدوية.
4. بناء نظام إشعارات (**In-App + FCM + WhatsApp**).
5. توفير واجهات تجريبية (Demo UI) لاختبار الإشعارات.
6. توحيد قاعدة البيانات في migrations أساسية فقط.

---

## 3. المصادقة والملف الشخصي

### 3.1 المستخدم (User)

#### التغييرات الرئيسية
- إزالة **Google ID** بالكامل.
- إزالة **password** من جدول users.
- استبدال التسجيل/الدخول التقليدي بنظام **OTP عبر WhatsApp**.
- إضافة API للملف الشخصي.

#### حقول المستخدم
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| name | string | مطلوب |
| date_of_birth | date | مطلوب |
| gender | string | male / female |
| avatar | string | اختياري |
| phone | string | فريد — وسيلة التوثيق |

#### مسارات API — المستخدم

| Method | Endpoint | الوصف | الحماية |
|--------|----------|-------|---------|
| POST | `/api/register/send-otp` | إرسال OTP للتسجيل | عام |
| POST | `/api/register/verify-otp` | التحقق وإنشاء الحساب | عام |
| POST | `/api/login/send-otp` | إرسال OTP للدخول | عام |
| POST | `/api/login/verify-otp` | التحقق وتسجيل الدخول | عام |
| GET | `/api/profile` | عرض الملف الشخصي | Sanctum |
| PUT | `/api/profile` | تحديث الملف الشخصي | Sanctum |
| POST | `/api/logout` | إلغاء Token | Sanctum |

#### آلية OTP
1. المستخدم يطلب OTP عبر رقم الهاتف.
2. النظام يولّد رمزاً من 6 أرقام ويخزنه مشفراً في جدول `otps`.
3. يُرسل الرمز عبر WhatsApp (أو يُسجّل في `laravel.log` في وضع التطوير).
4. عند التحقق الناجح، يُرجع **Bearer Token** في حقل `token`.

#### الملفات المرتبطة
- `app/Http/Controllers/AuthController.php`
- `app/Services/OtpService.php`
- `app/Services/WhatsAppService.php`
- `app/Models/Otp.php`
- `database/migrations/2026_06_24_130000_create_otps_table.php`

---

### 3.2 الأدmin (Admin)

#### حقول الأدmin
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| name | string | مطلوب |
| avatar | string | اختياري |
| phone | string | فريد |
| password | string | مطلوب — مشفر |
| role | string | super / admin / ticketsAdmin |

#### مسارات API — الأدmin

| Method | Endpoint | الوصف | الحماية |
|--------|----------|-------|---------|
| POST | `/api/admin/register` | إنشاء حساب أدmin | عام |
| POST | `/api/admin/login` | تسجيل الدخول | عام |
| GET | `/api/admin/profile` | عرض الملف | auth:admin |
| PUT | `/api/admin/profile` | تحديث الملف | auth:admin |
| POST | `/api/admin/logout` | إلغاء Token | auth:admin |
| POST | `/api/admin/admins/{id}` | تعديل أدmin | auth:admin |
| DELETE | `/api/admin/admins/{id}` | حذف أدmin | auth:admin |

#### الملفات المرتبطة
- `app/Http/Controllers/AdminAuthController.php`
- `database/migrations/2026_05_08_174758_create_admins_table.php`

---

## 4. قاعدة البيانات

### 4.1 سياسة الـ Migrations
- **ملف migration واحد لكل جدول** (create فقط).
- **حذف** جميع migrations من نوع `update_*`.
- تعديل الـ schema مباشرة في ملفات `create_*`.

### 4.2 الجداول (19 migration)

| # | الجدول | أبرز الحقول |
|---|--------|-------------|
| 1 | users | name, date_of_birth, gender, avatar, phone |
| 2 | admins | name, avatar, phone, password, role |
| 3 | cultural_centers | image, name, location, ... |
| 4 | halls | image, features, ... |
| 5 | theaters | image, features, ... |
| 6 | libraries | image, ... |
| 7 | books | image, ... |
| 8 | activities | type, image, capacity, start_time, end_time |
| 9 | reservations | ticket_id, qr_code, status, activity_id |
| 10 | ratings | user_id, activity_id, score |
| 11 | suggestions | type, content, user_id |
| 12 | volunteering_activities | title, description, image, ... |
| 13 | volunteerings | form_data (JSON), status |
| 14 | ads | morph (activity / volunteering_activity) |
| 15 | notifications | Laravel standard (UUID) |
| 16 | device_tokens | token, platform, notifiable |
| 17 | otps | phone, code, purpose, payload, expires_at |
| 18 | personal_access_tokens | Sanctum |
| 19 | cache | Laravel cache |

### 4.3 قرارات التسمية
| القاعدة | التفاصيل |
|---------|----------|
| `avatar` | للمستخدم والأدmin فقط |
| `image` | لجميع الكيانات الأخرى |
| `status` (reservations) | confirmed / wait_list / cancelled |
| `type` (suggestions) | suggestion / complaint / question |
| `type` (activities) | workshop / event / ... |

---

## 5. نظام الإشعارات

### 5.1 البنية المعمارية

```
Controller → Event → Listener → Notification → Channels
                                                    ├── database (In-App)
                                                    ├── FCM (Push)
                                                    └── WhatsApp
```

### 5.2 الأحداث (Events)

| Event | يُطلق عند | الملف |
|-------|-----------|-------|
| ReservationCreated | إنشاء حجز جديد | `app/Events/ReservationCreated.php` |
| ReservationCancelled | إلغاء حجز | `app/Events/ReservationCancelled.php` |
| WaitListPromoted | ترقية من قائمة الانتظار | `app/Events/WaitListPromoted.php` |
| VolunteeringSubmitted | تقديم طلب تطوع | `app/Events/VolunteeringSubmitted.php` |
| VolunteeringStatusUpdated | تغيير حالة التطوع | `app/Events/VolunteeringStatusUpdated.php` |
| SuggestionSubmitted | إرسال اقتراح/شكوى/سؤال | `app/Events/SuggestionSubmitted.php` |

### 5.3 المستمعون (Listeners)

| Listener | يستمع إلى |
|----------|-----------|
| SendReservationCreatedNotification | ReservationCreated |
| SendReservationCancelledNotification | ReservationCancelled |
| SendWaitListPromotedNotification | WaitListPromoted |
| NotifyAdminsOfVolunteering | VolunteeringSubmitted |
| SendVolunteeringStatusNotification | VolunteeringStatusUpdated |
| NotifyAdminsOfSuggestion | SuggestionSubmitted |

**التسجيل:** `app/Providers/AppServiceProvider.php`

### 5.4 الإشعارات (Notifications)

| الإشعار | المستلم | المحفّز |
|---------|---------|---------|
| ReservationConfirmed | المستخدم | حجز مؤكد |
| ReservationWaitListed | المستخدم | إضافة لقائمة الانتظار |
| WaitListPromoted | المستخدم | ترقية من الانتظار |
| ReservationCancelled | المستخدم | إلغاء الحجز |
| VolunteeringSubmitted | الأدmin | طلب تطوع جديد |
| VolunteeringStatusChanged | المستخدم | تغيير حالة التطوع |
| NewSuggestionReceived | الأدmin | اقتراح/شكوى/سؤال جديد |

### 5.5 القنوات (Channels)

| القناة | الملف | الوضع الافتراضي |
|--------|-------|-----------------|
| Database | Laravel built-in | فعّال |
| FCM | `app/Notifications/Channels/FcmChannel.php` | log |
| WhatsApp | `app/Notifications/Channels/WhatsAppChannel.php` | log |

### 5.6 API الإشعارات

#### للمستخدم (`auth:sanctum`)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/notifications` | قائمة الإشعارات |
| GET | `/api/notifications/unread-count` | عدد غير المقروء |
| PATCH | `/api/notifications/{id}/read` | تعليم كمقروء |
| POST | `/api/notifications/read-all` | تعليم الكل كمقروء |
| DELETE | `/api/notifications/{id}` | حذف إشعار |
| POST | `/api/device-tokens` | تسجيل FCM token |
| DELETE | `/api/device-tokens` | إلغاء FCM token |
| POST | `/api/notifications/test` | إرسال إشعار تجريبي |

#### للأدmin (`auth:admin` — prefix `/api/admin`)

نفس المسارات أعلاه تحت `/api/admin/notifications/...`

#### الملفات المرتبطة
- `app/Http/Controllers/NotificationController.php`
- `app/Http/Controllers/DeviceTokenController.php`
- `app/Http/Controllers/DemoNotificationController.php`
- `database/migrations/2026_05_08_175324_create_notifications_table.php`
- `database/migrations/2026_06_24_140000_create_device_tokens_table.php`

---

## 6. API الكيانات

### 6.1 المراكز والمرافق

| المورد | GET (عام) | CRUD (admin) |
|--------|-----------|--------------|
| `/api/centers` | ✅ | POST, POST/{id}, DELETE/{id} |
| `/api/theaters` | ✅ | POST, POST/{id}, DELETE/{id} |
| `/api/halls` | ✅ | POST, POST/{id}, DELETE/{id} |
| `/api/libraries` | ✅ | POST, POST/{id}, DELETE/{id} |

### 6.2 الأنشطة والحجوزات

| المورد | GET (عام) | CRUD (admin) | المستخدم (Sanctum) |
|--------|-----------|--------------|---------------------|
| `/api/activities` | ✅ | POST, POST/{id}, DELETE/{id} | — |
| `/api/activities/{id}/wait-list` | ✅ | — | — |
| `/api/reservations` | — | — | GET, POST, GET/{id}, POST/{id}/cancel |

**حالات الحجز:** `confirmed` | `wait_list` | `cancelled`

### 6.3 التطوع

| المورد | GET (عام) | CRUD (admin) | المستخدم |
|--------|-----------|--------------|----------|
| `/api/volunteering-activities` | ✅ | POST, POST/{id}, DELETE/{id} | — |
| `/api/volunteerings` | — | PUT /admin/volunteerings/{id}/status | GET, POST |

### 6.4 الاقتراحات والتقييمات والإعلانات

| المورد | GET (عام) | admin | المستخدم |
|--------|-----------|-------|----------|
| `/api/suggestions` | — | GET /admin/suggestions | GET, POST, PUT/{id}, DELETE/{id} |
| `/api/ratings` | ✅ | — | POST, PUT/{id}, DELETE/{id} |
| `/api/ads` | ✅ | POST, POST/{id}, DELETE/{id} | — |

### 6.5 لوحة التحكم

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/admin/dashboard` | إحصائيات Dashboard |

---

## 7. الواجهات التجريبية (Demo UI)

### 7.1 المسارات

| URL | الاسم | الوصف |
|-----|-------|-------|
| `/` | — | تحويل تلقائي إلى `/demo` |
| `/demo` | demo.index | الصفحة الرئيسية |
| `/demo/user` | demo.user | واجهة إشعارات المستخدم |
| `/demo/admin` | demo.admin | واجهة إشعارات الأدmin |

### 7.2 الملفات

```
app/Http/Controllers/DemoController.php
resources/views/demo/
├── layout.blade.php
├── index.blade.php
├── user.blade.php
└── admin.blade.php
public/js/notifications-demo.js
routes/web.php
```

### 7.3 الميزات

- جرس إشعارات مع عداد غير المقروء
- حفظ Bearer Token في localStorage
- تحديث قائمة الإشعارات
- تعليم إشعار/الكل كمقروء
- إرسال إشعار تجريبي
- تسجيل FCM token تجريبي
- Tailwind CSS عبر CDN (بدون Vite)

---

## 8. الإصلاحات المنفذة

| # | المشكلة | الحل |
|---|---------|------|
| 1 | صفحات Demo تعطي 500 | إنشاء `.env` + `php artisan key:generate` |
| 2 | Vite manifest not found | استبدال `@vite` بـ Tailwind CDN |
| 3 | روابط ثابتة لا تعمل في subfolder | استخدام `route('demo.*')` |
| 4 | تعارض مسار admin `POST /{id}` | تغيير إلى `POST /admins/{id}` |
| 5 | NotificationController لا يدعم admin | دعم guard مزدوج |
| 6 | مسارات API ثابتة في JS | `data-api-base` ديناميكي |

---

## 9. الإعدادات

### 9.1 متغيرات `.env` الأساسية

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file

WHATSAPP_MODE=log
FCM_MODE=log
OTP_EXPIRES_MINUTES=5
OTP_MAX_ATTEMPTS=5
```

### 9.2 أوضاع الخدمات الخارجية

| الخدمة | وضع log (تطوير) | وضع live (إنتاج) |
|--------|-----------------|-------------------|
| WhatsApp OTP | يُسجّل في `storage/logs/laravel.log` | يحتاج WHATSAPP_TOKEN + PHONE_NUMBER_ID |
| FCM Push | يُسجّل في `storage/logs/laravel.log` | يحتاج FCM_SERVER_KEY |

---

## 10. دليل التشغيل

### 10.1 المتطلبات
- PHP 8.2+
- Composer
- SQLite (أو MySQL/PostgreSQL)

### 10.2 خطوات التشغيل

```bash
# 1. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 2. قاعدة البيانات
php artisan migrate:fresh

# 3. تشغيل السيرفر
php artisan serve
```

### 10.3 الوصول
- **الواجهة التجريبية:** http://127.0.0.1:8000/demo
- **API:** http://127.0.0.1:8000/api/...

---

## 11. Bearer Token — دليل الحصول

### 11.1 مستخدم

```
1. POST /api/login/send-otp       → { "phone": "0991234567" }
2. راجع laravel.log للحصول على OTP (وضع log)
3. POST /api/login/verify-otp     → { "phone": "0991234567", "code": "123456" }
4. انسخ قيمة "token" من الاستجابة
```

### 11.2 أدmin

```
1. POST /api/admin/login → { "phone": "0911111111", "password": "password123" }
2. انسخ قيمة "token" من الاستجابة
```

### 11.3 الاستخدام في Demo UI
1. افتح `/demo/user` أو `/demo/admin`
2. الصق Token في خانة Bearer Token
3. اضغط "حفظ Token"

---

## 12. هيكل المشروع — الملفات الجديدة

```
app/
├── Events/
│   ├── ReservationCreated.php
│   ├── ReservationCancelled.php
│   ├── WaitListPromoted.php
│   ├── VolunteeringSubmitted.php
│   ├── VolunteeringStatusUpdated.php
│   └── SuggestionSubmitted.php
├── Listeners/
│   ├── SendReservationCreatedNotification.php
│   ├── SendReservationCancelledNotification.php
│   ├── SendWaitListPromotedNotification.php
│   ├── NotifyAdminsOfVolunteering.php
│   ├── SendVolunteeringStatusNotification.php
│   └── NotifyAdminsOfSuggestion.php
├── Notifications/
│   ├── ReservationConfirmed.php
│   ├── ReservationWaitListed.php
│   ├── WaitListPromoted.php
│   ├── ReservationCancelled.php
│   ├── VolunteeringSubmitted.php
│   ├── VolunteeringStatusChanged.php
│   ├── NewSuggestionReceived.php
│   ├── Concerns/FormatsNotificationPayload.php
│   └── Channels/
│       ├── FcmChannel.php
│       └── WhatsAppChannel.php
├── Services/
│   ├── OtpService.php
│   └── WhatsAppService.php
├── Http/Controllers/
│   ├── AuthController.php
│   ├── AdminAuthController.php
│   ├── NotificationController.php
│   ├── DeviceTokenController.php
│   ├── DemoNotificationController.php
│   └── DemoController.php
├── Models/Otp.php
└── Providers/AppServiceProvider.php

database/migrations/   (19 ملف create)
resources/views/demo/    (4 ملفات blade)
public/js/             notifications-demo.js
routes/
├── api.php
└── web.php
config/services.php    (whatsapp, otp, fcm)
```

---

## 13. ملاحظات وتوصيات

| # | الملاحظة |
|---|----------|
| 1 | `migrate:fresh` يحذف جميع البيانات — استخدمه عند تغيير الـ schema |
| 2 | الإشعارات تُرسل **متزامنة** — لا حاجة لـ queue worker للتجربة |
| 3 | لا حاجة لـ `npm run dev` — الواجهات التجريبية تعمل بدون Vite |
| 4 | للإنتاج: غيّر `WHATSAPP_MODE` و `FCM_MODE` إلى `live` وأضف credentials |
| 5 | Sanctum: المستخدم `auth:sanctum` — الأدmin `auth:admin` |

---

## 14. الخلاصة

تم تنفيذ مشروع API متكامل للمركز الثقافي يشمل:

- ✅ مصادقة OTP للمستخدم + password للأدmin
- ✅ 19 جدول قاعدة بيانات موحّد
- ✅ نظام إشعارات (6 events, 6 listeners, 7 notifications, 3 channels)
- ✅ API كامل للكيانات (centers, activities, reservations, volunteering, ...)
- ✅ واجهات Demo للاختبار
- ✅ إصلاح مشاكل الروابط والبيئة

**الحالة:** جاهز للتجربة المحلية بعد `migrate:fresh` + `php artisan serve`.

---

*نهاية التقرير*
