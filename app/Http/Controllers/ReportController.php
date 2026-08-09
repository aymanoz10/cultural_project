<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Book;
use App\Models\CulturalCenter;
use App\Models\Library;
use App\Models\Reservation;
use App\Models\VenueReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * صفحة التقارير (معاينة تفاعلية + أزرار التصدير)
     */
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $context = $this->scopeContext();
        $report  = $this->buildReport($from, $to, $context['is_super'], $context['center_id']);

        return view('admin.reports.index', array_merge($context, [
            'from'   => $from,
            'to'     => $to,
            'report' => $report,
        ]));
    }

    /**
     * تصدير التقرير بصيغة Word (.doc) أو PDF (عبر الطباعة)
     */
    public function export(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $context = $this->scopeContext();
        $report  = $this->buildReport($from, $to, $context['is_super'], $context['center_id']);

        $format = $request->query('format', 'pdf') === 'word' ? 'word' : 'pdf';

        $data = array_merge($context, [
            'from'         => $from,
            'to'           => $to,
            'report'       => $report,
            'generated_at' => now(),
            'mode'         => $format,
        ]);

        if ($format === 'word') {
            $html = view('admin.reports.document', $data)->render();
            $filename = 'cultural-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.doc';

            return response($html, 200, [
                'Content-Type'        => 'application/msword; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // PDF: تُعرض صفحة مهيّأة للطباعة تفتح حوار الطباعة (حفظ كـ PDF)
        return view('admin.reports.document', $data);
    }

    /**
     * تحديد نطاق الصلاحية (super = كل المراكز، admin = مركزه فقط)
     */
    private function scopeContext(): array
    {
        $admin    = Auth::guard('admin')->user();
        $isSuper  = $admin->isSuper();
        $centerId = $isSuper ? null : $admin->center_id;
        $center   = $centerId ? CulturalCenter::find($centerId) : null;

        return [
            'admin'     => $admin,
            'is_super'  => $isSuper,
            'center_id' => $centerId,
            'center'    => $center,
        ];
    }

    /**
     * قراءة نطاق التاريخ من الطلب (الافتراضي: آخر 30 يوماً)
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->input('from_date'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->input('to_date'))->endOfDay()
            : now()->endOfDay();

        // ضمان الترتيب الصحيح إذا عكس المستخدم التاريخين
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    /**
     * بناء بيانات التقرير مع تطبيق النطاق (الدور + المركز + المدة)
     */
    private function buildReport(Carbon $from, Carbon $to, bool $isSuper, ?int $centerId): array
    {
        // ---- الفعاليات (حسب وقت البداية ضمن المدة) ----
        $activityQuery = Activity::query()->whereBetween('start_time', [$from, $to]);
        if (! $isSuper) {
            $activityQuery->where('cultural_center_id', $centerId);
        }

        $activities = (clone $activityQuery)
            ->with(['culturalCenter', 'activityType', 'venue'])
            ->orderBy('start_time')
            ->get();

        $activitiesByType = $activities
            ->groupBy(fn ($a) => $a->activityType->title ?? 'غير مصنّف')
            ->map->count()
            ->sortDesc();

        $activitiesByCenter = $isSuper
            ? $activities->groupBy(fn ($a) => $a->culturalCenter->name ?? 'غير محدد')->map->count()->sortDesc()
            : collect();

        // ---- حجوزات القاعات (حسب تاريخ الطلب ضمن المدة) ----
        $venueResQuery = VenueReservation::query()->whereBetween('created_at', [$from, $to]);
        if (! $isSuper) {
            $venueResQuery->whereHas('venue', fn ($q) => $q->where('cultural_center_id', $centerId));
        }
        $venueResByStatus = (clone $venueResQuery)
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $venueResTotal = (int) $venueResByStatus->sum();

        // ---- حجوزات التذاكر (حسب تاريخ الطلب ضمن المدة) ----
        $ticketResQuery = Reservation::query()->whereBetween('created_at', [$from, $to]);
        if (! $isSuper) {
            $ticketResQuery->whereHas('activity', fn ($q) => $q->where('cultural_center_id', $centerId));
        }
        $ticketResByStatus = (clone $ticketResQuery)
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $ticketSeats = (clone $ticketResQuery)->sum('seats_count');
        $ticketResTotal = (int) $ticketResByStatus->sum();

        // ---- الكتب المضافة ضمن المدة (بالتفصيل) ----
        $bookQuery = Book::query()->whereBetween('created_at', [$from, $to]);
        if (! $isSuper) {
            $bookQuery->whereHas('library', fn ($q) => $q->where('cultural_center_id', $centerId));
        }
        $books = (clone $bookQuery)->with('library')->latest()->get();

        // ---- التفاصيل الكاملة لكل كيان ----
        $venueReservations  = (clone $venueResQuery)->with('venue.culturalCenter')->latest()->get();
        $ticketReservations = (clone $ticketResQuery)->with(['user', 'activity'])->latest()->get();

        // المكتبات ضمن النطاق (الحالة الحالية)
        $libraryQuery = Library::query()->withCount('books')->with('culturalCenter');
        if (! $isSuper) {
            $libraryQuery->where('cultural_center_id', $centerId);
        }
        $libraries = $libraryQuery->orderBy('cultural_center_id')->get();

        // المراكز ضمن النطاق (مع عدد القاعات والفعاليات)
        $centersQuery = CulturalCenter::query()->withCount(['venues', 'activities']);
        if (! $isSuper) {
            $centersQuery->where('id', $centerId ?? 0);
        }
        $centersDetail = $centersQuery->get();

        return [
            'summary' => [
                'activities'          => $activities->count(),
                'venue_reservations'  => $venueResTotal,
                'ticket_reservations' => $ticketResTotal,
                'ticket_seats'        => (int) $ticketSeats,
                'books_added'         => $books->count(),
                'libraries'           => $libraries->count(),
                'centers'             => $isSuper ? CulturalCenter::count() : ($centerId ? 1 : 0),
            ],
            'activities'           => $activities,
            'activities_by_type'   => $activitiesByType,
            'activities_by_center' => $activitiesByCenter,
            'venue_res_by_status'  => $venueResByStatus,
            'ticket_res_by_status' => $ticketResByStatus,
            'venue_reservations'   => $venueReservations,
            'ticket_reservations'  => $ticketReservations,
            'books'                => $books,
            'libraries'            => $libraries,
            'centers_detail'       => $centersDetail,
        ];
    }
}
