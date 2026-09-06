<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 week', '+1 week');
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->randomElement(['Họp nhóm đồ án', 'Học quân sự', 'Đi hội thảo', 'Kiểm tra giữa kỳ']),
            'description' => fake()->sentence(),
        'type' => fake()->randomElement(['Study', 'Personal', 'Work']),
        'start_time' => $start,
        'end_time' => (clone $start)->modify('+2 hours'),
        ];
    }

}
