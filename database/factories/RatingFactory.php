<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use App\Models\CulturalCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'value'         => fake()->numberBetween(1, 5),
            'comment'       => fake()->optional()->sentence(),
            'rateable_type' => CulturalCenter::class,
            'rateable_id'   => CulturalCenter::factory(),
        ];
    }
}
