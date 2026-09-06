<?php

namespace Database\Factories;

use App\Models\WorkoutCheckin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutCheckin>
 */
class WorkoutCheckinFactory extends Factory
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
        'status' => fake()->randomElement(['completed', 'missed']),
    ];
}

}
