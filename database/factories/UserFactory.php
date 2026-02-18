<?php

namespace Database\Factories;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => User::ROLE_DISPATCHER,
            'remember_token' => Str::random(10),
        ];
    }

    public function dispatcher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_DISPATCHER,
        ]);
    }

    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_MASTER,
        ]);
    }
}
