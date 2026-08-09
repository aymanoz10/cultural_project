<?php

namespace App\Listeners;

use App\Events\ReservationCancelled;
use App\Notifications\ReservationCancelled as ReservationCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationCancelledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ حاسم: بدون هذا، لو استغرقت معالجة الإشعار (إرسال FCM لعدة توكنات،
     * كل واحد بمهلة 10 ثوانٍ + إعادة محاولتين) وقتاً أطول من retry_after
     * المُعرَّف بـ config/queue.php، يعتبر Laravel هذا الـJob "ضائعاً"
     * ويُعيد جدولته تلقائياً بينما الأصلي لا يزال يعمل — فيُنفَّذ الإشعار
     * أكثر من مرة لنفس الحدث الواحد فقط (وهذا كان يحدث فعلياً).
     */
    public $tries = 1;
    public $timeout = 120;

    public function handle(ReservationCancelled $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $reservation->user?->notify(new ReservationCancelledNotification($reservation));
    }
}
