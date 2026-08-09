<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $ticketId = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));

        return [
            'user_id'          => User::factory(),
            'ticket_id'        => $ticketId,
            'qr_code'          => json_encode(['ticket_id' => $ticketId]),
            'activity_id'      => Activity::factory(),
            'reservation_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status'           => Reservation::STATUS_CONFIRMED,
        ];
    }
}
