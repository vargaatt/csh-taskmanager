<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->realText(100),
            'estimated_time' => $this->faker->numberBetween(0, 60),
            'used_time' => $this->faker->numberBetween(0, 60),
            'created_date' => $this->faker->date(),
            'user_id' => User::first()->id
        ];
    }
}
