<?php

namespace Database\Factories;

use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutPlan>
 */
class WorkoutPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'plan_name' => fake()->randomElement(['Tăng cơ 3 tháng', 'Giảm mỡ đón Tết', 'Sức bền chuyên sâu']),
            'description' => fake()->sentence(),
        ];
    }

}
