<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ServerTimingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $dbTime = 0;

        DB::listen(function ($query) use (&$dbTime) {
            $dbTime += $query->time; // بالميلي ثانية
        });

        $response = $next($request);

        $totalTime = (microtime(true) - $startTime) * 1000;

        // حقن الترويسة لتظهر في Network tab -> Headers -> Server-Timing
        $response->headers->set('Server-Timing', "app;dur={$totalTime}, db;dur={$dbTime}");

        return $response;
    }
}