<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\LichSuTapLuyen;
use App\Models\MonHoc;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang tổng quan Dashboard với các số liệu thống kê
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Tổng số môn học của người dùng
        $tongMonHoc = MonHoc::where('user_id', $userId)->count();

        // 2. Tổng số bài tập chưa hoàn thành
        $baiTapChuaHoanThanh = BaiTap::whereHas('monHoc', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('trang_thai', '!=', 'da_hoan_thanh')
        ->count();

        // 3. Tổng số sự kiện hôm nay
        $suKienHomNay = SuKien::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->count();

        // 4. Tổng số buổi tập tuần này
        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        // Trả dữ liệu về view dashboard.blade.php
        return view('dashboard', compact(
            'tongMonHoc',
            'baiTapChuaHoanThanh',
            'suKienHomNay',
            'buoiTapTuanNay'
        ));
    }
}