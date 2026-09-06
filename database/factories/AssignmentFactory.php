<?php

namespace Database\Factories;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => \App\Models\Subject::factory(),
            'title' => 'Bài tập ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            'priority' => fake()->randomElement(['High', 'Medium', 'Low']),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
        ];
    }

}
