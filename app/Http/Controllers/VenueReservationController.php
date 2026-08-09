<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Venue;
use App\Models\VenueReservation;
use App\Http\Resources\VenueReservationResource;
use App\Notifications\NewVenueReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class VenueReservationController extends Controller
{
    /**
     * عرض قائمة حجوزات القاعات (واجهة إدارة)
     */
    public function webIndex(Request $request)
    {
        $query = VenueReservation::with('venue')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->filled('search')) {
            $term = "%{$request->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('applicant_name', 'ilike', $term)
                  ->orWhere('requesting_party', 'ilike', $term)
                  ->orWhere('national_id_number', 'ilike', $term);
            });
        }

        $reservations = $query->paginate(12)->withQueryString();

        $counts = VenueReservation::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $stats = [
            'total'     => VenueReservation::count(),
            'pending'   => (int) ($counts[VenueReservation::STATUS_PENDING]   ?? 0),
            'accepted'  => (int) ($counts[VenueReservation::STATUS_ACCEPTED]  ?? 0),
            'approved'  => (int) ($counts[VenueReservation::STATUS_APPROVED]  ?? 0),
            'rejected'  => (int) ($counts[VenueReservation::STATUS_REJECTED]  ?? 0),
            'cancelled' => (int) ($counts[VenueReservation::STATUS_CANCELLED] ?? 0),
        ];

        $venues = Venue::all();

        return view('admin.venue_reservations.index', compact('reservations', 'stats', 'venues'));
    }

    /**
     * عرض تفاصيل حجز قاعة واحد (واجهة إدارة)
     */
    public function showWeb($id)
    {
        $reservation = VenueReservation::with(['venue.culturalCenter', 'user'])->findOrFail($id);

        return view('admin.venue_reservations.show', compact('reservation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venue_id'           => 'required|exists:venues,id',
            'requesting_party'   => 'required|string|max:255',
            'applicant_name'     => 'required|string|max:255',
            'national_id_number' => 'required|string|max:20',
            'reservation_reason' => 'required|string|max:255',
            'event_description'  => 'nullable|string',
            'is_public'          => 'required|boolean',
            'start_time'         => 'required|date|after:now',
            'end_time'           => 'required|date|after:start_time',
        ]);

        if ($this->hasConflict($request->venue_id, $request->start_time, $request->end_time)) {
            return response()->json([
                'message' => 'يوجد حجز في هذا المكان خلال الوقت المطلوب أو خلال 3 ساعات قبل أو بعده',
            ], 422);
        }

        $reservation = VenueReservation::create([
            'user_id'            => $request->user()->id,
            'venue_id'           => $request->venue_id,
            'requesting_party'   => $request->requesting_party,
            'applicant_name'     => $request->applicant_name,
            'national_id_number' => $request->national_id_number,
            'reservation_reason' => $request->reservation_reason,
            'event_description'  => $request->event_description,
            'is_public'          => $request->is_public,
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'status'             => VenueReservation::STATUS_PENDING,
        ]);

        // إشعار لوحة التحكم: المشرف العام + مشرف مركز القاعة
        $this->notifyAdminsOfNewReservation($reservation->load('venue'));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب الحجز بنجاح',
            'data'    => new VenueReservationResource($reservation),
        ], 201);
    }

    /**
     * إشعار المشرفين المعنيين بطلب حجز قاعة جديد (super + مشرف المركز).
     */
    private function notifyAdminsOfNewReservation(VenueReservation $reservation): void
    {
        $centerId = $reservation->venue?->cultural_center_id;

        $admins = Admin::query()
            ->where('role', 'super')
            ->when($centerId, function ($q) use ($centerId) {
                $q->orWhere(fn ($sub) => $sub->where('role', 'admin')->where('center_id', $centerId));
            })
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewVenueReservation($reservation));
        }
    }

    public function index(Request $request)
    {
        $query = VenueReservation::with('venue');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('applicant_name', 'like', "%{$request->search}%")
                  ->orWhere('requesting_party', 'like', "%{$request->search}%")
                  ->orWhere('national_id_number', 'like', "%{$request->search}%");
            });
        }

        return VenueReservationResource::collection($query->latest()->get());
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,approved,rejected,cancelled',
            'notes'  => 'nullable|string',
        ]);

        $reservation = VenueReservation::findOrFail($id);

        if (! in_array($request->status, $reservation->allowedTransitions(), true)) {
            return response()->json([
                'message' => 'لا يمكن تغيير الحالة من "' . $reservation->status . '" إلى "' . $request->status . '"',
            ], 422);
        }

        $reservation->update([
            'status' => $request->status,
            'notes'  => $request->notes ?? $reservation->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الحجز بنجاح',
            'data'    => new VenueReservationResource($reservation->fresh()->load('venue')),
        ]);
    }

    private function hasConflict(int $venueId, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $buffer = 3; // hours

        $newStart = \Carbon\Carbon::parse($startTime)->subHours($buffer);
        $newEnd   = \Carbon\Carbon::parse($endTime)->addHours($buffer);

        $query = VenueReservation::where('venue_id', $venueId)
            ->whereIn('status', [VenueReservation::STATUS_ACCEPTED, VenueReservation::STATUS_APPROVED])
            ->where('start_time', '<', $newEnd)
            ->where('end_time', '>', $newStart);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
