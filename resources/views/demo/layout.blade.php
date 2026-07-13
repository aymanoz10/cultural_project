<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Demo') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <header class="border-b border-slate-800 bg-slate-900/80 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('demo.index') }}" class="text-lg font-semibold text-emerald-400">🔔 نظام الإشعارات</a>
            <nav class="flex gap-3 text-sm">
                <a href="{{ route('demo.user') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('demo.user') ? 'bg-slate-800 text-emerald-300' : 'text-slate-300' }}">مستخدم</a>
                <a href="{{ route('demo.admin') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('demo.admin') ? 'bg-slate-800 text-emerald-300' : 'text-slate-300' }}">أدمن</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <script src="{{ asset('js/notifications-demo.js') }}"></script>
</body>
</html>
