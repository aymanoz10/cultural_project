<?php

namespace App\Actions;

use App\Models\Activity;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * إنشاء حجز بشكل ذرّي: يقفل صفّ الفعالية داخل معاملة لمنع تجاوز السعة عند التزامن (Race Condition).
 *
 * ⚠️ ملف جاهز للربط — لم يُوصَل بعد لأنّك طلبت عدم تعديل المتحكّمات.
 * للتفعيل: استبدل جسم ReservationController@add بالسطر:
 *   $reservation = app(\App\Actions\CreateReservation::class)->handle($request->user(), $request->validate([...]));
 * ثم أطلق الحدث كالمعتاد: ReservationCreated::dispatch($reservation->load('activity','user'));
 */
final class CreateReservation
{
    public function handle(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            // قفل تشاؤمي على صفّ الفعالية حتى نهاية المعاملة
            $activity = Activity::query()->lockForUpdate()->findOrFail($data['activity_id']);

            $alreadyReserved = Reservation::query()
                ->where('user_id', $user->id)
                ->where('activity_id', $activity->id)
                ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_WAIT_LIST])
                ->exists();

            if ($alreadyReserved) {
                throw ValidationException::withMessages([
                    'activity_id' => 'لديك حجز مسبق لهذا النشاط',
                ]);
            }

            $confirmed = $activity->reservations()
                ->where('status', Reservation::STATUS_CONFIRMED)
                ->count();

            $status = ($activity->capacity === null || $confirmed < $activity->capacity)
                ? Reservation::STATUS_CONFIRMED
                : Reservation::STATUS_WAIT_LIST;

            return Reservation::create([
                'user_id'          => $user->id,
                'ticket_id'        => 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8)),
                'activity_id'      => $activity->id,
                'venue_id'         => $data['venue_id'] ?? null,
                'library_id'       => $data['library_id'] ?? null,
                'reservation_date' => $data['reservation_date'],
                'status'           => $status,
                // qr_code يُوقّع تلقائيًا عبر خطّاف saving في نموذج Reservation
            ]);
        });
    }
}
