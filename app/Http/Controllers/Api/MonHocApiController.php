<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonHocApiController extends Controller
{
    /**
     * 1. GET /api/mon-hoc : Lấy danh sách môn học của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $danhSach = MonHoc::where('user_id', Auth::id())
            ->with('baiTaps')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/mon-hoc : Thêm môn học mới cho Auth User
    /**
     * Chuẩn hóa định dạng ngày từ d/m/Y hoặc Y-m-d về chuẩn Y-m-d.
     * Trả về null nếu giá trị rỗng.
     */
    protected function normalizeDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $matches)) {
            return sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * 2. POST /api/mon-hoc : Thêm môn học mới
     */
    public function store(Request $request): JsonResponse
    {
        $rawStart = $request->input('ngay_bat_dau');
        $rawEnd = $request->input('ngay_ket_thuc');

        $normalizedStart = $this->normalizeDate($rawStart);
        $normalizedEnd = $this->normalizeDate($rawEnd);

        $request->merge([
            'ngay_bat_dau' => $normalizedStart,
            'ngay_ket_thuc' => $normalizedEnd,
        ]);

        $rules = [
            'ma_mon'        => 'required|string|max:50',
            'ten_mon'       => 'required|string|max:255',
            'giang_vien'    => 'nullable|string|max:255',
            'phong_hoc'     => 'nullable|string|max:255',
            'so_tin_chi'    => 'required|integer|min:1|max:20',
            'tien_do'       => 'nullable|integer|min:0|max:100',
            'diem_so'       => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau'  => 'nullable|date',
            'mau_sac'       => 'nullable|string|max:20',
            'trang_thai'    => 'nullable|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        if ($normalizedStart && $normalizedEnd) {
            $rules['ngay_ket_thuc'] = 'nullable|date|after_or_equal:ngay_bat_dau';
        } else {
            $rules['ngay_ket_thuc'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'ngay_ket_thuc.after_or_equal' => 'The ngày kết thúc field must be a date after or equal to ngày bắt đầu.',
        ], [
            'ngay_bat_dau' => 'ngày bắt đầu',
            'ngay_ket_thuc' => 'ngày kết thúc',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['mau_sac'] = $validated['mau_sac'] ?? '#6366f1';
        $validated['trang_thai'] = $validated['trang_thai'] ?? 'dang_hoc';
        $validated['ngay_bat_dau'] = $normalizedStart;
        $validated['ngay_ket_thuc'] = $normalizedEnd;

        $monHoc = MonHoc::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm môn học thành công!',
            'data'    => $monHoc,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/mon-hoc/{id} : Lấy chi tiết 1 môn học (Anti-IDOR)
     */
    public function show($id): JsonResponse
    {
        $monHoc = MonHoc::where('user_id', Auth::id())
            ->with('baiTaps')
            ->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền truy cập.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'data'    => $monHoc,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/mon-hoc/{id} : Cập nhật môn học (Anti-IDOR)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $monHoc = MonHoc::where('user_id', Auth::id())->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền cập nhật.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $hasStart = $request->has('ngay_bat_dau');
        $hasEnd = $request->has('ngay_ket_thuc');

        $normalizedStart = $hasStart ? $this->normalizeDate($request->input('ngay_bat_dau')) : null;
        $normalizedEnd = $hasEnd ? $this->normalizeDate($request->input('ngay_ket_thuc')) : null;

        $mergeData = [];
        if ($hasStart) {
            $mergeData['ngay_bat_dau'] = $normalizedStart;
        }
        if ($hasEnd) {
            $mergeData['ngay_ket_thuc'] = $normalizedEnd;
        }
        if (!empty($mergeData)) {
            $request->merge($mergeData);
        }

        $rules = [
            'ma_mon'        => 'sometimes|required|string|max:50',
            'ten_mon'       => 'sometimes|required|string|max:255',
            'giang_vien'    => 'nullable|string|max:255',
            'phong_hoc'     => 'nullable|string|max:255',
            'so_tin_chi'    => 'nullable|integer|min:1|max:20',
            'tien_do'       => 'nullable|integer|min:0|max:100',
            'diem_so'       => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau'  => 'nullable|date',
            'mau_sac'       => 'nullable|string|max:20',
            'trang_thai'    => 'nullable|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        // Xác định ngày bắt đầu hiệu lực
        $effectiveStartDate = null;
        if ($hasStart) {
            $effectiveStartDate = $normalizedStart;
        } elseif ($monHoc->ngay_bat_dau) {
            $effectiveStartDate = $monHoc->ngay_bat_dau->format('Y-m-d');
        }

        $effectiveEndDate = null;
        if ($hasEnd) {
            $effectiveEndDate = $normalizedEnd;
        } elseif ($monHoc->ngay_ket_thuc) {
            $effectiveEndDate = $monHoc->ngay_ket_thuc->format('Y-m-d');
        }

        if ($effectiveStartDate && $effectiveEndDate) {
            $rules['ngay_ket_thuc'] = 'nullable|date|after_or_equal:' . $effectiveStartDate;
        } else {
            $rules['ngay_ket_thuc'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'ngay_ket_thuc.after_or_equal' => 'The ngày kết thúc field must be a date after or equal to ngày bắt đầu.',
        ], [
            'ngay_bat_dau' => 'ngày bắt đầu',
            'ngay_ket_thuc' => 'ngày kết thúc',
        ]);

        if ($hasStart) {
            $validated['ngay_bat_dau'] = $normalizedStart;
        }
        if ($hasEnd) {
            $validated['ngay_ket_thuc'] = $normalizedEnd;
        }

        $monHoc->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật môn học thành công!',
            'data'    => $monHoc,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/mon-hoc/{id} : Xóa môn học (Anti-IDOR)
     */
    public function destroy($id): JsonResponse
    {
        $monHoc = MonHoc::where('user_id', Auth::id())->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền xóa.',
            ], 403, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $monHoc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa môn học thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
