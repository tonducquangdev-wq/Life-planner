<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use Illuminate\Http\Request;

class MonHocApiController extends Controller
{
    /**
     * 1. GET /api/mon-hoc : Lấy danh sách môn học
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;

        $query = MonHoc::with('baiTaps');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $danhSach = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $danhSach
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/mon-hoc : Thêm môn học mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'ma_mon'        => 'required|string|max:50',
            'ten_mon'       => 'required|string|max:255',
            'giang_vien'    => 'nullable|string|max:255',
            'phong_hoc'     => 'nullable|string|max:255',
            'so_tin_chi'    => 'required|integer|min:1|max:20',
            'tien_do'       => 'nullable|integer|min:0|max:100',
            'diem_so'        => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau'  => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'mau_sac'       => 'nullable|string|max:20',
            'trang_thai'    => 'nullable|in:dang_hoc,da_hoan_thanh,tam_dung',
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        $validated['mau_sac'] = $validated['mau_sac'] ?? '#6366f1';
        $validated['trang_thai'] = $validated['trang_thai'] ?? 'dang_hoc';

        $monHoc = MonHoc::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm môn học thành công!',
            'data'    => $monHoc
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/mon-hoc/{id} : Lấy chi tiết 1 môn học
     */
    public function show($id)
    {
        $monHoc = MonHoc::with('baiTaps')->find($id);

        if (!$monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $monHoc
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT/PATCH /api/mon-hoc/{id} : Cập nhật môn học
     */
    public function update(Request $request, $id)
    {
        $monHoc = MonHoc::find($id);

        if (!$monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $validated = $request->validate([
            'ma_mon'        => 'sometimes|required|string|max:50',
            'ten_mon'       => 'sometimes|required|string|max:255',
            'giang_vien'    => 'nullable|string|max:255',
            'phong_hoc'     => 'nullable|string|max:255',
            'so_tin_chi'    => 'nullable|integer|min:1|max:20',
            'tien_do'       => 'nullable|integer|min:0|max:100',
            'diem_so'        => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau'  => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'mau_sac'       => 'nullable|string|max:20',
            'trang_thai'    => 'nullable|in:dang_hoc,da_hoan_thanh,tam_dung',
        ]);

        $monHoc->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật môn học thành công!',
            'data'    => $monHoc
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/mon-hoc/{id} : Xóa môn học
     */
    public function destroy($id)
    {
        $monHoc = MonHoc::find($id);

        if (!$monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học'
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $monHoc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa môn học thành công!'
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
