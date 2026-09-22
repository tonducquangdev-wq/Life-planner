<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\MonHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaiTapApiController extends Controller
{
    /**
     * 1. GET /api/bai-tap : Lấy danh sách bài tập thuộc môn học của Auth User
     */
    public function index(): JsonResponse
    {
        $danhSach = BaiTap::whereHas('monHoc', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('monHoc')->get();

        return response()->json([
            'success' => true,
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/bai-tap : Thêm bài tập mới (Kiểm tra môn học thuộc Auth User)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mon_hoc_id'     => 'required|exists:mon_hoc,id',
            'tieu_de'        => 'required|string|max:255',
            'mo_ta'          => 'nullable|string',
            'han_nop'        => 'required|date',
            'muc_do_uu_tien' => 'nullable|in:thap,trung_binh,cao',
            'trang_thai'     => 'nullable|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
        ]);

        // Ownership Check: Môn học phải thuộc về User hiện tại
        $monHoc = MonHoc::where('user_id', Auth::id())->find($validated['mon_hoc_id']);
        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Môn học không tồn tại hoặc bạn không có quyền gán bài tập cho môn này.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated['muc_do_uu_tien'] = $validated['muc_do_uu_tien'] ?? 'trung_binh';
        $validated['trang_thai'] = $validated['trang_thai'] ?? 'chua_hoan_thanh';

        $baiTap = BaiTap::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm bài tập thành công!',
            'data'    => $baiTap,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/bai-tap/{id} : Lấy chi tiết 1 bài tập (Anti-IDOR)
     */
    public function show($id): JsonResponse
    {
        $baiTap = BaiTap::whereHas('monHoc', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('monHoc')->find($id);

        if (! $baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập hoặc bạn không có quyền truy cập.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $baiTap,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/bai-tap/{id} : Cập nhật thông tin bài tập (Anti-IDOR)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $baiTap = BaiTap::whereHas('monHoc', function ($query) {
            $query->where('user_id', Auth::id());
        })->find($id);

        if (! $baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'mon_hoc_id'     => 'sometimes|required|exists:mon_hoc,id',
            'tieu_de'        => 'sometimes|required|string|max:255',
            'mo_ta'          => 'nullable|string',
            'han_nop'        => 'sometimes|required|date',
            'muc_do_uu_tien' => 'nullable|in:thap,trung_binh,cao',
            'trang_thai'     => 'nullable|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
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

        $baiTap->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài tập thành công!',
            'data'    => $baiTap,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/bai-tap/{id} : Xóa bài tập (Anti-IDOR)
     */
    public function destroy($id): JsonResponse
    {
        $baiTap = BaiTap::whereHas('monHoc', function ($query) {
            $query->where('user_id', Auth::id());
        })->find($id);

        if (! $baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập hoặc bạn không có quyền xóa.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $baiTap->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài tập thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
