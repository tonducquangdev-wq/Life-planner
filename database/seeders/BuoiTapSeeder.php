<?php

namespace Database\Seeders;

use App\Models\BuoiTap;
use Illuminate\Database\Seeder;

class BuoiTapSeeder extends Seeder
{
    public function run(): void
    {
        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => 1,
            'ten_buoi_tap' => 'Push',
            'mo_ta' => 'Ngực Vai Tay Sau',
            'thu_tu' => 1
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => 1,
            'ten_buoi_tap' => 'Pull',
            'mo_ta' => 'Lưng Tay Trước',
            'thu_tu' => 2
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => 1,
            'ten_buoi_tap' => 'Legs',
            'mo_ta' => 'Chân',
            'thu_tu' => 3
        ]);
    }
}