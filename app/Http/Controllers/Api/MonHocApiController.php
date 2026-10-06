<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MonHocApiController extends Controller
{
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
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * 1. GET /api/mon-hoc : Lấy danh sách môn học của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $danhSach = $request->user()->monHocs()
            ->with('baiTaps')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách môn học thành công!',
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['mau_sac'] = $validated['mau_sac'] ?? '#6366f1';
        $validated['trang_thai'] = $validated['trang_thai'] ?? 'dang_hoc';
        $validated['tien_do'] = $validated['tien_do'] ?? 0;
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
    public function show(Request $request, int|string $id): JsonResponse
    {
        $monHoc = $request->user()->monHocs()
            ->with('baiTaps')
            ->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền truy cập.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết môn học thành công!',
            'data'    => $monHoc,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/mon-hoc/{id} : Cập nhật môn học (Anti-IDOR)
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $monHoc = $request->user()->monHocs()->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền cập nhật.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
        if (! empty($mergeData)) {
            $request->merge($mergeData);
        }

        $rules = [
            'ma_mon'        => 'sometimes|required|string|max:50',
            'ten_mon'       => 'sometimes|required|string|max:255',
            'giang_vien'    => 'nullable|string|max:255',
            'phong_hoc'     => 'nullable|string|max:255',
            'so_tin_chi'    => 'sometimes|required|integer|min:1|max:20',
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
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
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
            'data'    => $monHoc->fresh(),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/mon-hoc/{id} : Xóa môn học (Anti-IDOR)
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $monHoc = $request->user()->monHocs()->find($id);

        if (! $monHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy môn học hoặc bạn không có quyền xóa.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $monHoc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa môn học thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 6. GET /api/bao-cao-hoc-tap : Báo cáo học tập & thống kê điểm GPA cho Mobile App
     */
    public function baoCaoHocTap(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $monHocs = MonHoc::where('user_id', $userId)->with('baiTaps')->get();

        $tongSoMon = $monHocs->count();
        $soMonDangHoc = $monHocs->where('trang_thai', 'dang_hoc')->count();
        $soMonHoanThanh = $monHocs->where('trang_thai', 'da_hoan_thanh')->count();
        $tongTinChi = (int) $monHocs->sum('so_tin_chi');
        $tinChiHoanThanh = (int) $monHocs->where('trang_thai', 'da_hoan_thanh')->sum('so_tin_chi');

        // GPA 10.0 & 4.0 scale calculation
        $validGradeSubjects = $monHocs->whereNotNull('diem_so')->where('diem_so', '>', 0);
        $totalWeightedPoints10 = 0;
        $totalCreditsWithGrade = 0;
        $totalWeightedPoints4 = 0;

        foreach ($validGradeSubjects as $m) {
            $score10 = (float) $m->diem_so;
            $credits = (int) $m->so_tin_chi;

            $totalWeightedPoints10 += ($score10 * $credits);
            $totalCreditsWithGrade += $credits;

            $score4 = match (true) {
                $score10 >= 8.5 => 4.0,
                $score10 >= 7.0 => 3.0,
                $score10 >= 5.5 => 2.0,
                $score10 >= 4.0 => 1.0,
                default => 0.0,
            };
            $totalWeightedPoints4 += ($score4 * $credits);
        }

        $gpa10 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints10 / $totalCreditsWithGrade, 2) : 0.0;
        $gpa4 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints4 / $totalCreditsWithGrade, 2) : 0.0;

        // Xếp loại học lực
        $xepLoai = match (true) {
            $gpa4 >= 3.6 => ['label' => 'Xuất sắc', 'color' => 'success', 'icon' => 'bi-trophy-fill'],
            $gpa4 >= 3.2 => ['label' => 'Giỏi', 'color' => 'primary', 'icon' => 'bi-award-fill'],
            $gpa4 >= 2.5 => ['label' => 'Khá', 'color' => 'info', 'icon' => 'bi-star-fill'],
            $gpa4 >= 2.0 => ['label' => 'Trung bình', 'color' => 'warning', 'icon' => 'bi-hand-thumbs-up-fill'],
            default => ['label' => 'Yếu / Kém', 'color' => 'danger', 'icon' => 'bi-exclamation-triangle-fill'],
        };

        // Assignment analytics
        $allAssignments = $monHocs->pluck('baiTaps')->flatten();
        $tongBaiTap = $allAssignments->count();
        $baiTapHoanThanh = $allAssignments->where('trang_thai', 'da_hoan_thanh')->count();
        $baiTapChuaNop = $allAssignments->where('trang_thai', '!=', 'da_hoan_thanh')->count();
        $tyLeHoanThanhBaiTap = $tongBaiTap > 0 ? round(($baiTapHoanThanh / $tongBaiTap) * 100) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo học tập thành công!',
            'data'    => [
                'gpa'                     => $gpa4,
                'gpa_10'                  => $gpa10,
                'gpa_4'                   => $gpa4,
                'tong_tin_chi'            => $tongTinChi,
                'tin_chi_hoan_thanh'      => $tinChiHoanThanh,
                'hoc_luc'                 => $xepLoai['label'],
                'xep_loai'                => $xepLoai,
                'tong_mon_hoc'            => $tongSoMon,
                'so_mon_dang_hoc'         => $soMonDangHoc,
                'so_mon_hoan_thanh'       => $soMonHoanThanh,
                'tong_bai_tap'            => $tongBaiTap,
                'bai_tap_hoan_thanh'      => $baiTapHoanThanh,
                'bai_tap_chua_nop'        => $baiTapChuaNop,
                'ty_le_hoan_thanh_bai_tap' => $tyLeHoanThanhBaiTap,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
