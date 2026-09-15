<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaiTapController extends Controller
{
    /**
     * Danh sách bài tập của user hiện tại
     */
    public function index()
    {
        $baiTaps = BaiTap::whereHas('monHoc', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('monHoc')
            ->latest()
            ->get();

        return view('bai_tap.index', compact('baiTaps'));
    }

    /**
     * Form tạo bài tập mới
     */
    public function create()
    {
        // Lấy danh sách môn học của user để chọn khi tạo bài tập
        $monHocs = MonHoc::where('user_id', Auth::id())->get();

        return view('bai_tap.create', compact('monHocs'));
    }

    /**
     * Lưu bài tập mới vào CSDL
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'han_nop' => 'required|date',
            'muc_do_uu_tien' => 'required|in:thap,trung_binh,cao',
            'trang_thai' => 'required|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
        ]);

        // Đảm bảo môn học thuộc về user đang đăng nhập
        $monHoc = MonHoc::where('id', $validated['mon_hoc_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $monHoc->baiTaps()->create($validated);

        return redirect()->route('bai-tap.index')->with('status', 'Thêm bài tập thành công!');
    }

    /**
     * Xem chi tiết bài tập
     */
    public function show(BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        return view('bai_tap.show', compact('baiTap'));
    }

    /**
     * Form chỉnh sửa bài tập
     */
    public function edit(BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        $monHocs = MonHoc::where('user_id', Auth::id())->get();

        return view('bai_tap.edit', compact('baiTap', 'monHocs'));
    }

    /**
     * Cập nhật bài tập
     */
    public function update(Request $request, BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'han_nop' => 'required|date',
            'muc_do_uu_tien' => 'required|in:thap,trung_binh,cao',
            'trang_thai' => 'required|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
        ]);

        $baiTap->update($validated);

        return redirect()->route('bai-tap.index')->with('status', 'Cập nhật bài tập thành công!');
    }

    /**
     * Xóa bài tập
     */
    public function destroy(BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        $baiTap->delete();

        return redirect()->route('bai-tap.index')->with('status', 'Đã xóa bài tập!');
    }
}
