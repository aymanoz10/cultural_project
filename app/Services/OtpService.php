<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(
        private WhatsAppService $whatsApp
    ) {}

    /**
     * إنشاء OTP جديد وتشفيره وإرساله عبر WhatsApp
     */
    public function send(string $phone, string $purpose, array $payload = []): Otp
    {
        $phone = $this->normalizePhone($phone);

        // توليد كود عشوائي 6 أرقام
        $plainCode = (string) random_int(100000, 999999);

        // حذف OTPs القديمة لنفس الرقم والغرض
        Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->delete();

        // إنشاء OTP جديد (مع تشفير الكود)
        $otp = Otp::create([
            'phone'      => $phone,
            'code'       => Hash::make($plainCode),
            'purpose'    => $purpose,
            'payload'    => $payload,
            'expires_at' => now()->addMinutes(5),
        ]);

        // إرسال الكود عبر WhatsApp
        $this->whatsApp->sendOtp($phone, $plainCode);

        // ✅ للتطوير فقط: طباعة الكود في الـ Log

        return $otp;
    }

    /**
     * إعادة إرسال OTP موجود (بدون توليد كود جديد)
     * — يُستخدم عندما لا يصل الكود للمستخدم
     */
    public function resend(string $phone, Otp $otp): void
    {
        // استخراج الكود الأصلي من الـ Log أو إعادة إرسال الرسالة
        // ملاحظة: الكود مشفر في DB، لذا نعتمد على WhatsAppService
        // لإعادة إرسال الرسالة الأصلية

        // في UltraMsg لا يوجد "resend" API، نُرسل رسالة جديدة بنفس الكود
        // لكننا لا نستطيع فك تشفير الكود من Hash

        // ✅ الحل: توليد كود جديد وتحديث السجل
        $plainCode = (string) random_int(100000, 999999);

        $otp->update([
            'code'       => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(5),
            'attempts'   => 0,
        ]);

        $this->whatsApp->sendOtp($phone, $plainCode);

    }

    /**
     * التحقق من OTP
     */
    public function verify(string $phone, string $code, string $purpose): Otp
    {
        $phone = $this->normalizePhone($phone);

        $otp = Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => ['رمز التحقق غير موجود، اطلب رمزاً جديداً'],
            ]);
        }

        if ($otp->isExpired()) {
            $otp->delete();
            throw ValidationException::withMessages([
                'code' => ['انتهت صلاحية رمز التحقق'],
            ]);
        }

        $maxAttempts = (int) config('services.otp.max_attempts', 5);

        if ($otp->attempts >= $maxAttempts) {
            $otp->delete();
            throw ValidationException::withMessages([
                'code' => ['تجاوزت عدد المحاولات المسموح، اطلب رمزاً جديداً'],
            ]);
        }

        if (! Hash::check($code, $otp->code)) {
            $otp->increment('attempts');
            throw ValidationException::withMessages([
                'code' => ['رمز التحقق غير صحيح'],
            ]);
        }

        $otp->delete();

        return $otp;
    }

    /**
     * توحيد صيغة الرقم الدولية
     */
    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '963' . substr($phone, 1);
        }

        return $phone;
    }
}
