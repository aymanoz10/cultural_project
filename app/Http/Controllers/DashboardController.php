<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Activity;
use App\Models\Book;
use App\Models\CulturalCenter;
use App\Models\Reservation;
use App\Models\Suggestion;
use App\Models\VenueReservation;
use App\Models\Volunteering;
use App\Models\VolunteeringActivity;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * لوحة التحكم الرئيسية (واجهة Blade مع بيانات حقيقية بالكامل)
     */
    public function web()
    {
        $now = now();

        $stats = [
            'total'        => Activity::count(),
            'finished'     => Activity::where('end_time', '<', $now)->count(),
            'live'         => Activity::where('start_time', '<=', $now)->where('end_time', '>=', $now)->count(),
            'upcoming'     => Activity::where('start_time', '>', $now)->count(),
            'pendingHalls' => VenueReservation::where('status', 'pending')->count(),
            'total_books'  => Book::count(),
        ];

        // نسبة الفعاليات المكتملة (بديل حقيقي لمؤشر مسح التذاكر الوهمي)
        $completion_rate = $stats['total'] > 0
            ? (int) round($stats['finished'] / $stats['total'] * 100)
            : 0;

        // توزيع الفعاليات حسب المركز الثقافي (بديل حقيقي لمخطط الإشغال الوهمي)
        $activities_by_center = CulturalCenter::withCount('activities')
            ->orderByDesc('activities_count')
            ->take(5)
            ->get()
            ->map(fn ($c) => ['label' => $c->name, 'value' => (int) $c->activities_count])
            ->values();

        // الفعالية الجارية الآن، أو أقرب فعالية قادمة (بديل حقيقي لبطاقة "البث المباشر" الوهمية)
        $liveActivity = Activity::with(['culturalCenter', 'venue'])
            ->where('start_time', '<=', $now)->where('end_time', '>=', $now)
            ->orderBy('end_time')
            ->first();

        $isLive = (bool) $liveActivity;

        if (! $liveActivity) {
            $liveActivity = Activity::with(['culturalCenter', 'venue'])
                ->where('start_time', '>', $now)
                ->orderBy('start_time')
                ->first();
        }

        $live_event = null;
        if ($liveActivity) {
            $target = $isLive ? $liveActivity->end_time : $liveActivity->start_time;
            $live_event = [
                'id'      => $liveActivity->id,
                'is_live' => $isLive,
                'title'   => $liveActivity->title,
                'center'  => $liveActivity->culturalCenter->name ?? 'غير محدد',
                'venue'   => $liveActivity->venue->name ?? 'غير محدد',
                'seconds' => max(0, $target->timestamp - $now->timestamp),
            ];
        }

        // أحدث طلبات حجز القاعات (بديل حقيقي لجدول "آخر الحجوزات" الوهمي)
        $recent_reservations = VenueReservation::with('venue')
            ->latest()
            ->take(5)
            ->get();

        $recent_books = Book::with('library')->latest()->take(5)->get();

        $books_by_category = Book::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.dashboard', compact(
            'stats',
            'completion_rate',
            'activities_by_center',
            'live_event',
            'recent_reservations',
            'recent_books',
            'books_by_category'
        ));
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'users_count'                  => User::count(),
                'admins_count'                 => Admin::count(),
                'activities_count'             => Activity::count(),
                'volunteering_activities_count'=> VolunteeringActivity::count(),
                'volunteerings_count'          => Volunteering::count(),
                'reservations_count'           => Reservation::count(),
                'confirmed_reservations'       => Reservation::where('status', 'confirmed')->count(),
                'wait_list_reservations'       => Reservation::where('status', 'wait_list')->count(),
                'suggestions_by_type'          => Suggestion::select('type', DB::raw('count(*) as total'))
                    ->groupBy('type')
                    ->pluck('total', 'type'),
                'average_rating'               => round((float) Rating::avg('value'), 2),
                'pending_volunteerings'        => Volunteering::where('status', 'pending')->count(),
            ],
        ]);
    }
}
