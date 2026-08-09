<?php

namespace Database\Factories;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuggestionFactory extends Factory
{
    protected $model = Suggestion::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type'    => fake()->randomElement(Suggestion::TYPES),
            'content' => fake()->paragraph(),
        ];
    }
}
