<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'phone'    => fake()->unique()->numerify('09########'),
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ];
    }

    public function super(): static
    {
        return $this->state(fn () => ['role' => 'super']);
    }

    public function ticketsAdmin(): static
    {
        return $this->state(fn () => ['role' => 'ticketsAdmin']);
    }
}
