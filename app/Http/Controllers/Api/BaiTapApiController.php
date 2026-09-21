<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use Illuminate\Http\Request;

class BaiTapApiController extends Controller
{
    /**
     * 1. GET /api/bai-tap : Lấy danh sách bài tập
     */
    public function index()
    {
        $danhSach = BaiTap::with('monHoc')->get();
        return response()->json([
            'success' => true,
            'data' => $danhSach
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/bai-tap : Thêm bài tập mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'tieu_de' => 'required|string|max:255',
            'mo_ta'       => 'nullable|string',
            'han_nop' => 'required|date',
            'muc_do_uu_tien' => 'nullable|in:thap,trung_binh,cao',
            'trang_thai' => 'nullable|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
        ]);

        $baiTap = BaiTap::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm bài tập thành công!',
            'data'    => $baiTap
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/bai-tap/{id} : Lấy chi tiết 1 bài tập
     */
    public function show($id)
    {
        $baiTap = BaiTap::find($id);

        if (!$baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $baiTap
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/bai-tap/{id} : Cập nhật thông tin bài tập
     */
    public function update(Request $request, $id)
    {
        $baiTap = BaiTap::find($id);

        if (!$baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'tieu_de' => 'sometimes|required|string|max:255',
            'mo_ta'       => 'nullable|string',
        ]);

        $baiTap->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công!',
            'data'    => $baiTap
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/bai-tap/{id} : Xóa bài tập
     */
    public function destroy($id)
    {
        $baiTap = BaiTap::find($id);

        if (!$baiTap) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $baiTap->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài tập thành công!'
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
