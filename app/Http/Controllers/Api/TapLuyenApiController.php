<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TapLuyenController;
use App\Models\BaiTapTheChat;
use App\Models\BuoiTap;
use App\Models\ChiTietBuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TapLuyenApiController extends Controller
{
    /**
     * Lấy lịch tập tuần động và trạng thái tập luyện hôm nay
     */
    public function schedule(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $activePlan = KeHoachTapLuyen::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->first();

        if (!$activePlan) {
            $activePlan = KeHoachTapLuyen::where('user_id', $userId)
                ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
                ->first();
            if ($activePlan) {
                $activePlan->update(['is_active' => true]);
            }
        }

        $controller = new TapLuyenController();
        $lichTuan = $controller->buildLichTuan($activePlan);
        $todayIso = Carbon::now()->dayOfWeekIso; // 1 = T2 ... 7 = CN
        $dinhHuongHomNay = $lichTuan[$todayIso] ?? $lichTuan[1];

        // KPI stats
        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $thoiGianTapHomNay = (int) LichSuTapLuyen::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->sum('tong_thoi_luong');

        $tongBuoiTap = LichSuTapLuyen::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'active_plan' => $activePlan ? [
                    'id' => $activePlan->id,
                    'ten_ke_hoach' => $activePlan->ten_ke_hoach,
                    'mo_ta' => $activePlan->mo_ta,
                    'so_buoi_tap' => $activePlan->buoiTaps->count(),
                ] : null,
                'today_iso' => $todayIso,
                'today_schedule' => $dinhHuongHomNay,
                'weekly_schedule' => array_values($lichTuan),
                'kpis' => [
                    'buoi_tap_tuan_nay' => $buoiTapTuanNay,
                    'thoi_gian_tap_hom_nay' => $thoiGianTapHomNay,
                    'tong_buoi_tap' => $tongBuoiTap,
                ],
            ],
        ]);
    }

    /**
     * Danh sách các Kế hoạch tập luyện
     */
    public function indexPlans(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $plans = KeHoachTapLuyen::where('user_id', $userId)
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Tạo Kế hoạch tập luyện mới
     */
    public function storePlan(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'ten_ke_hoach' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active', true);
        if ($isActive) {
            KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        }

        $plan = KeHoachTapLuyen::create([
            'user_id' => $userId,
            'ten_ke_hoach' => $validated['ten_ke_hoach'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'is_active' => $isActive,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo kế hoạch \"{$plan->ten_ke_hoach}\"",
            'data' => $plan,
        ], 201);
    }

    /**
     * Kích hoạt Kế hoạch tập luyện
     */
    public function activatePlan(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $plan = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        $plan->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Đã kích hoạt kế hoạch \"{$plan->ten_ke_hoach}\"",
            'data' => $plan,
        ]);
    }

    /**
     * Xóa Kế hoạch tập luyện
     */
    public function destroyPlan(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $plan = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $wasActive = $plan->is_active;
        $plan->delete();

        if ($wasActive) {
            $nextPlan = KeHoachTapLuyen::where('user_id', $userId)->first();
            if ($nextPlan) {
                $nextPlan->update(['is_active' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa kế hoạch thành công',
        ]);
    }

    /**
     * Danh sách Buổi tập của kế hoạch đang kích hoạt
     */
    public function indexSessions(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $planId = $request->query('plan_id');

        $query = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['chiTietBuoiTaps.baiTapTheChat']);

        if ($planId) {
            $query->where('ke_hoach_tap_luyen_id', $planId);
        } else {
            $query->whereHas('keHoachTapLuyen', function ($q) {
                $q->where('is_active', true);
            });
        }

        $sessions = $query->orderBy('thu_tu')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    /**
     * Thêm Buổi tập mới
     */
    public function storeSession(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'ke_hoach_tap_luyen_id' => 'required|integer',
            'ten_buoi_tap' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
        ]);

        $plan = KeHoachTapLuyen::where('id', $validated['ke_hoach_tap_luyen_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        $count = BuoiTap::where('ke_hoach_tap_luyen_id', $plan->id)->count();

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => $validated['ten_buoi_tap'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'thu_tu' => $count + 1,
            'ngay_trong_tuan' => $validated['ngay_trong_tuan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo buổi tập \"{$session->ten_buoi_tap}\"",
            'data' => $session,
        ], 201);
    }

    /**
     * Cập nhật Buổi tập
     */
    public function updateSession(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'ten_buoi_tap' => 'sometimes|required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật buổi tập \"{$session->ten_buoi_tap}\"",
            'data' => $session,
        ]);
    }

    /**
     * Xóa Buổi tập
     */
    public function destroySession(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->firstOrFail();

        $name = $session->ten_buoi_tap;
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa buổi tập \"{$name}\"",
        ]);
    }

    /**
     * Cập nhật danh sách bài tập của buổi tập
     */
    public function capNhatBuoiTap(Request $request): JsonResponse
    {
        $controller = new TapLuyenController();
        return $controller->capNhatBuoiTap($request);
    }

    /**
     * Ghi nhận hoàn thành buổi tập vào lịch sử
     */
    public function hoanThanh(Request $request): JsonResponse
    {
        $controller = new TapLuyenController();
        return $controller->hoanThanh($request);
    }
}
