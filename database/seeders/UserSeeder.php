<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'quang@gmail.com'],
            [
                'ho_ten' => 'Tôn Đức Quang',
                'password' => Hash::make('12345678'),
                'anh_dai_dien' => null,
            ]
        );
    }
}