<?php

namespace Database\Factories;

use App\Models\Volunteering;
use App\Models\User;
use App\Models\VolunteeringActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteeringFactory extends Factory
{
    protected $model = Volunteering::class;

    public function definition(): array
    {
        return [
            'user_id'                  => User::factory(),
            'volunteering_activity_id' => VolunteeringActivity::factory(),
            'form_data'                => ['experience' => fake()->sentence()],
            'status'                   => 'pending',
        ];
    }
}
