<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'          => fake()->name(),
            'phone'         => fake()->unique()->numerify('09########'),
            'date_of_birth' => fake()->date(),
            'gender'        => fake()->randomElement(['male', 'female']),
            'remember_token'=> Str::random(10),
        ];
    }
}