@extends('demo.layout')

@section('title', 'الرئيسية')

@section('content')
<div class="space-y-8">
    <div class="text-center space-y-3 py-8">
        <h1 class="text-3xl font-bold text-white">واجهات تجريبية للإشعارات</h1>
        <p class="text-slate-400 max-w-xl mx-auto">جرّب نظام الإشعارات الكامل: In-App + FCM (log) + WhatsApp (log)</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <a href="{{ route('demo.user') }}" class="group block p-6 rounded-2xl border border-slate-800 bg-slate-900 hover:border-emerald-500/50 transition">
            <div class="text-4xl mb-4">👤</div>
            <h2 class="text-xl font-semibold text-emerald-400 group-hover:text-emerald-300">واجهة المستخدم</h2>
            <p class="text-slate-400 mt-2 text-sm">جرس الإشعارات، قراءة، إرسال تجريبي، تسجيل FCM token</p>
        </a>

        <a href="{{ route('demo.admin') }}" class="group block p-6 rounded-2xl border border-slate-800 bg-slate-900 hover:border-amber-500/50 transition">
            <div class="text-4xl mb-4">🛡️</div>
            <h2 class="text-xl font-semibold text-amber-400 group-hover:text-amber-300">واجهة الأدمن</h2>
            <p class="text-slate-400 mt-2 text-sm">إشعارات التطوع والاقتراحات والشكاوى</p>
        </a>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-sm text-slate-400 space-y-2">
        <p class="font-medium text-slate-200">خطوات التجربة:</p>
        <ol class="list-decimal list-inside space-y-1">
            <li>شغّل <code class="text-emerald-400">php artisan migrate:fresh</code> إن لزم</li>
            <li>سجّل دخول عبر OTP واحصل على Token</li>
            <li>الصق الـ Token في الواجهة التجريبية</li>
            <li>اضغط "إرسال إشعار تجريبي" أو نفّذ حجز/تطوع/اقتراح</li>
            <li>راجع <code class="text-emerald-400">storage/logs/laravel.log</code> لـ FCM و WhatsApp</li>
        </ol>
    </div>
</div>
@endsection
