<?php

namespace App\Http\Controllers; // <-- التعديل هنا (إزالة \Admin)
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    /**
     * عرض كافة حجوزات النظام داخل لوحة التحكم (Blade View)
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'activity', 'venue']);

        // بحث بالاسم أو البريد أو رقم التذكرة
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة بحسب الفعالية
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // فلترة بحسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->latest()->paginate(15)->withQueryString();
        $activities   = Activity::select('id', 'title')->get();

        // إحصائيات الحجوزات العامة
        $stats = [
            'total'     => Reservation::count(),
            'confirmed' => Reservation::where('status', Reservation::STATUS_CONFIRMED)->count(),
            'wait_list' => Reservation::where('status', Reservation::STATUS_WAIT_LIST)->count(),
            'cancelled' => Reservation::where('status', Reservation::STATUS_CANCELLED)->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'activities', 'stats'));
    }

    /**
     * إلغاء حجز مباشر من الأدمن
     */
    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        return back()->with('success', 'تم إلغاء الحجز بنجاح.');
    }
}