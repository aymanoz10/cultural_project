<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_CONFIRMED = 'CONFIRMED'; // فعّالة (صالحة للدخول)
    public const STATUS_WAIT_LIST = 'WAIT_LIST'; // قائمة انتظار
    public const STATUS_COMPLETED = 'COMPLETED'; // مكتملة (تم تسجيل الحضور بالمسح)
    public const STATUS_CANCELLED = 'CANCELLED'; // ملغاة

    public const STATUS_LABELS = [
        self::STATUS_CONFIRMED => 'فعّالة',
        self::STATUS_WAIT_LIST => 'قائمة انتظار',
        self::STATUS_COMPLETED => 'مكتملة',
        self::STATUS_CANCELLED => 'ملغاة',
    ];

    protected $fillable = [
        'user_id',
        'ticket_id',
        'qr_payload',
        'activity_id',
        'venue_id',
        'library_id',
        'reservation_date',
        'seats_count',
        'status',
    ];

    /**
     * توليد المعرّف تلقائياً عند الإنشاء، والحمولة المشفّرة بعد الإنشاء.
     */
    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (! $reservation->ticket_id) {
                $reservation->ticket_id = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });

        static::created(function (Reservation $reservation) {
            if (empty($reservation->qr_payload)) {
                $reservation->qr_payload = $reservation->generateQrPayload();
                $reservation->saveQuietly();
            }
        });
    }

    /**
     * توليد حمولة QR مشفّرة تحوي معلومات الحاجز + الفعالية + التوقيت.
     * لا يمكن فكّها إلا على الخادم (بمفتاح APP_KEY) — مضادة للتزوير.
     */
    public function generateQrPayload(): string
    {
        $this->loadMissing('activity', 'user');

        return Crypt::encryptString(json_encode([
            'ticket_id'      => $this->ticket_id,
            'holder_name'    => $this->user?->name ?? 'ضيف',
            'user_id'        => $this->user_id,
            'activity_id'    => $this->activity_id,
            'activity_title' => $this->activity?->title ?? '',
            'activity_time'  => optional($this->activity?->start_time)->format('Y-m-d H:i'),
            'seats_count'    => $this->seats_count,
            'issued_at'      => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * فكّ تشفير حمولة QR ممسوحة (يعيد المصفوفة أو null عند الفشل).
     */
    public static function decodeQrPayload(string $payload): ?array
    {
        try {
            $decoded = json_decode(Crypt::decryptString($payload), true);
            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
