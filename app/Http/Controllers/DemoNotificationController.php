<?php

namespace App\Http\Controllers;

use App\Notifications\ReservationConfirmed;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DemoNotificationController extends Controller
{
    public function sendTest(Request $request)
    {
        $user = $request->user();

        $reservation = Reservation::with('activity')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (! $reservation) {
            $reservation = new Reservation([
                'id'        => 0,
                'ticket_id' => 'TKT-DEMO-0001',
                'status'    => Reservation::STATUS_CONFIRMED,
            ]);
            $reservation->setRelation('activity', (object) ['title' => 'نشاط تجريبي']);
        }

        $user->notify(new ReservationConfirmed($reservation));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال إشعار تجريبي',
        ]);
    }
}
