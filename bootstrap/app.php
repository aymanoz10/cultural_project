<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
        ]);

        // خطوة صفر: تسجيل وسيط مراقبة أداء الخادم لقياس زمني التطبيق وقاعدة البيانات في DevTools
        $middleware->append([
            \App\Http\Middleware\ServerTimingMiddleware::class,
        ]);

        // الثقة بوكلاء العكس (ngrok / أي reverse proxy) حتى تُولَّد الروابط بنفس
        // نطاق وبروتوكول الطلب الأصلي (https)، فيختفي خطأ Mixed Content على الأنفاق.
        $middleware->trustProxies(at: '*');

        // توحيد استجابات الـAPI كـ JSON (تُصبح أخطاء التحقق 422 بصيغة JSON بدل صفحات HTML)
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();