<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeHoachTapLuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutPlanApiController extends Controller
{
    /**
     * 1. GET /api/workout-plans : Lấy danh sách kế hoạch tập luyện của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $plans = $request->user()->keHoachTapLuyens()
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách kế hoạch tập luyện thành công!',
            'data'    => $plans,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/workout-plans : Tạo kế hoạch tập luyện mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_ke_hoach' => 'required|string|max:255',
            'mo_ta'        => 'nullable|string|max:1000',
            'is_active'    => 'nullable|boolean',
            'is_shared'    => 'nullable|boolean',
        ]);

        $userId = $request->user()->id;
        $isActive = $request->boolean('is_active', false);

        // Nếu đặt làm kế hoạch chính (is_active = true) hoặc đây là kế hoạch đầu tiên của user
        $hasPlans = KeHoachTapLuyen::where('user_id', $userId)->exists();
        if (! $hasPlans) {
            $isActive = true;
        }

        if ($isActive) {
            KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        }

        $validated['user_id'] = $userId;
        $validated['is_active'] = $isActive;
        $validated['is_shared'] = $request->boolean('is_shared', false);

        $plan = KeHoachTapLuyen::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Tạo kế hoạch tập luyện \"{$plan->ten_ke_hoach}\" thành công!",
            'data'    => $plan,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/workout-plans/{id} : Xem chi tiết 1 kế hoạch (Anti-IDOR)
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $plan = $request->user()->keHoachTapLuyens()
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->find($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch tập luyện hoặc bạn không có quyền truy cập.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết kế hoạch tập luyện.',
            'data'    => $plan,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/workout-plans/{id} : Cập nhật kế hoạch tập luyện (Anti-IDOR)
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $plan = $request->user()->keHoachTapLuyens()->find($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch tập luyện hoặc bạn không có quyền cập nhật.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'ten_ke_hoach' => 'sometimes|required|string|max:255',
            'mo_ta'        => 'nullable|string|max:1000',
            'is_active'    => 'nullable|boolean',
            'is_shared'    => 'nullable|boolean',
        ]);

        if (isset($validated['is_active']) && $validated['is_active']) {
            KeHoachTapLuyen::where('user_id', $request->user()->id)
                ->where('id', '!=', $plan->id)
                ->update(['is_active' => false]);
        }

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kế hoạch tập luyện thành công!',
            'data'    => $plan->fresh(['buoiTaps.chiTietBuoiTaps.baiTapTheChat']),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/workout-plans/{id} : Xóa kế hoạch tập luyện (Anti-IDOR)
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $plan = $request->user()->keHoachTapLuyens()->find($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kế hoạch tập luyện hoặc bạn không có quyền xóa.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $wasActive = $plan->is_active;
        $userId = $request->user()->id;

        $plan->delete();

        if ($wasActive) {
            $nextPlan = KeHoachTapLuyen::where('user_id', $userId)->first();
            if ($nextPlan) {
                $nextPlan->update(['is_active' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa kế hoạch tập luyện thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
