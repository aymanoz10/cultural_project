<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendRegisterOtpRequest;
use App\Http\Requests\VerifyRegisterOtpRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * إرسال OTP للتسجيل عبر WhatsApp
     * — يُستخدم مرة واحدة فقط كل 60 ثانية لنفس الرقم
     * (فحص المدخلات يتم تلقائياً عبر Form Request: SendRegisterOtpRequest)
     */
    public function sendRegisterOtp(SendRegisterOtpRequest $request)
    {
        $phone = $this->otpService->normalizePhone($request->phone);

        // حماية Rate Limiting: منع إرسال OTP بشكل متكرر (60 ثانية)
        $key = 'otp-register:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'يرجى الانتظار ' . $seconds . ' ثانية قبل إعادة الإرسال',
            ], Response::HTTP_TOO_MANY_REQUESTS); // 429
        }

        if (User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف مسجل مسبقاً'], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        $this->otpService->send($phone, Otp::PURPOSE_REGISTER, $request->only(['name', 'date_of_birth', 'gender']));
        RateLimiter::hit($key, 60); // تسجيل المحاولة (تنتهي بعد 60 ثانية)

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إرسال رمز التحقق عبر WhatsApp',
            'phone'   => $phone,
        ]);
    }

    /**
     * إعادة إرسال OTP للتسجيل
     * — يتحقق من وجود OTP سابق غير منتهٍ قبل إعادة الإرسال
     * — يُسمح بإعادة الإرسال بعد 60 ثانية فقط
     */
    public function resendRegisterOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->otpService->normalizePhone($request->phone);

        // Rate Limiting منفصل لإعادة الإرسال
        $key = 'otp-register-resend:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'يرجى الانتظار ' . $seconds . ' ثانية قبل إعادة الإرسال',
            ], Response::HTTP_TOO_MANY_REQUESTS); // 429
        }

        if (User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف مسجل مسبقاً'], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        // التحقق من وجود OTP سابق غير منتهٍ
        $existingOtp = Otp::where('phone', $phone)
            ->where('purpose', Otp::PURPOSE_REGISTER)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $existingOtp) {
            return response()->json([
                'message' => 'لا يوجد رمز تحقق نشط، يرجى طلب رمز جديد',
            ], Response::HTTP_BAD_REQUEST); // 400
        }

        // إعادة إرسال نفس الـ OTP (بدون توليد كود جديد)
        $this->otpService->resend($phone, $existingOtp);
        RateLimiter::hit($key, 60);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إعادة إرسال رمز التحقق عبر WhatsApp',
            'phone'   => $phone,
        ]);
    }

    /**
     * التحقق من OTP وإنشاء الحساب
     * — يقبل multipart/form-data فقط إذا أُرفقت صورة (avatar)
     * (فحص المدخلات يتم تلقائياً عبر Form Request: VerifyRegisterOtpRequest)
     */
    public function verifyRegisterOtp(VerifyRegisterOtpRequest $request)
    {
        $phone = $this->otpService->normalizePhone($request->phone);
        $otp = $this->otpService->verify($phone, $request->code, Otp::PURPOSE_REGISTER);

        // حارس أمني: يمنع انهيار السيرفر إذا انتهت صلاحية الـ OTP أو حُذفت
        if (! $otp || empty($otp->payload)) {
            return response()->json(['message' => 'بيانات التحقق غير صالحة أو منتهية'], Response::HTTP_BAD_REQUEST); // 400
        }

        if (User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف مسجل مسبقاً'], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        $user = User::create(array_merge($otp->payload, [
            'phone' => $phone,
        ]));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إنشاء الحساب بنجاح',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], Response::HTTP_CREATED); // 201
    }

    /**
     * إرسال OTP لتسجيل الدخول عبر WhatsApp
     * — يُستخدم مرة واحدة فقط كل 60 ثانية لنفس الرقم
     */
    public function sendLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->otpService->normalizePhone($request->phone);

        // حماية Rate Limiting: منع إرسال OTP بشكل متكرر
        $key = 'otp-login:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'يرجى الانتظار ' . $seconds . ' ثانية قبل إعادة الإرسال',
            ], Response::HTTP_TOO_MANY_REQUESTS); // 429
        }

        if (! User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف غير مسجل'], Response::HTTP_NOT_FOUND); // 404
        }

        $this->otpService->send($phone, Otp::PURPOSE_LOGIN);
        RateLimiter::hit($key, 60);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إرسال رمز التحقق عبر WhatsApp',
            'phone'   => $phone,
        ]);
    }

    /**
     * إعادة إرسال OTP لتسجيل الدخول
     * — يتحقق من وجود OTP سابق غير منتهٍ قبل إعادة الإرسال
     * — يُسمح بإعادة الإرسال بعد 60 ثانية فقط
     */
    public function resendLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->otpService->normalizePhone($request->phone);

        // Rate Limiting منفصل لإعادة الإرسال
        $key = 'otp-login-resend:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'يرجى الانتظار ' . $seconds . ' ثانية قبل إعادة الإرسال',
            ], Response::HTTP_TOO_MANY_REQUESTS); // 429
        }

        if (! User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'رقم الهاتف غير مسجل'], Response::HTTP_NOT_FOUND); // 404
        }

        // التحقق من وجود OTP سابق غير منتهٍ
        $existingOtp = Otp::where('phone', $phone)
            ->where('purpose', Otp::PURPOSE_LOGIN)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $existingOtp) {
            return response()->json([
                'message' => 'لا يوجد رمز تحقق نشط، يرجى طلب رمز جديد',
            ], Response::HTTP_BAD_REQUEST); // 400
        }

        // إعادة إرسال نفس الـ OTP (بدون توليد كود جديد)
        $this->otpService->resend($phone, $existingOtp);
        RateLimiter::hit($key, 60);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إعادة إرسال رمز التحقق عبر WhatsApp',
            'phone'   => $phone,
        ]);
    }

    /**
     * التحقق من OTP وتسجيل الدخول
     * — يُرجع توكن Sanctum للمصادقة في الطلبات المستقبلية
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string|size:6',
        ]);

        $phone = $this->otpService->normalizePhone($request->phone);
        $otp = $this->otpService->verify($phone, $request->code, Otp::PURPOSE_LOGIN);

        // 🔴 إصلاح أمني: التحقق من صلاحية الـ OTP قبل إصدار التوكن
        if (! $otp) {
            return response()->json(['message' => 'رمز التحقق غير صالح أو منتهي الصلاحية'], Response::HTTP_BAD_REQUEST); // 400
        }

        $user = User::where('phone', $phone)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user'   => new UserResource($user),
            'token'  => $token,
        ]);
    }

    /**
     * تسجيل الخروج وإتلاف الرمز التعبيري الحالي
     * — يتطلب توكن صالح في الهيدر Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        // التحقق من وجود توكن قبل الحذف (يمنع خطأ إذا استُدعي بدون توكن)
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * جلب بيانات الملف الشخصي للمستخدم الحالي
     * — يتطلب توكن صالح
     */
    public function get(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => new UserResource($request->user()),
        ]);
    }

    /**
     * تحديث البيانات الشخصية العامة
     * — يتم استبعاد حقل الـ phone لحماية أمن النظام ومنع التلاعب بالهوية بدون OTP
     * — يتطلب توكن صالح
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->update($request->only([
            'name',
            'date_of_birth',
            'gender',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث البيانات بنجاح',
            'user'    => new UserResource($user->fresh()),
        ]);
    }

    /**
     * رفع أو تحديث صورة المستخدم
     * — يُستخدم بشكل منفصل عن التسجيل أو التحديث العام
     * — يتطلب توكن صالح
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB كحد أقصى
        ]);

        $user = $request->user();

        // حذف الصورة القديمة إن وجدت (اختياري)
        // if ($user->avatar) { Storage::disk('public')->delete($user->avatar); }

        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث الصورة بنجاح',
            'avatar'  => asset('storage/' . $avatarPath),
            'user'    => new UserResource($user->fresh()),
        ]);
    }
}
