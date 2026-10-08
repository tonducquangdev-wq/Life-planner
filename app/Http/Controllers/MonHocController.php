<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonHocController extends Controller
{
    /**
     * Hiển thị danh sách môn học của người dùng đăng nhập
     */
    public function index()
    {
        $monHocs = MonHoc::where('user_id', Auth::id())
            ->withCount('baiTaps')
            ->with('baiTaps')
            ->latest()
            ->get();

        return view('mon_hoc.index', compact('monHocs'));
    }

    /**
     * Hiển thị form tạo mới môn học
     */
    public function create()
    {
        return view('mon_hoc.create');
    }

    /**
     * Lưu môn học mới vào CSDL
     */
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

        // Trường hợp đã là Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Trường hợp dd/mm/yyyy hoặc dd-mm-yyyy
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
     * Lưu môn học mới vào CSDL
     */
    public function store(Request $request)
    {
        // Chuẩn hóa ngày trước khi validate để tránh xung đột định dạng d/m/Y vs Y-m-d
        $rawStart = $request->input('ngay_bat_dau');
        $rawEnd = $request->input('ngay_ket_thuc');

        $normalizedStart = $this->normalizeDate($rawStart);
        $normalizedEnd = $this->normalizeDate($rawEnd);

        // Tự động tạo mã môn nếu người dùng để trống
        $maMon = trim((string) $request->input('ma_mon'));
        if ($maMon === '') {
            $rawLetters = preg_replace('/[^A-Za-z0-9]/', '', (string) $request->input('ten_mon', ''));
            $slug = strtoupper(substr($rawLetters, 0, 4));
            $maMon = ($slug ?: 'MH') . rand(100, 999);
        }

        $request->merge([
            'ma_mon' => $maMon,
            'ngay_bat_dau' => $normalizedStart,
            'ngay_ket_thuc' => $normalizedEnd,
            'so_tin_chi' => $request->input('so_tin_chi') ? (int) $request->input('so_tin_chi') : 3,
            'tien_do' => $request->input('tien_do') !== null ? (int) $request->input('tien_do') : 0,
            'trang_thai' => $request->input('trang_thai') ?: 'dang_hoc',
            'mau_sac' => $request->input('mau_sac') ?: '#6366f1',
        ]);

        $rules = [
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'nullable|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        // Chỉ kiểm tra after_or_equal khi CẢ HAI ngày đều được cung cấp
        if ($normalizedStart && $normalizedEnd) {
            $rules['ngay_ket_thuc'] = 'nullable|date|after_or_equal:ngay_bat_dau';
        } else {
            $rules['ngay_ket_thuc'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'ten_mon.required' => 'Vui lòng nhập tên môn học.',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ], [
            'ten_mon' => 'tên môn học',
            'ma_mon' => 'mã môn học',
            'ngay_bat_dau' => 'ngày bắt đầu',
            'ngay_ket_thuc' => 'ngày kết thúc',
        ]);

        // Gán user_id bằng Auth::id() và tạo môn học
        $validated['user_id'] = Auth::id();
        $validated['mau_sac'] = $request->input('mau_sac', '#6366f1');
        $validated['tien_do'] = (int) $request->input('tien_do', 0);
        $validated['so_tin_chi'] = (int) $request->input('so_tin_chi', 3);
        $validated['ngay_bat_dau'] = $normalizedStart;
        $validated['ngay_ket_thuc'] = $normalizedEnd;

        $monHoc = MonHoc::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Môn học đã được thêm thành công!',
                'data' => [
                    'id' => $monHoc->id,
                    'ma_mon' => $monHoc->ma_mon,
                    'ten_mon' => $monHoc->ten_mon,
                    'giang_vien' => $monHoc->giang_vien,
                    'phong_hoc' => $monHoc->phong_hoc,
                    'so_tin_chi' => $monHoc->so_tin_chi,
                    'tien_do' => $monHoc->tien_do,
                    'ngay_bat_dau' => $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : null,
                    'ngay_ket_thuc' => $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('d/m/Y') : null,
                    'mau_sac' => $monHoc->mau_sac,
                    'trang_thai' => $monHoc->trang_thai,
                    'show_url' => route('mon-hoc.show', $monHoc),
                    'edit_url' => route('mon-hoc.edit', $monHoc),
                    'destroy_url' => route('mon-hoc.destroy', $monHoc),
                ],
            ]);
        }

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Thêm môn học thành công!');
    }

    /**
     * Xem chi tiết môn học
     */
    public function show(MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        $monHoc->load('baiTaps');

        return view('mon_hoc.show', compact('monHoc'));
    }

    /**
     * Hiển thị form chỉnh sửa môn học
     */
    public function edit(MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        return view('mon_hoc.edit', compact('monHoc'));
    }

    /**
     * Cập nhật thông tin môn học
     */
    public function update(Request $request, MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

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
        if (!$request->has('so_tin_chi')) {
            $mergeData['so_tin_chi'] = $monHoc->so_tin_chi ?? 3;
        }
        if (!$request->has('tien_do')) {
            $mergeData['tien_do'] = $monHoc->tien_do ?? 0;
        }
        if (!$request->has('diem_so')) {
            $mergeData['diem_so'] = $monHoc->diem_so;
        }
        if (!empty($mergeData)) {
            $request->merge($mergeData);
        }

        $rules = [
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'nullable|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        // Xác định ngày bắt đầu hiệu lực (effective start date)
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

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Cập nhật môn học thành công!');
    }

    /**
     * Xóa mềm môn học (Soft Delete)
     */
    public function destroy(MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        $monHoc->delete();

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Đã xóa môn học thành công!');
    }

    /**
     * Báo cáo học tập & Thống kê điểm GPA
     */
    public function baoCaoHocTap()
    {
        $userId = Auth::id();
        $monHocs = MonHoc::where('user_id', $userId)->with('baiTaps')->get();

        // Seed sample subjects if user currently has none in DB
        if ($monHocs->isEmpty()) {
            $sampleSubjects = [
                ['ma_mon' => 'INT3306', 'ten_mon' => 'Lập trình Web & Laravel 13', 'giang_vien' => 'TS. Nguyễn Văn A', 'phong_hoc' => 'Phòng B2.04', 'so_tin_chi' => 3, 'tien_do' => 85, 'diem_so' => 9.0, 'mau_sac' => '#6366f1', 'trang_thai' => 'dang_hoc'],
                ['ma_mon' => 'INT2204', 'ten_mon' => 'Cơ sở dữ liệu nâng cao', 'giang_vien' => 'ThS. Trần Thị B', 'phong_hoc' => 'Phòng A1.02', 'so_tin_chi' => 4, 'tien_do' => 100, 'diem_so' => 8.5, 'mau_sac' => '#3b82f6', 'trang_thai' => 'da_hoan_thanh'],
                ['ma_mon' => 'INT3110', 'ten_mon' => 'Kiến trúc Phần mềm', 'giang_vien' => 'PGS.TS. Lê Văn C', 'phong_hoc' => 'Phòng C3.01', 'so_tin_chi' => 3, 'tien_do' => 60, 'diem_so' => 8.0, 'mau_sac' => '#10b981', 'trang_thai' => 'dang_hoc'],
                ['ma_mon' => 'ENG201', 'ten_mon' => 'Tiếng Anh Chuyên ngành IT', 'giang_vien' => 'Ms. Emily Smith', 'phong_hoc' => 'Phòng D1.05', 'so_tin_chi' => 2, 'tien_do' => 100, 'diem_so' => 9.5, 'mau_sac' => '#f59e0b', 'trang_thai' => 'da_hoan_thanh'],
                ['ma_mon' => 'INT3401', 'ten_mon' => 'An toàn & Bảo mật Thông tin', 'giang_vien' => 'TS. Phạm Hoàng D', 'phong_hoc' => 'Phòng B2.01', 'so_tin_chi' => 3, 'tien_do' => 40, 'diem_so' => 7.8, 'mau_sac' => '#ec4899', 'trang_thai' => 'dang_hoc'],
            ];

            foreach ($sampleSubjects as $subData) {
                $subData['user_id'] = $userId;
                $m = MonHoc::create($subData);
                $m->baiTaps()->createMany([
                    ['tieu_de' => 'Bài tập lớn '.$m->ten_mon, 'mo_ta' => 'Xây dựng module hệ thống', 'han_nop' => now()->addDays(5), 'muc_do_uu_tien' => 'cao', 'trang_thai' => 'dang_thuc_hien'],
                    ['tieu_de' => 'Bài tập cá nhân 1', 'mo_ta' => 'Trả lời câu hỏi lý thuyết', 'han_nop' => now()->subDays(2), 'muc_do_uu_tien' => 'trung_binh', 'trang_thai' => 'da_hoan_thanh'],
                ]);
            }

            $monHocs = MonHoc::where('user_id', $userId)->with('baiTaps')->get();
        }

        $tongSoMon = $monHocs->count();
        $soMonDangHoc = $monHocs->where('trang_thai', 'dang_hoc')->count();
        $soMonHoanThanh = $monHocs->where('trang_thai', 'da_hoan_thanh')->count();
        $tongTinChi = $monHocs->sum('so_tin_chi');
        $tinChiHoanThanh = $monHocs->where('trang_thai', 'da_hoan_thanh')->sum('so_tin_chi');

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

        $gpa10 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints10 / $totalCreditsWithGrade, 2) : 0;
        $gpa4 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints4 / $totalCreditsWithGrade, 2) : 0;

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

        return view('mon_hoc.bao_cao', compact(
            'monHocs',
            'tongSoMon',
            'soMonDangHoc',
            'soMonHoanThanh',
            'tongTinChi',
            'tinChiHoanThanh',
            'gpa10',
            'gpa4',
            'xepLoai',
            'tongBaiTap',
            'baiTapHoanThanh',
            'baiTapChuaNop',
            'tyLeHoanThanhBaiTap'
        ));
    }

    /**
     * Tìm kiếm và xem thông tin gia sư phục vụ sinh viên
     */
    public function danhSachGiaSu(Request $request)
    {
        $keyword = trim($request->input('q', ''));
        $monHocId = $request->input('mon_hoc_id');
        $mucGia = $request->input('muc_gia');

        $query = \App\Models\GiaSu::with('monHoc');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%{$keyword}%")
                  ->orWhere('chuyen_mon', 'like', "%{$keyword}%")
                  ->orWhere('mo_ta_kinh_nghiem', 'like', "%{$keyword}%");
            });
        }

        if (!empty($monHocId)) {
            $query->where('mon_hoc_id', $monHocId);
        }

        if ($mucGia === 'duoi_180') {
            $query->where('hoc_phi_theo_gio', '<=', 180000);
        } elseif ($mucGia === 'tren_180') {
            $query->where('hoc_phi_theo_gio', '>', 180000);
        }

        $giaSus = $query->orderByDesc('danh_gia')->get();
        $monHocs = MonHoc::where('user_id', Auth::id())->get();

        return view('mon_hoc.gia_su', compact('giaSus', 'monHocs', 'keyword', 'monHocId', 'mucGia'));
    }
}
