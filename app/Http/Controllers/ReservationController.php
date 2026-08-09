<?php

namespace App\Http\Controllers;

use App\Events\ReservationCancelled as ReservationCancelledEvent;
use App\Events\ReservationCreated;
use App\Events\WaitListPromoted;
use App\Http\Resources\ReservationResource;
use App\Models\Activity;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    private const DEFAULT_MAX_SEATS_PER_USER = 5;

    /**
     * الحالة الابتدائية الصحيحة عند تخصيص مقعد فعلياً لمستخدم:
     * فعالية مجانية → فعّالة مباشرة (لا شيء يُدفع).
     * فعالية مدفوعة → "غير مدفوعة" بانتظار الدفع، حتى يُمسح الباركود
     * (الدفع نقداً عند الحضور) أو تُضاف بوابة دفع إلكتروني لاحقاً.
     */
    private function initialSeatStatus(Activity $activity): string
    {
        $isPaid = $activity->ticket_price !== null && (float) $activity->ticket_price > 0;

        return $isPaid ? Reservation::STATUS_PENDING_PAYMENT : Reservation::STATUS_CONFIRMED;
    }

    /**
     * عرض كافة حجوزات المستخدم الحالي
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'activity.activityType', 'activity.culturalCenter', 'activity.venue', 'venue'])
            ->where('user_id', $request->user()->id);

        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = max(1, min($request->integer('per_page', 10), 100));

        return ReservationResource::collection(
            $query->latest()->paginate($perPage)
        );
    }

    /**
     * إنشاء حجز جديد (مع دعم Idempotency وتجزيء الحجز والترتيب الآمن للأقفال)
     */
    public function add(Request $request)
    {
        // 1. التحقق من صحة الإدخال
        $request->validate([
            'activity_id'      => 'required|exists:activities,id',
            'venue_id'         => 'nullable|exists:venues,id',
            'library_id'       => 'nullable|exists:libraries,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'seats_count'      => 'required|integer|min:1|max:10',
            'allow_partial'    => 'nullable|boolean',
        ]);

        $userId         = $request->user()->id;
        $idempotencyKey = $request->header('X-Idempotency-Key');

        // 2. فحص مفتاح التوافقية (Idempotency Key) لمنع التكرار عند النقر المزدوج
        if ($idempotencyKey) {
            $cacheKey = "idempotency:reservation:{$userId}:{$idempotencyKey}";
            $existingId = Cache::get($cacheKey);

            if ($existingId) {
                $existingReservation = Reservation::with(['activity', 'venue'])->find($existingId);
                if ($existingReservation) {
                    return response()->json([
                        'success' => true,
                        'message' => 'تم استرجاع الحجز المسجل مسبقاً',
                        'data'    => $existingReservation,
                    ], 200);
                }
            }
        }

        $requestedSeats = (int) $request->seats_count;
        $allowPartial   = $request->boolean('allow_partial', false);

        // 3. تنفيذ المعاملة بكفالة القفل المتشائم بالترتيب الموحد
        $result = DB::transaction(function () use ($request, $userId, $requestedSeats, $allowPartial) {
            
            // الترتيب الذهبي: قفل Activity دائماً في البداية
            $activity = Activity::where('id', $request->activity_id)
                ->lockForUpdate()
                ->firstOrFail();

            $maxAllowedSeats = $activity->max_seats_per_user ?? self::DEFAULT_MAX_SEATS_PER_USER;

            // التحقق من السقف الإجمالي لمقاعد المستخدم
            $userExistingSeats = Reservation::where('user_id', $userId)
                ->where('activity_id', $activity->id)
                ->whereIn('status', [
                    Reservation::STATUS_CONFIRMED,
                    Reservation::STATUS_PENDING_PAYMENT,
                    Reservation::STATUS_WAIT_LIST,
                ])
                ->sum('seats_count');

            if (($userExistingSeats + $requestedSeats) > $maxAllowedSeats) {
                throw ValidationException::withMessages([
                    'seats_count' => ["تجاوزت الحد الأقصى المسموح به للمقاعد لهذا النشاط ({$maxAllowedSeats} مقاعد)"],
                ]);
            }

            // حساب المقاعد المتاحة (الحجوزات "غير المدفوعة" تشغل مقعداً أيضاً بانتظار الدفع)
            $confirmedSeatsSum = Reservation::where('activity_id', $activity->id)
                ->whereIn('status', Reservation::SEAT_OCCUPYING_STATUSES)
                ->sum('seats_count');

            $availableSeats = max(0, $activity->capacity - $confirmedSeatsSum);

            // الخيار أ: تجزيء الحجز (Split Booking) في حال تفعيل allow_partial وجود توفر جزئي
            if ($allowPartial && $availableSeats > 0 && $availableSeats < $requestedSeats) {
                $confirmedCount = $availableSeats;
                $waitlistCount  = $requestedSeats - $availableSeats;

                // حجز المقاعد المتاحة (فعّالة مباشرة إن كانت مجانية، أو "غير مدفوعة" إن كانت مدفوعة)
                $confirmedRes = $this->makeReservationRecord($activity, $request->user(), $request, $confirmedCount, $this->initialSeatStatus($activity));
                ReservationCreated::dispatch($confirmedRes->loadMissing(['activity', 'user']));

                // إضافة المتبقي للانتظار
                $waitlistRes = $this->makeReservationRecord($activity, $request->user(), $request, $waitlistCount, Reservation::STATUS_WAIT_LIST);
                ReservationCreated::dispatch($waitlistRes->loadMissing(['activity', 'user']));

                return [
                    'is_split'  => true,
                    'confirmed' => $confirmedRes->loadMissing(['activity', 'venue']),
                    'wait_list' => $waitlistRes->loadMissing(['activity', 'venue']),
                    'message'   => "تم تأكيد {$confirmedCount} مقاعد، وإضافة {$waitlistCount} مقعد إلى قائمة الانتظار",
                ];
            }

            // الخيار ب: النقل الكامل (Atomic Allocation)
            $status = ($availableSeats >= $requestedSeats)
                ? $this->initialSeatStatus($activity)
                : Reservation::STATUS_WAIT_LIST;

            $reservation = $this->makeReservationRecord($activity, $request->user(), $request, $requestedSeats, $status);
            ReservationCreated::dispatch($reservation->loadMissing(['activity', 'user']));

            return [
                'is_split'    => false,
                'reservation' => $reservation->loadMissing(['activity', 'venue']),
                'status'      => $status,
            ];
        });

        // 4. حفظ المعرف في الـ Cache لخاصية Idempotency
        if ($idempotencyKey) {
            $savedId = $result['is_split'] ? $result['confirmed']->id : $result['reservation']->id;
            Cache::put("idempotency:reservation:{$userId}:{$idempotencyKey}", $savedId, now()->addDay());
        }

        // 5. صياغة الرد للمستخدم
        if ($result['is_split']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data'    => [
                    'confirmed' => $result['confirmed'],
                    'wait_list' => $result['wait_list'],
                ],
            ], 201);
        }

        $message = match ($result['status']) {
            Reservation::STATUS_WAIT_LIST => 'تمت إضافة طلبك إلى قائمة الانتظار لعدم توفر كامل المقاعد المطلوبة',
            Reservation::STATUS_PENDING_PAYMENT => 'تم حجز مقعدك، بانتظار إتمام الدفع لتأكيد الفعالية',
            default => 'تم تأكيد الحجز بنجاح',
        };

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $result['reservation'],
        ], 201);
    }

    /**
     * عرض تفاصيل حجز محدد
     */
    public function show(Request $request, $id)
    {
        $reservation = Reservation::with(['activity.activityType', 'activity.culturalCenter', 'activity.venue', 'venue', 'library'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return new ReservationResource($reservation);
    }

    /**
     * إلغاء حجز وترقية الانتظار بأمان ضد Deadlock
     */
    public function cancel(Request $request, $id)
    {
        $userId = $request->user()->id;

        $reservation = DB::transaction(function () use ($id, $userId) {
            
            // أ) استعلام مبدئي لتحديد الفعالية بدون قفل
            $unlockedReservation = Reservation::where('user_id', $userId)
                ->select('id', 'activity_id')
                ->findOrFail($id);

            // ب) القفل الأول: Activity دائماً أولاً تفادياً لـ Deadlock مع دالة add()
            $activity = Activity::where('id', $unlockedReservation->activity_id)
                ->lockForUpdate()
                ->firstOrFail();

            // ج) القفل الثاني: Reservation ثانياً
            $reservation = Reservation::where('id', $id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservation->status === Reservation::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'reservation' => ['الحجز ملغى مسبقاً'],
                ]);
            }

            if ($reservation->status === Reservation::STATUS_COMPLETED) {
                throw ValidationException::withMessages([
                    'reservation' => ['لا يمكن إلغاء تذكرة تم تسجيل حضورها'],
                ]);
            }

            // ✅ الحجز "غير المدفوعة" (PENDING_PAYMENT) تشغل مقعداً فعلياً تماماً
            // مثل "فعّالة" (CONFIRMED)، فيجب إرجاع المقعد وترقية قائمة الانتظار
            // عند إلغاء أيٍّ من الحالتين، وليس CONFIRMED فقط كما كان سابقاً.
            $wasSeatOccupying = in_array($reservation->status, Reservation::SEAT_OCCUPYING_STATUSES, true);
            $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

            // د) ترقية قائمة الانتظار عند إلغاء حجز كان يشغل مقعداً
            if ($wasSeatOccupying) {
                $this->promoteFromWaitListBounded($activity);
            }

            ReservationCancelledEvent::dispatch($reservation->load(['activity', 'user']));

            return $reservation->fresh(['activity', 'venue']);
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data'    => new ReservationResource($reservation),
        ]);
    }

    /**
     * عرض قائمة الانتظار لفعالية معينة
     */
    public function waitList($activityId)
    {
        $waitList = Reservation::with('user')
            ->where('activity_id', $activityId)
            ->where('status', Reservation::STATUS_WAIT_LIST)
            ->orderBy('created_at')
            ->get();

        return response()->json(['data' => $waitList]);
    }

    // ==========================================
    // Private Helper Methods (دوال مساعدة خاصة)
    // ==========================================

    /**
     * دالة مساعدة لإنشاء سجل الحجز وتوليد Ticket ID و QR Payload
     */
    private function makeReservationRecord(Activity $activity, $user, Request $request, int $seatsCount, string $status): Reservation
    {
        $reservation = new Reservation([
            'user_id'          => $user->getKey(),
            'activity_id'      => $activity->getKey(),
            'venue_id'         => $request->venue_id ?? null,
            'library_id'       => $request->library_id ?? null,
            'reservation_date' => $request->reservation_date,
            'seats_count'      => $seatsCount,
            'status'           => $status,
        ]);

        // تمرير الفعالية (المقفولة) والمستخدم المُحمّلَين مسبقاً يمنع generateQrPayload من
        // إعادة استعلامهما (loadMissing) داخل نافذة القفل؛ وticket_id + qr_payload يُولَّدان
        // في خطّاف creating، فتكفي كتابة واحدة (INSERT) لكل حجز — بلا UPDATE ثانٍ.
        $reservation->setRelation('activity', $activity);
        $reservation->setRelation('user', $user);
        $reservation->save();

        return $reservation;
    }

    /**
     * ترقية قائمة الانتظار بطريقة محدودة تمنع الحلقات اللانهائية (Bounded Batch Promotion)
     */
    private function promoteFromWaitListBounded(Activity $activity): void
    {
        $confirmedSeatsSum = Reservation::where('activity_id', $activity->id)
            ->whereIn('status', Reservation::SEAT_OCCUPYING_STATUSES)
            ->sum('seats_count');

        $availableSeats = max(0, $activity->capacity - $confirmedSeatsSum);

        if ($availableSeats <= 0) {
            return;
        }

        // جلب أول 20 طلب انتظار مناسبين فقط لحماية أداء السيرفر
        $candidates = Reservation::where('activity_id', $activity->id)
            ->where('status', Reservation::STATUS_WAIT_LIST)
            ->where('seats_count', '<=', $availableSeats)
            ->orderBy('created_at')
            ->limit(20)
            ->lockForUpdate()
            ->get();

        $promotedStatus = $this->initialSeatStatus($activity);

        foreach ($candidates as $candidate) {
            if ($candidate->seats_count > $availableSeats) {
                continue;
            }

            // الحمولة المشفّرة مستقلّة عن الحالة (تُقرأ الحالة من قاعدة البيانات عند المسح)
            $candidate->status = $promotedStatus;
            $candidate->save();

            $availableSeats -= $candidate->seats_count;

            WaitListPromoted::dispatch($candidate->fresh(['activity', 'user']));

            if ($availableSeats <= 0) {
                break;
            }
        }
    }
}