<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuKienApiController extends Controller
{
    /**
     * 1. GET /api/su-kien : Lấy danh sách sự kiện Calendar của Auth User
     */
    public function index(): JsonResponse
    {
        $danhSach = SuKien::where('user_id', Auth::id())
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/su-kien : Thêm sự kiện Calendar mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tieu_de'           => 'required|string|max:255',
            'mo_ta'             => 'nullable|string',
            'loai_su_kien'      => 'nullable|string|max:50',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'required|date|after_or_equal:thoi_gian_bat_dau',
            'mau_hien_thi'      => 'nullable|string|max:20',
            'bat_thong_bao'     => 'nullable|boolean',
            'so_ngay_nhac'      => 'nullable|integer|min:1|max:30',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['loai_su_kien'] = $validated['loai_su_kien'] ?? 'ca_nhan';
        $validated['mau_hien_thi'] = $validated['mau_hien_thi'] ?? '#6366f1';
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao', false);
        $validated['so_ngay_nhac'] = $validated['so_ngay_nhac'] ?? 1;

        $suKien = SuKien::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo sự kiện thành công!',
            'data'    => $suKien,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/su-kien/{id} : Chi tiết sự kiện (Anti-IDOR)
     */
    public function show($id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền truy cập.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $suKien,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/su-kien/{id} : Cập nhật sự kiện (Anti-IDOR)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'tieu_de'           => 'sometimes|required|string|max:255',
            'mo_ta'             => 'nullable|string',
            'loai_su_kien'      => 'nullable|string|max:50',
            'thoi_gian_bat_dau' => 'sometimes|required|date',
            'thoi_gian_ket_thuc' => 'sometimes|required|date|after_or_equal:thoi_gian_bat_dau',
            'mau_hien_thi'      => 'nullable|string|max:20',
            'bat_thong_bao'     => 'nullable|boolean',
            'so_ngay_nhac'      => 'nullable|integer|min:1|max:30',
        ]);

        $suKien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sự kiện thành công!',
            'data'    => $suKien,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/su-kien/{id} : Xóa sự kiện (Anti-IDOR + SoftDeletes)
     */
    public function destroy($id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền xóa.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $suKien->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sự kiện thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
