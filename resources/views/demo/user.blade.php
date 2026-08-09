@extends('demo.layout')

@section('title', 'مستخدم')

@section('content')
<div class="space-y-6" id="demo-app" data-role="user" data-api-base="{{ url('/api') }}">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-white">إشعارات المستخدم</h1>
            <p class="text-slate-400 text-sm mt-1">In-App + FCM + WhatsApp</p>
        </div>
        <div class="relative">
            <button type="button" id="bell-btn" class="relative p-3 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                <span class="text-xl">🔔</span>
                <span id="unread-badge" class="hidden absolute -top-1 -left-1 min-w-5 h-5 px-1 flex items-center justify-center text-xs font-bold bg-red-500 text-white rounded-full">0</span>
            </button>
            <div id="dropdown" class="hidden absolute left-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-slate-700 bg-slate-900 shadow-xl z-50">
                <div class="p-3 border-b border-slate-800 flex justify-between items-center">
                    <span class="font-medium text-sm">الإشعارات</span>
                    <button type="button" id="read-all-btn" class="text-xs text-emerald-400 hover:underline">قراءة الكل</button>
                </div>
                <div id="notifications-list" class="divide-y divide-slate-800"></div>
                <p id="empty-state" class="hidden p-6 text-center text-slate-500 text-sm">لا توجد إشعارات</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-4">
            <h2 class="font-semibold text-emerald-400">⚙️ الإعداد</h2>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Bearer Token</label>
                <textarea id="token-input" rows="3" class="w-full rounded-lg bg-slate-950 border border-slate-700 px-3 py-2 text-sm font-mono text-slate-200 focus:border-emerald-500 focus:outline-none" placeholder="الصق token من /login/verify-otp"></textarea>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="save-token-btn" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-medium">حفظ Token</button>
                <button type="button" id="refresh-btn" class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm">تحديث</button>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-4">
            <h2 class="font-semibold text-emerald-400">🧪 تجربة</h2>
            <button type="button" id="test-notification-btn" class="w-full px-4 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-medium text-sm">
                إرسال إشعار تجريبي (حجز)
            </button>
            <button type="button" id="register-fcm-btn" class="w-full px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm border border-slate-700">
                تسجيل FCM Token تجريبي
            </button>
            <p id="status-msg" class="text-sm text-slate-400 min-h-5"></p>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="font-semibold text-white mb-4">📋 كل الإشعارات</h2>
        <div id="full-list" class="space-y-3"></div>
    </section>
</div>
@endsection
