<?php

namespace Database\Seeders;

use App\Models\KeHoachTapLuyen;
use Illuminate\Database\Seeder;

class KeHoachTapLuyenSeeder extends Seeder
{
    public function run(): void
    {
        KeHoachTapLuyen::create([
            'user_id' => 1,
            'ten_ke_hoach' => 'Push Pull Legs',
            'mo_ta' => 'Kế hoạch tăng cơ 3 buổi'
        ]);
    }
}