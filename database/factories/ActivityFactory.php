<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\CulturalCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+2 hours');

        return [
            'cultural_center_id' => CulturalCenter::factory(),
            'type'               => fake()->randomElement(Activity::TYPES),
            'title'              => fake()->sentence(3),
            'description'        => fake()->paragraph(),
            'start_time'         => $start,
            'end_time'           => $end,
            'capacity'           => fake()->numberBetween(10, 100),
        ];
    }
}
