<?php

namespace Database\Factories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
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
            'goal_name' => fake()->randomElement(['Đạt GPA 3.5', 'Giảm xuống 65kg', 'Chạy bộ 10km']),
            'target_value' => fake()->numberBetween(50, 100),
            'current_value' => fake()->numberBetween(10, 49),
            'status' => fake()->randomElement(['in_progress', 'achieved']),
        ];
    }

}
