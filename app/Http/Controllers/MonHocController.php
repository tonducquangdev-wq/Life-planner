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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ]);

        // Gán user_id bằng Auth::id() và tạo môn học
        $validated['user_id'] = Auth::id();
        $validated['mau_sac'] = $request->input('mau_sac', '#6366f1');
        $validated['tien_do'] = $request->input('tien_do', 0);

        MonHoc::create($validated);

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

        $validated = $request->validate([
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ]);

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
}
