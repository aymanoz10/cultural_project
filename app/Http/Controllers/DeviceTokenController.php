<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token'    => 'required|string|max:500',
            'platform' => 'nullable|string|in:android,ios,web',
        ]);

        $user = $request->user();

        $user->deviceTokens()->updateOrCreate(
            ['token' => $request->token],
            ['platform' => $request->platform ?? 'web']
        );

        return response()->json(['success' => true, 'message' => 'تم تسجيل الجهاز']);
    }

    public function destroy(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $request->user()->deviceTokens()->where('token', $request->token)->delete();

        return response()->json(['success' => true]);
    }
}
