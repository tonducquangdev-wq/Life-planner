<?php

namespace Database\Seeders;

use App\Models\GiaSu;
use App\Models\MonHoc;
use Illuminate\Database\Seeder;

class GiaSuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách ID các môn học sẵn có
        $monHocs = MonHoc::pluck('id', 'ten_mon')->toArray();

        // Helper tìm môn học id theo từ khóa
        $findMonId = function ($keyword) use ($monHocs) {
            foreach ($monHocs as $name => $id) {
                if (stripos($name, $keyword) !== false) {
                    return $id;
                }
            }
            return null;
        };

        $tutors = [
            [
                'ho_ten' => 'Nguyễn Minh Tuấn',
                'email' => 'tuan.nguyen.it@gmail.com',
                'so_dien_thoai' => '0912 345 678',
                'mon_hoc_id' => $findMonId('Web') ?? null,
                'chuyen_mon' => 'Lập trình Web & Laravel 13, Vue.js, REST API',
                'hoc_phi_theo_gio' => 180000,
                'danh_gia' => 4.9,
                'so_danh_gia' => 38,
                'mo_ta_kinh_nghiem' => 'Thủ khoa ngành CNTT ĐH Bách Khoa, 4 năm kinh nghiệm phát triển phần mềm và 2 năm làm trợ giảng môn Lập trình Web.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
            [
                'ho_ten' => 'Trần Thị Thu Hà',
                'email' => 'thuha.data@gmail.com',
                'so_dien_thoai' => '0988 765 432',
                'mon_hoc_id' => $findMonId('Cơ sở dữ liệu') ?? null,
                'chuyen_mon' => 'Cơ sở dữ liệu, SQL Tối ưu hóa, MySQL & PostgreSQL',
                'hoc_phi_theo_gio' => 160000,
                'danh_gia' => 4.8,
                'so_danh_gia' => 29,
                'mo_ta_kinh_nghiem' => 'Data Engineer tại tập đoàn công nghệ lớn, có kinh nghiệm kèm 1-1 các bài tập lớn CSDL và thiết kế mô hình quan hệ.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
            [
                'ho_ten' => 'Lê Hoàng Long',
                'email' => 'long.le.arch@gmail.com',
                'so_dien_thoai' => '0903 112 233',
                'mon_hoc_id' => $findMonId('Kiến trúc') ?? null,
                'chuyen_mon' => 'Kiến trúc Phần mềm, Microservices, Clean Architecture',
                'hoc_phi_theo_gio' => 220000,
                'danh_gia' => 5.0,
                'so_danh_gia' => 42,
                'mo_ta_kinh_nghiem' => 'Senior Solution Architect với 7 năm kinh nghiệm. Hướng dẫn sinh viên làm đồ án tốt nghiệp và kiến trúc hệ thống phân tán.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
            [
                'ho_ten' => 'Phạm Phương Thảo',
                'email' => 'thao.english.it@gmail.com',
                'so_dien_thoai' => '0977 456 789',
                'mon_hoc_id' => $findMonId('Tiếng Anh') ?? null,
                'chuyen_mon' => 'Tiếng Anh Chuyên ngành IT, Phỏng vấn xin việc FAANG',
                'hoc_phi_theo_gio' => 170000,
                'danh_gia' => 4.9,
                'so_danh_gia' => 55,
                'mo_ta_kinh_nghiem' => 'IELTS 8.0, chuyên gia dịch thuật tài liệu kỹ thuật và luyện nói phỏng vấn xin việc làm cho lập trình viên.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
            [
                'ho_ten' => 'Vũ Đức Thịnh',
                'email' => 'thinh.security@gmail.com',
                'so_dien_thoai' => '0938 998 877',
                'mon_hoc_id' => $findMonId('Bảo mật') ?? null,
                'chuyen_mon' => 'An toàn & Bảo mật Thông tin, Web Security OWASP',
                'hoc_phi_theo_gio' => 200000,
                'danh_gia' => 4.7,
                'so_danh_gia' => 21,
                'mo_ta_kinh_nghiem' => 'Chuyên gia bảo mật mạng (CEH, OSCP). Nhận kèm thực hành Pentest, bảo mật ứng dụng web và phân tích mã độc.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
            [
                'ho_ten' => 'Đỗ Mai Linh',
                'email' => 'mailinh.algorithm@gmail.com',
                'so_dien_thoai' => '0966 223 344',
                'mon_hoc_id' => null,
                'chuyen_mon' => 'Cấu trúc Dữ liệu & Giải thuật, Ôn thi LeetCode',
                'hoc_phi_theo_gio' => 150000,
                'danh_gia' => 4.9,
                'so_danh_gia' => 34,
                'mo_ta_kinh_nghiem' => 'Từng đạt giải Nhì Olympic Tin học sinh viên toàn quốc. Giảng dạy logic tư duy, thuật toán và giải đề thi nhanh.',
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
                'trang_thai' => 'san_sang',
            ],
        ];

        foreach ($tutors as $tutor) {
            GiaSu::updateOrCreate(
                ['email' => $tutor['email']],
                $tutor
            );
        }
    }
}
