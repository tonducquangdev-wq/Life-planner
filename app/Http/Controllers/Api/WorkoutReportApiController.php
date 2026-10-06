<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WorkoutReportApiController extends Controller
{
    /**
     * GET /api/workout-report
     * Lấy báo cáo thống kê tập luyện của Auth User.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // 1. Tổng số kế hoạch tập luyện
        $tongSoKeHoach = KeHoachTapLuyen::where('user_id', $userId)->count();

        // 2. Tổng số buổi tập trong các kế hoạch
        $tongSoBuoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        // 3. Tổng số lượt hoàn thành buổi tập (Tích lũy)
        $buoiTapHoanThanh = LichSuTapLuyen::where('user_id', $userId)->count();

        // 4. Số buổi tập hoàn thành tuần này
        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        // 5. Tổng thời gian tập hôm nay (phút)
        $thoiGianTapHomNay = (int) LichSuTapLuyen::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->sum('tong_thoi_luong');

        // 6. Tổng thời gian tập tích lũy (phút)
        $tongThoiGianTap = (int) LichSuTapLuyen::where('user_id', $userId)
            ->sum('tong_thoi_luong');

        // 7. Chuỗi ngày tập rèn luyện liên tục (Streak)
        $dates = LichSuTapLuyen::where('user_id', $userId)
            ->orderByDesc('thoi_gian_bat_dau')
            ->pluck('thoi_gian_bat_dau')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $chuoiNgayTap = 0;
        if ($dates->isNotEmpty()) {
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
            $firstDate = $dates->first();

            if ($firstDate === $today || $firstDate === $yesterday) {
                $currentCheck = Carbon::parse($firstDate);
                foreach ($dates as $dateStr) {
                    if ($dateStr === $currentCheck->toDateString()) {
                        $chuoiNgayTap++;
                        $currentCheck->subDay();
                    } else {
                        break;
                    }
                }
            }
        }

        // 8. Kế hoạch hiện tại & Tính tiến độ tập luyện (%)
        $activePlan = KeHoachTapLuyen::where('user_id', $userId)
            ->where('is_active', true)
            ->withCount('buoiTaps')
            ->first();

        if (! $activePlan) {
            $activePlan = KeHoachTapLuyen::where('user_id', $userId)
                ->withCount('buoiTaps')
                ->first();
        }

        $tienDoTapLuyen = 0;
        if ($activePlan && $activePlan->buoi_taps_count > 0) {
            $tienDoTapLuyen = (int) round(min(100, ($buoiTapTuanNay / $activePlan->buoi_taps_count) * 100));
        } elseif ($tongSoBuoiTap > 0) {
            $tienDoTapLuyen = (int) round(min(100, ($buoiTapHoanThanh / $tongSoBuoiTap) * 100));
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo tập luyện thành công!',
            'data'    => [
                'tong_so_ke_hoach'     => $tongSoKeHoach,
                'tong_so_buoi_tap'     => $tongSoBuoiTap,
                'buoi_tap_hoan_thanh'  => $buoiTapHoanThanh,
                'buoi_tap_tuan_nay'    => $buoiTapTuanNay,
                'thoi_gian_tap_hom_nay' => $thoiGianTapHomNay,
                'tong_thoi_gian_tap'   => $tongThoiGianTap,
                'chuoi_ngay_tap'       => $chuoiNgayTap,
                'tien_do_tap_luyen'    => $tienDoTapLuyen,
                'ke_hoach_hien_tai'    => $activePlan ? [
                    'id'           => $activePlan->id,
                    'ten_ke_hoach' => $activePlan->ten_ke_hoach,
                    'mo_ta'        => $activePlan->mo_ta,
                    'so_buoi_tap'  => $activePlan->buoi_taps_count,
                    'is_active'    => (bool) $activePlan->is_active,
                ] : null,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
