<?php

namespace Database\Factories;

use App\Modules\Request\Models\Request;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'phone' => '+7 (999) ' . fake()->numerify('###-##-##'),
            'address' => fake()->address(),
            'problem_text' => fake()->sentence(10),
            'status' => Request::STATUS_NEW,
            'assigned_to' => null,
        ];
    }

    public function assigned(): static
    {
        return $this->state(function (array $attributes) {
            $master = User::where('role', User::ROLE_MASTER)->first() 
                ?? User::factory()->master()->create();
            
            return [
                'status' => Request::STATUS_ASSIGNED,
                'assigned_to' => $master->id,
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $master = User::where('role', User::ROLE_MASTER)->first() 
                ?? User::factory()->master()->create();
            
            return [
                'status' => Request::STATUS_IN_PROGRESS,
                'assigned_to' => $master->id,
            ];
        });
    }
}
