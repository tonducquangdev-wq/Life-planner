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
}
