<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutSessionApiController extends Controller
{
    /**
     * 1. GET /api/workout-sessions : Lấy danh sách buổi tập của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $query = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['keHoachTapLuyen', 'chiTietBuoiTaps.baiTapTheChat']);

        if ($request->has('plan_id')) {
            $query->where('ke_hoach_tap_luyen_id', $request->input('plan_id'));
        }

        $sessions = $query->orderBy('thu_tu')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách buổi tập thành công!',
            'data'    => $sessions,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/workout-sessions : Tạo buổi tập mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ke_hoach_tap_luyen_id' => 'required|integer',
            'ten_buoi_tap'          => 'required|string|max:255',
            'mo_ta'                 => 'nullable|string|max:1000',
            'thu_tu'                => 'nullable|integer|min:1',
            'ngay_trong_tuan'       => 'nullable|integer|between:1,7',
        ]);

        $userId = $request->user()->id;

        $plan = KeHoachTapLuyen::where('id', $validated['ke_hoach_tap_luyen_id'])
            ->where('user_id', $userId)
            ->first();

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => 'Kế hoạch tập luyện không tồn tại hoặc không thuộc sở hữu của bạn.',
            ], 422, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $count = BuoiTap::where('ke_hoach_tap_luyen_id', $plan->id)->count();

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => $validated['ten_buoi_tap'],
            'mo_ta'                 => $validated['mo_ta'] ?? null,
            'thu_tu'                => $validated['thu_tu'] ?? ($count + 1),
            'ngay_trong_tuan'       => $validated['ngay_trong_tuan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo buổi tập thành công!',
            'data'    => $session->fresh(['keHoachTapLuyen', 'chiTietBuoiTaps.baiTapTheChat']),
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/workout-sessions/{id} : Chi tiết buổi tập (Anti-IDOR)
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $userId = $request->user()->id;

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['keHoachTapLuyen', 'chiTietBuoiTaps.baiTapTheChat'])->find($id);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy buổi tập hoặc bạn không có quyền truy cập.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết buổi tập.',
            'data'    => $session,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/workout-sessions/{id} : Cập nhật buổi tập (Anti-IDOR)
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $userId = $request->user()->id;

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->find($id);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy buổi tập hoặc bạn không có quyền cập nhật.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'ten_buoi_tap'          => 'sometimes|required|string|max:255',
            'mo_ta'                 => 'nullable|string|max:1000',
            'thu_tu'                => 'nullable|integer|min:1',
            'ngay_trong_tuan'       => 'nullable|integer|between:1,7',
            'ke_hoach_tap_luyen_id' => 'nullable|integer',
        ]);

        if (isset($validated['ke_hoach_tap_luyen_id'])) {
            $plan = KeHoachTapLuyen::where('id', $validated['ke_hoach_tap_luyen_id'])
                ->where('user_id', $userId)
                ->first();

            if (! $plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kế hoạch tập luyện không tồn tại hoặc không thuộc sở hữu của bạn.',
                ], 422, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật buổi tập thành công!',
            'data'    => $session->fresh(['keHoachTapLuyen', 'chiTietBuoiTaps.baiTapTheChat']),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/workout-sessions/{id} : Xóa buổi tập (Anti-IDOR)
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $userId = $request->user()->id;

        $session = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->find($id);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy buổi tập hoặc bạn không có quyền xóa.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa buổi tập thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
