<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // إضافة ترويسة الكاش للملفات الثابتة والصور (تخزين لمدة سنة)
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');

        return $response;
    }
}