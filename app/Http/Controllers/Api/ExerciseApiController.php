<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiTapTheChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseApiController extends Controller
{
    /**
     * GET /api/exercises
     * Lấy danh mục bài tập thể chất hệ thống (có hỗ trợ tìm kiếm & lọc & phân trang).
     */
    public function index(Request $request): JsonResponse
    {
        $query = BaiTapTheChat::query();

        // 1. Tìm kiếm theo từ khóa (tên bài tập, nhóm cơ, mô tả)
        $search = trim($request->input('q', $request->input('search', '')));
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ten_bai_tap', 'like', "%{$search}%")
                  ->orWhere('nhom_co', 'like', "%{$search}%")
                  ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        // 2. Lọc theo nhóm cơ
        if ($request->filled('nhom_co')) {
            $query->where('nhom_co', $request->input('nhom_co'));
        }

        // 3. Lọc theo loại bài tập
        if ($request->filled('loai_bai_tap')) {
            $query->where('loai_bai_tap', $request->input('loai_bai_tap'));
        }

        $perPage = (int) $request->input('per_page', 0);
        if ($perPage > 0) {
            $data = $query->orderBy('id')->paginate($perPage);
        } else {
            $data = $query->orderBy('id')->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách bài tập thể chất thành công!',
            'data'    => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/exercises/{id}
     * Chi tiết bài tập thể chất hệ thống.
     */
    public function show(int|string $id): JsonResponse
    {
        $exercise = BaiTapTheChat::find($id);

        if (! $exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập thể chất.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết bài tập thể chất.',
            'data'    => $exercise,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
