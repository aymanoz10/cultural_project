<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CulturalCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    public function index()
    {
        $users = Admin::all(); 
        return view('admin.users.index', compact('users'));
    }

    public function profile()
    {
        $admin = auth('admin')->user();
        return view('admin.profile', compact('admin'));
    }

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

        Auth::guard('admin')->login($admin);

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success'  => true,
            'admin'    => $this->formatAdmin($admin),
            'token'    => $token,
            'redirect' => route('admin.dashboard')
        ], 201);
    }
public function store(Request $request)
{
    // 1. التحقق من صلاحيات السوبر أدمن
    if (! Auth::guard('admin')->user()?->isSuper()) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'ليس لديك صلاحيات كافية'], 403);
        }
        return redirect()->back()->with('error', 'ليس لديك صلاحيات كافية');
    }

    // 2. التحقق من البيانات (تأكد من إدخال 8 أحرف لكلمة المرور)
    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'phone'     => 'required|string|unique:admins,phone',
        'password'  => 'required|string|min:8|confirmed',
        'role'      => 'required|string',
        'center_id' => 'nullable|exists:centers,id',
        'status'    => 'nullable|string',
        'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // 3. تجهيز البيانات للحفظ
    $data = [
        'name'      => $request->name,
        'phone'     => $request->phone,
        'password'  => Hash::make($request->password),
        'role'      => $request->role,
        'center_id' => $request->center_id ?: null,
        'status'    => $request->status ?? 'active',
    ];

    if ($request->hasFile('avatar')) {
        $data['avatar'] = $request->file('avatar')->store('admins', 'public');
    }

    // 4. إنشاء الحساب
    Admin::create($data);

    if ($request->wantsJson()) {
        return response()->json([
            'status'  => 'success',
            'message' => 'تم إنشاء المستخدم بنجاح'
        ], 201);
    }

    return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
}

    public function create()
    {
        $centers = CulturalCenter::all(); 
        return view('admin.users.create', compact('centers'));
    }

 public function login(Request $request)
{
    // 1. التحقق من البيانات المدخلة
    $credentials = $request->validate([
        'phone'    => 'required',
        'password' => 'required',
    ]);

    // 2. محاولة تسجيل الدخول عبر حارس الأدمن (Session Guard)
    if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
        
        // 🟢 خطوة حاسمة: تجديد الجلسة لتثبيت كوكيز الدخول في المتصفح
        $request->session()->regenerate();

        // جلب بيانات الأدمن الذي سجل دخوله للتو
        $admin = Auth::guard('admin')->user();

        // تحديد مسار التوجيه بناءً على دور الأدمن
        $redirectUrl = route('admin.dashboard'); // المسار الافتراضي للإدارة

        if ($admin->role === 'ticketsAdmin') {
            $redirectUrl = route('admin.barcode.scan'); // مسار مسؤول التذاكر
        }

        return response()->json([
            'message'  => 'تم تسجيل الدخول بنجاح',
            'redirect' => $redirectUrl
        ], 200);
    }

    // 3. في حال فشل البيانات
    return response()->json([
        'message' => 'بيانات الدخول غير صحيحة'
    ], 422);
}
    public function get(Request $request)
    {
        $admin = Auth::guard('admin')->user() ?? $request->user('admin');

        return response()->json([
            'status' => 'success',
            'admin'  => $admin ? $this->formatAdmin($admin) : null,
        ]);
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'المستخدم غير محدد أو انتهت الجلسة'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'انتهت الجلسة، يرجى تسجيل الدخول');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:admins,phone,' . $admin->id,
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        $admin->name  = $request->name;
        $admin->phone = $request->phone;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $admin->avatar = $request->file('avatar')->store('admins', 'public');
        }

        $admin->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => 'تم تحديث الملف الشخصي بنجاح',
                'saved_path' => $admin->avatar
            ]);
        }

        return redirect()->route('admin.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
    
    public function edit(Request $request, $id)
    {
        if (! Auth::guard('admin')->user()?->isSuper()) {
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

    public function remove(Request $request, $id)
    {
        if (! Auth::guard('admin')->user()?->isSuper()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'لا تملك صلاحية الحذف'], 403);
            }
            return redirect()->back()->with('error', 'لا تملك صلاحية الحذف');
        }

        $admin = Admin::findOrFail($id);
        
        if ($admin->avatar) {
            Storage::disk('public')->delete($admin->avatar);
        }
        
        $admin->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'تم حذف الأدمن بنجاح'], 200);
        }

        return redirect()->route('admin.users.index')->with('success', 'تم حذف الأدمن بنجاح');
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::logout();
        }

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
        }

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    private function formatAdmin(Admin $admin): array
    {
        return [
            'id'     => $admin->id,
            'name'   => $admin->name,
            'phone'  => $admin->phone,
            'role'   => $admin->role,
            'avatar' => $admin->avatar ? asset('storage/' . ltrim($admin->avatar, '/')) : null,
        ];
    }
}