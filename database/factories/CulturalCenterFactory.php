<?php

namespace Database\Factories;

use App\Models\CulturalCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CulturalCenterFactory extends Factory
{
    protected $model = CulturalCenter::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->city() . ' Cultural Center',
            'location'    => fake()->city(),
            'description' => fake()->paragraph(),
        ];
    }
}
