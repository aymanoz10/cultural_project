<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:admins,phone',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:super,admin,ticketsAdmin',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name'     => $request->name,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('admins', 'public');
        }

        $admin = Admin::create($data);
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'admin'   => $this->formatAdmin($admin),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('phone', $request->phone)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'مرحباً بك مجدداً',
            'admin'   => $this->formatAdmin($admin),
            'token'   => $token,
        ], 200);
    }

    public function get(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'admin'  => $this->formatAdmin($request->user()),
        ]);
    }

    public function update(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'phone'    => 'sometimes|required|string|unique:admins,phone,' . $admin->id,
            'password' => 'sometimes|required|string|min:8',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('admins', 'public');
        }

        $admin->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث البيانات بنجاح',
            'admin'   => $this->formatAdmin($admin->fresh()),
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (! Auth::guard('admin')->user()->isSuper()) {
            return response()->json(['message' => 'ليس لديك صلاحيات السوبر أدمن'], 403);
        }

        $admin = Admin::findOrFail($id);

        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'phone'  => 'sometimes|required|string|unique:admins,phone,' . $admin->id,
            'role'   => 'sometimes|required|in:super,admin,ticketsAdmin',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'role']);

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('admins', 'public');
        }

        $admin->update($data);

        return response()->json([
            'message' => 'تم التعديل بنجاح',
            'admin'   => $this->formatAdmin($admin->fresh()),
        ], 200);
    }

    public function remove($id)
    {
        if (! Auth::guard('admin')->user()->isSuper()) {
            return response()->json(['message' => 'لا تملك صلاحية الحذف'], 403);
        }

        $admin = Admin::findOrFail($id);
        if ($admin->avatar) {
            Storage::disk('public')->delete($admin->avatar);
        }
        $admin->delete();

        return response()->json(['message' => 'تم حذف الأدمن بنجاح'], 200);
    }

public function logout(Request $request)
{
    // 1. إذا كان المستخدم مسجلاً عبر Sanctum API Token
    if ($request->user() && $request->user()->currentAccessToken()) {
        $request->user()->currentAccessToken()->delete();
    }

    // 2. إذا كان الحساب مسجلاً عبر Web Session (Admin Guard)
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
    } else {
        Auth::logout();
    }

    // إلغاء الجلسة وإعادة توليد توكن CSRF للـ Web
    if ($request->hasSession()) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    // 3. تحديد نوع الاستجابة (JSON للـ API أو Redirect للـ Blade)
    if ($request->wantsJson()) {
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    return redirect()->route('admin.login')->with('success', 'تم تسجيل الخروج بنجاح');
}

    private function formatAdmin(Admin $admin): array
    {
        return [
            'id'     => $admin->id,
            'name'   => $admin->name,
            'phone'  => $admin->phone,
            'role'   => $admin->role,
            'avatar' => $admin->avatar ? asset('storage/' . $admin->avatar) : null,
        ];
    }
}
