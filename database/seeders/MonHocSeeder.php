<?php

namespace Database\Seeders;

use App\Models\MonHoc;
use Illuminate\Database\Seeder;

class MonHocSeeder extends Seeder
{
    public function run(): void
    {
        MonHoc::create([
            'user_id' => 1,
            'ma_mon' => 'PHP101',
            'ten_mon' => 'Lập trình PHP',
            'giang_vien' => 'Nguyễn Văn A',
            'phong_hoc' => 'A101',
            'so_tin_chi' => 3,
            'tien_do' => 40,
            'diem_so' => 8.5,
        ]);

        MonHoc::create([
            'user_id' => 1,
            'ma_mon' => 'WEB201',
            'ten_mon' => 'Thiết kế Web',
            'giang_vien' => 'Trần Văn B',
            'phong_hoc' => 'B203',
            'so_tin_chi' => 3,
            'tien_do' => 60,
            'diem_so' => 7.8,
        ]);

        MonHoc::create([
            'user_id' => 1,
            'ma_mon' => 'DB301',
            'ten_mon' => 'Hệ quản trị cơ sở dữ liệu',
            'giang_vien' => 'Lê Văn C',
            'phong_hoc' => 'C105',
            'so_tin_chi' => 4,
            'tien_do' => 25,
            'diem_so' => null,
        ]);
    }
}