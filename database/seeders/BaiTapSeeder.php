<?php

namespace Database\Seeders;

use App\Models\BaiTap;
use Illuminate\Database\Seeder;

class BaiTapSeeder extends Seeder
{
    public function run(): void
    {
        BaiTap::create([
            'mon_hoc_id' => 1,
            'tieu_de' => 'CRUD Laravel',
            'mo_ta' => 'Xây dựng chức năng CRUD môn học',
            'han_nop' => '2026-09-20 23:59:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        BaiTap::create([
            'mon_hoc_id' => 1,
            'tieu_de' => 'Authentication',
            'mo_ta' => 'Đăng nhập và đăng ký',
            'han_nop' => '2026-09-25 23:59:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'dang_thuc_hien',
        ]);

        BaiTap::create([
            'mon_hoc_id' => 2,
            'tieu_de' => 'Trang Dashboard',
            'mo_ta' => 'Thiết kế giao diện dashboard',
            'han_nop' => '2026-09-18 23:59:00',
            'muc_do_uu_tien' => 'trung_binh',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        BaiTap::create([
            'mon_hoc_id' => 2,
            'tieu_de' => 'Responsive Layout',
            'mo_ta' => 'Tối ưu giao diện mobile',
            'han_nop' => '2026-09-22 23:59:00',
            'muc_do_uu_tien' => 'trung_binh',
            'trang_thai' => 'da_hoan_thanh',
        ]);

        BaiTap::create([
            'mon_hoc_id' => 3,
            'tieu_de' => 'Thiết kế ERD',
            'mo_ta' => 'Phân tích và thiết kế cơ sở dữ liệu',
            'han_nop' => '2026-09-30 23:59:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'dang_thuc_hien',
        ]);
    }
}