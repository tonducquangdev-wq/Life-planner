<?php

namespace Database\Factories;

use App\Models\WorkoutSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutSession>
 */
class WorkoutSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_plan_id' => \App\Models\WorkoutPlan::factory(),
            'session_name' => fake()->randomElement(['Thứ 2 - Tập Ngực/Tay sau', 'Thứ 4 - Tập Chân/Mông', 'Thứ 6 - Tập Lưng/Xô']),
            'day_of_week' => fake()->randomElement(['Monday', 'Wednesday', 'Friday']),
        ];
    }

}
