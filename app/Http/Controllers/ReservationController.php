<?php

namespace App\Http\Controllers;

use App\Events\ReservationCancelled as ReservationCancelledEvent;
use App\Events\ReservationCreated;
use App\Events\WaitListPromoted;
use App\Models\Activity;
use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'activity', 'venue'])
            ->where('user_id', $request->user()->id);

        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return ReservationResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'activity_id'      => 'required|exists:activities,id',
            'venue_id'         => 'nullable|exists:venues,id',
            'library_id'       => 'nullable|exists:libraries,id',
            'reservation_date' => 'required|date|after_or_equal:today',
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $user = $request->user();

        $existing = Reservation::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_WAIT_LIST])
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'لديك حجز مسبق لهذا النشاط'], 422);
        }

        $status = $activity->hasAvailableSeats()
            ? Reservation::STATUS_CONFIRMED
            : Reservation::STATUS_WAIT_LIST;

        $ticketId = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        $qrPayload = json_encode([
            'ticket_id'   => $ticketId,
            'user_id'     => $user->id,
            'activity_id' => $activity->id,
            'status'      => $status,
        ], JSON_UNESCAPED_UNICODE);

        $reservation = Reservation::create([
            'user_id'          => $user->id,
            'ticket_id'        => $ticketId,
            'qr_code'          => $qrPayload,
            'activity_id'      => $activity->id,
            'venue_id'         => $request->venue_id,
            'library_id'       => $request->library_id,
            'reservation_date' => $request->reservation_date,
            'status'           => $status,
        ]);

        $message = $status === Reservation::STATUS_WAIT_LIST
            ? 'تمت إضافتك إلى قائمة الانتظار'
            : 'تم تأكيد الحجز بنجاح';

        ReservationCreated::dispatch($reservation->load(['activity', 'user']));

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => new ReservationResource($reservation->load(['activity', 'venue'])),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $reservation = Reservation::with(['activity', 'venue', 'library'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => new ReservationResource($reservation)]);
    }

    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::where('user_id', $request->user()->id)->findOrFail($id);

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            return response()->json(['message' => 'الحجز ملغى مسبقاً'], 422);
        }

        $wasConfirmed = $reservation->status === Reservation::STATUS_CONFIRMED;
        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        if ($wasConfirmed) {
            $this->promoteFromWaitList($reservation->activity_id);
        }

        ReservationCancelledEvent::dispatch($reservation->load(['activity', 'user']));

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز',
            'data'    => new ReservationResource($reservation->fresh()),
        ]);
    }

    public function waitList($activityId)
    {
        $waitList = Reservation::with('user')
            ->where('activity_id', $activityId)
            ->where('status', Reservation::STATUS_WAIT_LIST)
            ->orderBy('created_at')
            ->get();

        return ReservationResource::collection($waitList);
    }

    private function promoteFromWaitList(int $activityId): void
    {
        $activity = Activity::find($activityId);

        if (! $activity || ! $activity->hasAvailableSeats()) {
            return;
        }

        $next = Reservation::where('activity_id', $activityId)
            ->where('status', Reservation::STATUS_WAIT_LIST)
            ->orderBy('created_at')
            ->first();

        if (! $next) {
            return;
        }

        $next->update([
            'status'  => Reservation::STATUS_CONFIRMED,
            'qr_code' => json_encode([
                'ticket_id'   => $next->ticket_id,
                'user_id'     => $next->user_id,
                'activity_id' => $activityId,
                'status'      => Reservation::STATUS_CONFIRMED,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        WaitListPromoted::dispatch($next->fresh(['activity', 'user']));
    }
}
