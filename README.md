---
title: Cultural Project
emoji: 🎭
colorFrom: green
colorTo: gray
sdk: docker
app_port: 7860
pinned: false
---

# منظومة الثقافة — لوحة تحكم

لوحة تحكم **Laravel 12** (عربية RTL) لإدارة المراكز الثقافية والفعاليات وحجوزات القاعات والمكتبات.

| الطبقة | الخدمة |
|--------|--------|
| الاستضافة | Hugging Face Spaces (Docker) |
| قاعدة البيانات | Neon (PostgreSQL) |
| تخزين الملفات | Supabase Storage (S3) |

للنشر خطوة بخطوة راجع [`DEPLOYMENT.md`](DEPLOYMENT.md). المتغيّرات المطلوبة في [`.env.production.example`](.env.production.example).

> يعمل التطبيق على المنفذ **7860** (منفذ Hugging Face الافتراضي). الهجرات وإنشاء المشرف العام يتمّان تلقائياً عند أول إقلاع.
