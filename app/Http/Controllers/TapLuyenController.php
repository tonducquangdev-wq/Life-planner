<?php

namespace App\Http\Controllers;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TapLuyenController extends Controller
{
    /**
     * Hiển thị trang theo dõi thể chất và tập luyện
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Thống kê số buổi tập trong tuần này của người dùng
        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        // Nếu chưa có lịch sử thực tế trong DB, mặc định hiển thị 4 buổi để giao diện trực quan
        if ($buoiTapTuanNay === 0) {
            $buoiTapTuanNay = 4;
        }

        // 2. Dữ liệu chỉ số sức khỏe mặc định / tổng hợp
        $chiSoSucKhoe = [
            'nhip_tim' => 72,
            'can_nang' => 68.5,
            'ty_le_mo' => 16.2,
            'luong_nuoc' => 2.1,
            'thoi_gian_tap_hom_nay' => 45,
            'giac_ngu' => '7h 15m',
            'chuoi_ngay' => 12,
        ];

        // 3. Danh sách hoạt động gần đây
        $hoatDongGanDay = [
            [
                'loai' => 'Push',
                'ten' => 'Push — Ngực, vai, tay sau',
                'thoi_gian' => '45 phút',
                'so_bai' => '6 bài tập',
                'thoi_diem' => 'Hôm nay',
                'icon' => 'bi-fire',
                'color' => 'danger',
            ],
            [
                'loai' => 'Pull',
                'ten' => 'Pull — Lưng xô, tay trước',
                'thoi_gian' => '50 phút',
                'so_bai' => '5 bài tập',
                'thoi_diem' => 'Hôm qua',
                'icon' => 'bi-lightning-charge-fill',
                'color' => 'primary',
            ],
            [
                'loai' => 'Legs',
                'ten' => 'Legs — Chân, mông, bắp chuối',
                'thoi_gian' => '60 phút',
                'so_bai' => '5 bài tập',
                'thoi_diem' => '3 ngày trước',
                'icon' => 'bi-activity',
                'color' => 'secondary',
            ],
        ];

        return view('tap_luyen.index', compact('buoiTapTuanNay', 'chiSoSucKhoe', 'hoatDongGanDay'));
    }
}
