<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Activity;
use App\Models\Reservation;
use App\Models\Suggestion;
use App\Models\Volunteering;
use App\Models\VolunteeringActivity;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
