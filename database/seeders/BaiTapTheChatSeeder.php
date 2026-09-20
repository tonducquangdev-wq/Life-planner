<?php

namespace Database\Seeders;

use App\Models\BaiTapTheChat;
use Illuminate\Database\Seeder;

class BaiTapTheChatSeeder extends Seeder
{
    public function run(): void
    {
        BaiTapTheChat::create([
            'ten_bai_tap' => 'Bench Press',
            'nhom_co' => 'Ngực',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Phát triển ngực giữa'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Incline Dumbbell Press',
            'nhom_co' => 'Ngực',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Phát triển ngực trên'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Cable Fly',
            'nhom_co' => 'Ngực',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Cô lập cơ ngực'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Triceps Pushdown',
            'nhom_co' => 'Tay sau',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Phát triển cơ tay sau'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Pull Up',
            'nhom_co' => 'Lưng',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Xô rộng'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Squat',
            'nhom_co' => 'Chân',
            'loai_bai_tap' => 'strength',
            'mo_ta' => 'Phát triển đùi'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Core',
            'nhom_co' => 'Core',
            'loai_bai_tap' => 'core',
            'mo_ta' => 'Tập cơ bụng và thân giữa'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Đi bộ',
            'nhom_co' => 'Cardio',
            'loai_bai_tap' => 'cardio',
            'mo_ta' => 'Đi bộ dốc phục hồi và đốt mỡ'
        ]);
    }
}