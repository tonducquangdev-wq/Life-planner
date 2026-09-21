<?php

namespace Database\Seeders;

use App\Models\LichHoc;
use App\Models\MonHoc;
use App\Models\User;
use Illuminate\Database\Seeder;

class LichHocSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'ho_ten' => 'Tôn Đức Quang',
            'email' => 'quang@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $monPhp = MonHoc::where('ma_mon', 'PHP101')->first();
        $monWeb = MonHoc::where('ma_mon', 'WEB201')->first();
        $monDb  = MonHoc::where('ma_mon', 'DB301')->first();

        // 1. Lịch học Thứ 2 (Lập trình PHP)
        LichHoc::create([
            'user_id' => $user->id,
            'mon_hoc_id' => $monPhp?->id ?? 1,
            'ngay_trong_tuan' => 1, // Thứ 2
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '10:30',
            'ghi_chu' => 'Phòng A101 • Thầy Nguyễn Văn A',
        ]);

        // 2. Lịch học Thứ 4 (Thiết kế Web)
        LichHoc::create([
            'user_id' => $user->id,
            'mon_hoc_id' => $monWeb?->id ?? 2,
            'ngay_trong_tuan' => 3, // Thứ 4
            'gio_bat_dau' => '13:00',
            'gio_ket_thuc' => '15:30',
            'ghi_chu' => 'Phòng B203 • Thầy Trần Văn B',
        ]);

        // 3. Lịch học Thứ 6 (Hệ quản trị cơ sở dữ liệu)
        LichHoc::create([
            'user_id' => $user->id,
            'mon_hoc_id' => $monDb?->id ?? 3,
            'ngay_trong_tuan' => 5, // Thứ 6
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '11:00',
            'ghi_chu' => 'Phòng C105 • Thầy Lê Văn C',
        ]);
    }
}
