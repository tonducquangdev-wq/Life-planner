<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorldChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorldChatMessage>
 */
class WorldChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'noi_dung' => fake()->sentence(),
        ];
    }
}
