<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Hiển thị màn hình Lịch (Calendar First)
     */
    public function index()
    {
        // 1. Thông tin tháng hiện tại
        $currentMonthYear = "Tháng 9, 2026";

        // 2. Dữ liệu mẫu các sự kiện hiển thị trên Lưới Lịch (Calendar Grid 35 ô đại diện Tháng 9/2026)
        // Tháng 9/2026 bắt đầu từ Thứ 3 (ngày 1/9). Ô 0 (Chủ nhật) là ngày 31/08.
        $calendarDays = [];

        // Hàng 1 (Ngày 31/8 -> 6/9)
        $calendarDays[] = ['day' => 31, 'is_current_month' => false, 'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 1,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
        ]];
        $calendarDays[] = ['day' => 2,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => 'Nghỉ lễ Quốc Khánh', 'type' => 'ca-nhan', 'time' => 'Cả ngày'],
        ]];
        $calendarDays[] = ['day' => 3,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '14:00 Thảo luận CSDL', 'type' => 'hoc-tap', 'time' => '14:00'],
            ['title' => '18:00 Tập Gym (Legs)', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 4,  'is_current_month' => true,  'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 5,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '09:00 Chạy bộ 5km', 'type' => 'tap-luyen', 'time' => '09:00'],
        ]];
        $calendarDays[] = ['day' => 6,  'is_current_month' => true,  'is_today' => false, 'events' => []];

        // Hàng 2 (Ngày 7/9 -> 13/9)
        $calendarDays[] = ['day' => 7,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
        ]];
        $calendarDays[] = ['day' => 8,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '18:00 Tập Gym (Chest)', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 9,  'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '23:59 Nộp Bài tập 1', 'type' => 'deadline', 'time' => '23:59'],
        ]];
        $calendarDays[] = ['day' => 10, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '13:30 Kiểm thử phần mềm', 'type' => 'hoc-tap', 'time' => '13:30'],
        ]];
        $calendarDays[] = ['day' => 11, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '18:00 Tập Gym (Back)', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 12, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '19:00 Sinh hoạt CLB', 'type' => 'ca-nhan', 'time' => '19:00'],
        ]];
        $calendarDays[] = ['day' => 13, 'is_current_month' => true,  'is_today' => false, 'events' => []];

        // Hàng 3 (Ngày 14/9 -> 20/9) - Ngày 14 là HÔM NAY (is_today = true)
        $calendarDays[] = ['day' => 14, 'is_current_month' => true,  'is_today' => true,  'events' => [
            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
            ['title' => '14:00 Họp nhóm Đồ án', 'type' => 'ca-nhan', 'time' => '14:00'],
            ['title' => '18:00 Tập Gym (Legs)', 'type' => 'tap-luyen', 'time' => '18:00'],
            ['title' => '23:59 Nộp Báo cáo Lab', 'type' => 'deadline', 'time' => '23:59'],
            ['title' => '21:00 Đọc sách Clean Code', 'type' => 'ca-nhan', 'time' => '21:00'],
        ]]; // Ô này có 5 sự kiện -> hiển thị 3 sự kiện + "+2 khác"
        $calendarDays[] = ['day' => 15, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '10:00 Học Tiếng Anh', 'type' => 'hoc-tap', 'time' => '10:00'],
        ]];
        $calendarDays[] = ['day' => 16, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '23:59 Nộp đồ án PHP', 'type' => 'deadline', 'time' => '23:59'],
            ['title' => '18:00 Tập Cardio', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 17, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '08:00 CSDL Nâng cao', 'type' => 'hoc-tap', 'time' => '08:00'],
        ]];
        $calendarDays[] = ['day' => 18, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '23:59 Báo cáo CSDL', 'type' => 'deadline', 'time' => '23:59'],
        ]];
        $calendarDays[] = ['day' => 19, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '17:00 Đá bóng giao hữu', 'type' => 'tap-luyen', 'time' => '17:00'],
        ]];
        $calendarDays[] = ['day' => 20, 'is_current_month' => true,  'is_today' => false, 'events' => []];

        // Hàng 4 (Ngày 21/9 -> 27/9)
        $calendarDays[] = ['day' => 21, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '08:00 Thi giữa kỳ KTPM', 'type' => 'deadline', 'time' => '08:00'],
        ]];
        $calendarDays[] = ['day' => 22, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '18:00 Tập Gym (FullBody)', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 23, 'is_current_month' => true,  'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 24, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '14:00 Workshop AI', 'type' => 'hoc-tap', 'time' => '14:00'],
        ]];
        $calendarDays[] = ['day' => 25, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '18:00 Tập Yoga', 'type' => 'tap-luyen', 'time' => '18:00'],
        ]];
        $calendarDays[] = ['day' => 26, 'is_current_month' => true,  'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 27, 'is_current_month' => true,  'is_today' => false, 'events' => []];

        // Hàng 5 (Ngày 28/9 -> 4/10)
        $calendarDays[] = ['day' => 28, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => '08:00 Thuyết trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
        ]];
        $calendarDays[] = ['day' => 29, 'is_current_month' => true,  'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 30, 'is_current_month' => true,  'is_today' => false, 'events' => [
            ['title' => 'Tổng kết Tháng 9', 'type' => 'ca-nhan', 'time' => '20:00'],
        ]];
        $calendarDays[] = ['day' => 1,  'is_current_month' => false, 'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 2,  'is_current_month' => false, 'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 3,  'is_current_month' => false, 'is_today' => false, 'events' => []];
        $calendarDays[] = ['day' => 4,  'is_current_month' => false, 'is_today' => false, 'events' => []];

        // 3. Dữ liệu Card bên phải (Column Right 25%)
        // Card 1: Lịch trình hôm nay
        $lichTrinhHomNay = [
            [
                'thoi_gian' => '08:00 - 10:30',
                'tieu_de' => 'Lập trình Web & Laravel 13',
                'dia_diem' => 'Phòng B2.04',
                'badge' => 'Học tập',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle'
            ],
            [
                'thoi_gian' => '14:00 - 15:30',
                'tieu_de' => 'Họp nhóm Đồ án Life Planner',
                'dia_diem' => 'Google Meet',
                'badge' => 'Cá nhân',
                'badge_class' => 'bg-purple-subtle text-purple border border-purple-subtle'
            ],
            [
                'thoi_gian' => '18:00 - 19:30',
                'tieu_de' => 'Tập Gym - Buổi tập Chân & Vai',
                'dia_diem' => 'Fitness Center',
                'badge' => 'Tập luyện',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle'
            ]
        ];

        // Card 2: Sắp đến hạn
        $sapDenHan = [
            [
                'tieu_de' => 'Nộp Đồ án PHP & Laravel 13',
                'mon_hoc' => 'Lập trình Web nâng cao',
                'con_lai' => 'Còn 2 ngày',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle'
            ],
            [
                'tieu_de' => 'Báo cáo CSDL MySQL',
                'mon_hoc' => 'Cơ sở dữ liệu',
                'con_lai' => 'Còn 4 ngày',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle'
            ],
            [
                'tieu_de' => 'Thi giữa kỳ Kiến trúc Phần mềm',
                'mon_hoc' => 'Kiến trúc PM',
                'con_lai' => 'Còn 7 ngày',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle'
            ]
        ];

        // Card 3: Thống kê nhanh
        $thongKeNhanh = [
            'mon_hoc_tuan_nay' => 5,
            'buoi_tap_tuan_nay' => 4,
            'deadline_chua_nop' => 3
        ];

        return view('calendar', compact(
            'currentMonthYear',
            'calendarDays',
            'lichTrinhHomNay',
            'sapDenHan',
            'thongKeNhanh'
        ));
    }
}
