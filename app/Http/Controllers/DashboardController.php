<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\LichSuTapLuyen;
use App\Models\MonHoc;
use App\Models\MucTieu;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang tổng quan Dashboard với giao diện thiết kế mới
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Tổng số môn học của người dùng
        $tongMonHoc = MonHoc::where('user_id', $userId)->count();
        if ($tongMonHoc === 0) {
            $tongMonHoc = 5; // Dữ liệu mẫu demo
        }

        // 2. Tổng số bài tập chưa hoàn thành
        $baiTapChuaHoanThanh = BaiTap::whereHas('monHoc', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('trang_thai', '!=', 'da_hoan_thanh')
        ->count();
        if ($baiTapChuaHoanThanh === 0) {
            $baiTapChuaHoanThanh = 3; // Dữ liệu mẫu demo
        }

        // 3. Tổng số sự kiện hôm nay
        $suKienHomNayCount = SuKien::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->count();
        $suKienHomNay = $suKienHomNayCount > 0 ? $suKienHomNayCount : 3;

        // 4. Tổng số buổi tập tuần này
        $buoiTapTuanNayCount = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();
        $buoiTapTuanNay = $buoiTapTuanNayCount > 0 ? $buoiTapTuanNayCount : 4;

        // Dữ liệu Hàng 2 (Trái): Timeline "Hôm nay"
        $homNayTimeline = [
            [
                'thoi_gian' => '08:00 - 10:30',
                'tieu_de' => 'Lập trình Web & Laravel 13',
                'mo_ta' => 'Phòng B2.04 • Thầy Nguyễn Văn A',
                'trang_thai' => 'da_hoan_thanh', // da_hoan_thanh | sap_dien_ra | qua_han
                'trang_thai_text' => 'Đã hoàn thành',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'dot_class' => 'dot-completed',
                'icon' => 'bi-laptop'
            ],
            [
                'thoi_gian' => '13:00 - 15:00',
                'tieu_de' => 'Họp nhóm Đồ án Life Planner',
                'mo_ta' => 'Google Meet • Thảo luận Module Calendar',
                'trang_thai' => 'sap_dien_ra',
                'trang_thai_text' => 'Sắp diễn ra',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'dot_class' => 'dot-upcoming',
                'icon' => 'bi-people-fill'
            ],
            [
                'thoi_gian' => '18:00 - 19:30',
                'tieu_de' => 'Tập Gym - Buổi tập Chân & Vai',
                'mo_ta' => 'CLB Fitness Center • Kế hoạch Tuần 3',
                'trang_thai' => 'sap_dien_ra',
                'trang_thai_text' => 'Sắp diễn ra',
                'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                'dot_class' => 'dot-upcoming',
                'icon' => 'bi-activity'
            ]
        ];

        // Dữ liệu Hàng 2 (Phải): Card "Sắp đến hạn"
        $sapDenHan = [
            [
                'tieu_de' => 'Đồ án PHP & Laravel 13',
                'mon_hoc' => 'Lập trình Web nâng cao',
                'con_lai' => '2 ngày',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'urgency' => 'high'
            ],
            [
                'tieu_de' => 'Báo cáo Bài tập CSDL MySQL',
                'mon_hoc' => 'Cơ sở dữ liệu',
                'con_lai' => '4 ngày',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'urgency' => 'medium'
            ],
            [
                'tieu_de' => 'Thi giữa kỳ Kiến trúc Phần mềm',
                'mon_hoc' => 'Kiến trúc phần mềm',
                'con_lai' => '7 ngày',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'urgency' => 'low'
            ]
        ];

        // Dữ liệu Hàng 3 (Trái): Card "Mục tiêu đang thực hiện"
        $mucTieuDangThucHien = [
            [
                'tieu_de' => 'Hoàn thành Đồ án PHP',
                'chi_tiet' => 'Mục tiêu GPA 3.8 / Kỳ này',
                'phan_tram' => 75,
                'bar_class' => 'bg-primary',
                'category' => 'Học tập'
            ],
            [
                'tieu_de' => 'Tăng cân lên 68kg',
                'chi_tiet' => 'Hiện tại: 65.5kg / 68kg',
                'phan_tram' => 60,
                'bar_class' => 'bg-success',
                'category' => 'Thể chất'
            ],
            [
                'tieu_de' => 'Học Tiếng Anh B1',
                'chi_tiet' => 'Đã học 50/100 bài bài giảng',
                'phan_tram' => 50,
                'bar_class' => 'bg-warning',
                'category' => 'Cá nhân'
            ]
        ];

        // Dữ liệu Hàng 3 (Phải): Card "Hoạt động gần đây"
        $hoatDongGanDay = [
            [
                'noi_dung' => 'Hoàn thành bài tập Laravel Controller',
                'thoi_gian' => '30 phút trước',
                'icon' => 'bi-check-circle-fill text-success',
                'badge' => 'Học tập'
            ],
            [
                'noi_dung' => 'Check-in buổi tập Leg Day',
                'thoi_gian' => '2 giờ trước',
                'icon' => 'bi-activity text-primary',
                'badge' => 'Thể chất'
            ],
            [
                'noi_dung' => 'Tạo sự kiện mới: Họp nhóm Đồ án',
                'thoi_gian' => 'Hôm qua, 15:30',
                'icon' => 'bi-calendar-plus-fill text-info',
                'badge' => 'Sự kiện'
            ],
            [
                'noi_dung' => 'Cập nhật tiến độ mục tiêu cân nặng',
                'thoi_gian' => '2 ngày trước',
                'icon' => 'bi-trophy-fill text-warning',
                'badge' => 'Mục tiêu'
            ]
        ];

        // Trả dữ liệu về view dashboard.blade.php
        return view('dashboard', compact(
            'tongMonHoc',
            'baiTapChuaHoanThanh',
            'suKienHomNay',
            'buoiTapTuanNay',
            'homNayTimeline',
            'sapDenHan',
            'mucTieuDangThucHien',
            'hoatDongGanDay'
        ));
    }
}