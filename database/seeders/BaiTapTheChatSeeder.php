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
            'mo_ta' => 'Phát triển ngực giữa'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Incline Dumbbell Press',
            'nhom_co' => 'Ngực',
            'mo_ta' => 'Phát triển ngực trên'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Cable Fly',
            'nhom_co' => 'Ngực',
            'mo_ta' => 'Cô lập cơ ngực'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Pull Up',
            'nhom_co' => 'Lưng',
            'mo_ta' => 'Xô rộng'
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap' => 'Squat',
            'nhom_co' => 'Chân',
            'mo_ta' => 'Phát triển đùi'
        ]);
    }
}