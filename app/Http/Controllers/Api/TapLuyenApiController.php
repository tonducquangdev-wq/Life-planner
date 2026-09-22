<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TapLuyenController;
use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TapLuyenApiController extends Controller
{
    /**
     * 1. GET /api/tap-luyen : Lấy danh sách kế hoạch tập luyện của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $plans = KeHoachTapLuyen::where('user_id', $userId)
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $plans,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/tap-luyen : Tạo kế hoạch tập luyện mới
     */
    public function store(Request $request): JsonResponse
    {
        return $this->storePlan($request);
    }

    /**
     * 3. GET /api/tap-luyen/{id} : Chi tiết kế hoạch tập luyện (Anti-IDOR)
     */
    public function show($id): JsonResponse
    {
        $plan = KeHoachTapLuyen::where('user_id', Auth::id())
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->find($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch tập luyện hoặc bạn không có quyền truy cập.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $plan,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/tap-luyen/{id} : Cập nhật kế hoạch tập luyện (Anti-IDOR)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $plan = KeHoachTapLuyen::where('user_id', Auth::id())->find($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch tập luyện hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'ten_ke_hoach' => 'sometimes|required|string|max:255',
            'mo_ta'        => 'nullable|string|max:1000',
            'is_active'    => 'nullable|boolean',
        ]);

        if (isset($validated['is_active']) && $validated['is_active']) {
            KeHoachTapLuyen::where('user_id', Auth::id())->update(['is_active' => false]);
        }

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kế hoạch tập luyện thành công!',
            'data'    => $plan,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/tap-luyen/{id} : Xóa kế hoạch tập luyện (Anti-IDOR)
     */
    public function destroy($id): JsonResponse
    {
        return $this->destroyPlan(request(), $id);
    }

    /**
     * Lấy lịch tập tuần động và trạng thái tập luyện hôm nay
     */
    public function schedule(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $activePlan = KeHoachTapLuyen::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->first();

        if (! $activePlan) {
            $activePlan = KeHoachTapLuyen::where('user_id', $userId)
                ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
                ->first();
            if ($activePlan) {
                $activePlan->update(['is_active' => true]);
            }
        }

        $controller = new TapLuyenController;
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
            'data'    => [
                'active_plan' => $activePlan ? [
                    'id'           => $activePlan->id,
                    'ten_ke_hoach' => $activePlan->ten_ke_hoach,
                    'mo_ta'        => $activePlan->mo_ta,
                    'so_buoi_tap'  => $activePlan->buoiTaps->count(),
                ] : null,
                'today_iso'        => $todayIso,
                'today_schedule'   => $dinhHuongHomNay,
                'weekly_schedule'  => array_values($lichTuan),
                'kpis'             => [
                    'buoi_tap_tuan_nay'    => $buoiTapTuanNay,
                    'thoi_gian_tap_hom_nay' => $thoiGianTapHomNay,
                    'tong_buoi_tap'        => $tongBuoiTap,
                ],
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Danh sách các Kế hoạch tập luyện
     */
    public function indexPlans(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * Tạo Kế hoạch tập luyện mới
     */
    public function storePlan(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'ten_ke_hoach' => 'required|string|max:255',
            'mo_ta'        => 'nullable|string|max:1000',
            'is_active'    => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active', true);
        if ($isActive) {
            KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        }

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $userId,
            'ten_ke_hoach' => $validated['ten_ke_hoach'],
            'mo_ta'        => $validated['mo_ta'] ?? null,
            'is_active'    => $isActive,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo kế hoạch \"{$plan->ten_ke_hoach}\"",
            'data'    => $plan,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Kích hoạt Kế hoạch tập luyện
     */
    public function activatePlan(Request $request, $id): JsonResponse
    {
        $userId = Auth::id();

        $plan = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại hoặc không thuộc sở hữu của bạn.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        $plan->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Đã kích hoạt kế hoạch \"{$plan->ten_ke_hoach}\"",
            'data'    => $plan,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Xóa Kế hoạch tập luyện
     */
    public function destroyPlan(Request $request, $id): JsonResponse
    {
        $userId = Auth::id();

        $plan = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch không tồn tại hoặc không thuộc sở hữu của bạn.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

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
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Danh sách Buổi tập của kế hoạch đang kích hoạt
     */
    public function indexSessions(Request $request): JsonResponse
    {
        $userId = Auth::id();
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
            'data'    => $sessions,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Thêm Buổi tập mới
     */
    public function storeSession(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'ke_hoach_tap_luyen_id' => 'required|integer',
            'ten_buoi_tap'          => 'required|string|max:255',
            'mo_ta'                 => 'nullable|string|max:1000',
            'ngay_trong_tuan'       => 'nullable|integer|between:1,7',
        ]);

        $plan = KeHoachTapLuyen::where('id', $validated['ke_hoach_tap_luyen_id'])
            ->where('user_id', $userId)
            ->first();

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch tập luyện không tồn tại hoặc không thuộc sở hữu của bạn.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $count = BuoiTap::where('ke_hoach_tap_luyen_id', $plan->id)->count();

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => $validated['ten_buoi_tap'],
            'mo_ta'                 => $validated['mo_ta'] ?? null,
            'thu_tu'                => $count + 1,
            'ngay_trong_tuan'       => $validated['ngay_trong_tuan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo buổi tập \"{$session->ten_buoi_tap}\"",
            'data'    => $session,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/tap-luyen/lich-su : Lịch sử tập luyện của Auth User
     */
    public function indexHistory(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $lichSu = LichSuTapLuyen::where('user_id', $userId)
            ->with(['buoiTap.keHoachTapLuyen'])
            ->orderBy('thoi_gian_bat_dau', 'desc')
            ->get();


        return response()->json([
            'success' => true,
            'data'    => $lichSu,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Cập nhật Buổi tập
     */
    public function updateSession(Request $request, $id): JsonResponse
    {
        $userId = Auth::id();

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Buổi tập không tồn tại hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'ten_buoi_tap'    => 'sometimes|required|string|max:255',
            'mo_ta'           => 'nullable|string|max:1000',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật buổi tập \"{$session->ten_buoi_tap}\"",
            'data'    => $session,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Xóa Buổi tập
     */
    public function destroySession(Request $request, $id): JsonResponse
    {
        $userId = Auth::id();

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Buổi tập không tồn tại hoặc bạn không có quyền xóa.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $name = $session->ten_buoi_tap;
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa buổi tập \"{$name}\"",
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Cập nhật danh sách bài tập của buổi tập
     */
    public function capNhatBuoiTap(Request $request): JsonResponse
    {
        $controller = new TapLuyenController;

        return $controller->capNhatBuoiTap($request);
    }

    /**
     * Ghi nhận hoàn thành buổi tập vào lịch sử
     */
    public function hoanThanh(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $request->validate([
            'buoi_tap_id' => [
                'nullable',
                'integer',
                Rule::exists('buoi_tap', 'id')->where(function ($query) use ($userId) {
                    $query->whereIn('ke_hoach_tap_luyen_id', function ($sub) use ($userId) {
                        $sub->select('id')->from('ke_hoach_tap_luyen')->where('user_id', $userId);
                    });
                }),
            ],
            'ten_buoi_tap' => 'nullable|string|max:255',
            'seconds' => 'nullable|integer|min:0',
            'tong_thoi_luong' => 'nullable|integer|min:0',
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $controller = new TapLuyenController;

        return $controller->hoanThanh($request);
    }

}
