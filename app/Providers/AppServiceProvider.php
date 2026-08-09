<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->hasHeader('x-forwarded-proto')) {
        URL::forceScheme('https');
    }
        // خطوة صفر: تفعيل مراقبة وتسجيل الاستعلامات البطيئة (> 100ms) كخط أساس للقياس
        DB::listen(function ($query) {
            if ($query->time > 100) {
                Log::warning("⚠️ استعلام قاعدة بيانات بطيء ({$query->time}ms): {$query->sql}", [
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            }
        });

        Relation::morphMap([
            'venue'    => 'App\Models\Venue',
            'center'   => 'App\Models\CulturalCenter',
            'activity' => 'App\Models\Activity',
        ]);

        // ✅ لا حاجة لأي Event::listen() هنا يدوياً — Laravel يكتشف تلقائياً
        // (Auto-Discovery) كل كلاس بمجلد app/Listeners له دالة handle()
        // بنوع حدث محدَّد، ويُسجّله تلقائياً لذلك الحدث. كان التسجيل اليدوي
        // هنا موجوداً بالتوازي مع هذا الاكتشاف التلقائي، فكان كل مستمع
        // (ومن ضمنها كل إشعارات الحجز/الإلغاء/الترقية/التطوع) يُنفَّذ مرتين
        // لكل حدث واحد فقط — وهو السبب الجذري لكل تكرار الإشعارات الذي
        // واجهناه. احتفظ بملفات app/Events و app/Listeners كما هي؛
        // الاكتشاف التلقائي يعمل بمجرد وجودها هناك دون أي تسجيل إضافي.
    }
}