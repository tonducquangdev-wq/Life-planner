<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\MonHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LichHocApiController extends Controller
{
    /**
     * 1. GET /api/lich-hoc : Lấy lịch học của Auth User
     */
    public function index(): JsonResponse
    {
        $danhSach = LichHoc::where('user_id', Auth::id())
            ->with('monHoc')
            ->orderBy('ngay_trong_tuan', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/lich-hoc : Thêm lịch học mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mon_hoc_id'      => 'required|exists:mon_hoc,id',
            'ngay_trong_tuan' => 'required|integer|between:1,7',
            'gio_bat_dau'     => 'required|date_format:H:i',
            'gio_ket_thuc'    => 'required|date_format:H:i|after:gio_bat_dau',
            'ghi_chu'         => 'nullable|string|max:500',
        ]);

        // Anti-IDOR Check: Môn học phải thuộc sở hữu của user
        $monHoc = MonHoc::where('user_id', Auth::id())->find($validated['mon_hoc_id']);
        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Môn học không tồn tại hoặc không thuộc quyền sở hữu của bạn.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated['user_id'] = Auth::id();

        $lichHoc = LichHoc::create($validated);
        $lichHoc->load('monHoc');

        return response()->json([
            'success' => true,
            'message' => 'Thêm lịch học thành công!',
            'data'    => $lichHoc,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/lich-hoc/{id} : Chi tiết lịch học (Anti-IDOR)
     */
    public function show($id): JsonResponse
    {
        $lichHoc = LichHoc::where('user_id', Auth::id())
            ->with('monHoc')
            ->find($id);

        if (! $lichHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch học hoặc bạn không có quyền truy cập.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $lichHoc,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/lich-hoc/{id} : Cập nhật lịch học (Anti-IDOR)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $lichHoc = LichHoc::where('user_id', Auth::id())->find($id);

        if (! $lichHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch học hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'mon_hoc_id'      => 'sometimes|required|exists:mon_hoc,id',
            'ngay_trong_tuan' => 'sometimes|required|integer|between:1,7',
            'gio_bat_dau'     => 'sometimes|required|date_format:H:i',
            'gio_ket_thuc'    => 'sometimes|required|date_format:H:i|after:gio_bat_dau',
            'ghi_chu'         => 'nullable|string|max:500',
        ]);

        if (isset($validated['mon_hoc_id'])) {
            $monHoc = MonHoc::where('user_id', Auth::id())->find($validated['mon_hoc_id']);
            if (! $monHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Môn học mới không hợp lệ hoặc không thuộc sở hữu của bạn.',
                ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        $lichHoc->update($validated);
        $lichHoc->load('monHoc');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lịch học thành công!',
            'data'    => $lichHoc,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/lich-hoc/{id} : Xóa lịch học (Anti-IDOR)
     */
    public function destroy($id): JsonResponse
    {
        $lichHoc = LichHoc::where('user_id', Auth::id())->find($id);

        if (! $lichHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch học hoặc bạn không có quyền xóa.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $lichHoc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa lịch học thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
