<?php

namespace Database\Factories;

use App\Models\VolunteeringActivity;
use App\Models\CulturalCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteeringActivityFactory extends Factory
{
    protected $model = VolunteeringActivity::class;

    public function definition(): array
    {
        return [
            'cultural_center_id' => CulturalCenter::factory(),
            'title'              => fake()->sentence(3),
            'description'        => fake()->paragraph(),
            'location'           => fake()->city(),
            'start_time'         => fake()->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
