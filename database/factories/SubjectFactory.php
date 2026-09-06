<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // Tự động gắn với 1 user tạo mới
            'subject_code' => fake()->unique()->bothify('???###'), // Ví dụ: INT102
            'subject_name' => fake()->randomElement(['Lập trình PHP', 'Cơ sở dữ liệu', 'Phát triển Web', 'Mạng máy tính']),
            'classroom' => 'Phòng ' . fake()->numberBetween(100, 500),
            'lecturer' => fake()->name(),
            'grade' => fake()->randomElement(['A', 'B+', 'B', 'C', 'D']),
        ];
    }

}
