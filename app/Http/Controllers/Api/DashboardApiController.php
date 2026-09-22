<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BuoiTap;
use App\Models\LichSuTapLuyen;
use App\Models\MonHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    /**
     * GET /api/dashboard
     * Trả về thông số tổng quan phục vụ báo cáo Dashboard
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        // 1. Tổng số môn học của User
        $tongMonHoc = MonHoc::where('user_id', $userId)->count();

        // 2. Tổng số bài tập thuộc môn học của User
        $tongBaiTap = BaiTap::whereHas('monHoc', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        // 3. Số bài tập deadline sắp tới (hạn nộp trong 7 ngày tới & chưa hoàn thành)
        $deadlineSapToi = BaiTap::whereHas('monHoc', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('trang_thai', '!=', 'da_hoan_thanh')
            ->whereBetween('han_nop', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();

        // 4. Tổng số buổi tập trong kế hoạch đang kích hoạt
        $tongBuoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('is_active', true);
        })->count();

        // 5. Số buổi tập đã hoàn thành trong lịch sử tập luyện của User
        $soBuoiDaHoanThanh = LichSuTapLuyen::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Lấy dữ liệu tổng quan thành công!',
            'data'    => [
                'tong_mon_hoc'          => $tongMonHoc,
                'tong_bai_tap'          => $tongBaiTap,
                'deadline_sap_toi'      => $deadlineSapToi,
                'tong_buoi_tap'         => $tongBuoiTap,
                'so_buoi_da_hoan_thanh' => $soBuoiDaHoanThanh,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
