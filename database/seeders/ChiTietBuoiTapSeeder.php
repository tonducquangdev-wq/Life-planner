<?php

namespace Database\Seeders;

use App\Models\ChiTietBuoiTap;
use Illuminate\Database\Seeder;

class ChiTietBuoiTapSeeder extends Seeder
{
    public function run(): void
    {
        // Push
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 1,
            'thu_tu' => 1
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 2,
            'thu_tu' => 2
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 3,
            'thu_tu' => 3
        ]);

        // Pull
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 2,
            'bai_tap_the_chat_id' => 4,
            'thu_tu' => 1
        ]);

        // Legs
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 3,
            'bai_tap_the_chat_id' => 5,
            'thu_tu' => 1
        ]);
    }
}