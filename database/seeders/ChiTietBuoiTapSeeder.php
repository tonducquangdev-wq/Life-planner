<?php

namespace Database\Seeders;

use App\Models\ChiTietBuoiTap;
use Illuminate\Database\Seeder;

class ChiTietBuoiTapSeeder extends Seeder
{
    public function run(): void
    {
        // Push (Strength + Core + Cardio)
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 1, // Bench Press
            'thu_tu' => 1,
            'loai_bai_tap' => 'strength',
            'so_sets' => 4,
            'so_reps' => '8-10',
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 2, // Incline Dumbbell Press
            'thu_tu' => 2,
            'loai_bai_tap' => 'strength',
            'so_sets' => 3,
            'so_reps' => '8-12',
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 3, // Cable Fly
            'thu_tu' => 3,
            'loai_bai_tap' => 'strength',
            'so_sets' => 3,
            'so_reps' => '10-12',
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 4, // Triceps Pushdown
            'thu_tu' => 4,
            'loai_bai_tap' => 'strength',
            'so_sets' => 3,
            'so_reps' => '10-12',
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 7, // Core
            'thu_tu' => 5,
            'loai_bai_tap' => 'core',
            'so_sets' => 3,
            'so_reps' => '10-12',
        ]);

        ChiTietBuoiTap::create([
            'buoi_tap_id' => 1,
            'bai_tap_the_chat_id' => 8, // Đi bộ
            'thu_tu' => 6,
            'loai_bai_tap' => 'cardio',
            'thoi_luong' => 30,
            'don_vi_thoi_gian' => 'phut',
        ]);

        // Pull
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 2,
            'bai_tap_the_chat_id' => 5, // Pull Up
            'thu_tu' => 1,
            'loai_bai_tap' => 'strength',
            'so_sets' => 4,
            'so_reps' => '8-12',
        ]);

        // Legs
        ChiTietBuoiTap::create([
            'buoi_tap_id' => 3,
            'bai_tap_the_chat_id' => 6, // Squat
            'thu_tu' => 1,
            'loai_bai_tap' => 'strength',
            'so_sets' => 4,
            'so_reps' => '8-10',
        ]);
    }
}