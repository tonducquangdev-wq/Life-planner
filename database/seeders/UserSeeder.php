<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'ho_ten' => 'Tôn Đức Quang',
            'email' => 'quang@gmail.com',
            'password' => Hash::make('12345678'),
            'anh_dai_dien' => null,
        ]);
    }
}