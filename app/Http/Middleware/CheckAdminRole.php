<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ❌ الخطأ الشائع: Auth::user() أو auth()->user()
        // ✅ الصح: تحديد guard('admin') صراحة
        $admin = Auth::guard('admin')->user();

        if (! $admin || ! in_array($admin->role, $roles)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالوصول'], 403);
            }
            return redirect()->route('login')->with('error', 'غير مصرح لك بالوصول');
        }

        return $next($request);
    }
}   