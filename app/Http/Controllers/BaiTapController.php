<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\MonHoc;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            ->orderByRaw("CASE WHEN trang_thai = 'da_hoan_thanh' THEN 1 ELSE 0 END ASC")
            ->orderBy('han_nop', 'asc')
            ->get();

        return view('bai_tap.index', compact('baiTaps'));
    }

    /**
     * Form tạo bài tập mới
     */
    public function create()
    {
        $monHocs = MonHoc::where('user_id', Auth::id())->get();

        return view('bai_tap.create', compact('monHocs'));
    }

    /**
     * Lưu bài tập mới vào CSDL & tự động đồng bộ vào Lịch
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'han_nop' => 'required|date',
            'muc_do_uu_tien' => 'required|in:thap,trung_binh,cao',
            'trang_thai' => 'nullable|in:chua_hoan_thanh,dang_thuc_hien,da_hoan_thanh',
        ]);

        if (empty($validated['trang_thai'])) {
            $validated['trang_thai'] = 'chua_hoan_thanh';
        }

        // Đảm bảo môn học thuộc về user đang đăng nhập
        $monHoc = MonHoc::where('id', $validated['mon_hoc_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $baiTap = $monHoc->baiTaps()->create($validated);

        // Tự động đồng bộ vào trang Lịch dưới mục Deadline (bảng su_kien)
        $dueTime = Carbon::parse($validated['han_nop']);
        SuKien::create([
            'user_id' => Auth::id(),
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Deadline: '.$validated['tieu_de'],
            'mo_ta' => 'Hạn nộp bài tập môn '.$monHoc->ten_mon.($validated['mo_ta'] ? (' - '.$validated['mo_ta']) : ''),
            'loai_su_kien' => 'deadline',
            'thoi_gian_bat_dau' => $dueTime,
            'thoi_gian_ket_thuc' => $dueTime->copy()->addHour(),
            'mau_hien_thi' => '#ef4444',
            'bat_thong_bao' => true,
            'so_ngay_nhac' => 1,
            'quy_tac_lap' => 'once',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm bài tập thành công và tự động đồng bộ vào Lịch!',
                'data' => $baiTap->load('monHoc'),
            ]);
        }

        return redirect()->route('mon-hoc.index')->with('status', 'Thêm bài tập thành công và tự động đồng bộ vào Lịch!');
    }

    /**
     * Bật/tắt trạng thái hoàn thành bài tập (AJAX Toggle)
     */
    public function toggleStatus(BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        $newStatus = ($baiTap->trang_thai === 'da_hoan_thanh') ? 'chua_hoan_thanh' : 'da_hoan_thanh';
        $baiTap->update(['trang_thai' => $newStatus]);

        return response()->json([
            'success' => true,
            'trang_thai' => $newStatus,
            'is_completed' => ($newStatus === 'da_hoan_thanh'),
            'message' => ($newStatus === 'da_hoan_thanh') ? 'Đã đánh dấu hoàn thành bài tập!' : 'Đã chuyển về chưa hoàn thành.',
        ]);
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

        $oldTitle = $baiTap->tieu_de;
        $oldMonHocId = $baiTap->mon_hoc_id;

        $baiTap->update($validated);

        // Đồng bộ cập nhật sang sự kiện Lịch trình (Deadline)
        $dueTime = Carbon::parse($validated['han_nop']);
        $newMonHoc = MonHoc::find($validated['mon_hoc_id']);

        $suKien = SuKien::where('user_id', Auth::id())
            ->where(function ($q) use ($oldMonHocId, $validated) {
                $q->where('mon_hoc_id', $oldMonHocId)
                    ->orWhere('mon_hoc_id', $validated['mon_hoc_id']);
            })
            ->where('loai_su_kien', 'deadline')
            ->where(function ($q) use ($oldTitle, $validated) {
                $q->where('tieu_de', 'like', '%'.$oldTitle.'%')
                    ->orWhere('tieu_de', 'like', '%'.$validated['tieu_de'].'%');
            })
            ->first();

        if ($suKien) {
            $suKien->update([
                'mon_hoc_id' => $validated['mon_hoc_id'],
                'tieu_de' => 'Deadline: '.$validated['tieu_de'],
                'mo_ta' => 'Hạn nộp bài tập môn '.($newMonHoc?->ten_mon ?? '').($validated['mo_ta'] ? (' - '.$validated['mo_ta']) : ''),
                'thoi_gian_bat_dau' => $dueTime,
                'thoi_gian_ket_thuc' => $dueTime->copy()->addHour(),
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài tập thành công và đã đồng bộ Lịch!',
                'data' => $baiTap->load('monHoc'),
            ]);
        }

        return redirect()->route('mon-hoc.index')->with('status', 'Cập nhật bài tập thành công!');
    }

    /**
     * Xóa bài tập
     */
    public function destroy(Request $request, BaiTap $baiTap)
    {
        abort_if($baiTap->monHoc->user_id !== Auth::id(), 403);

        // Xóa sự kiện Lịch đồng bộ kèm theo nếu có
        SuKien::where('user_id', Auth::id())
            ->where('mon_hoc_id', $baiTap->mon_hoc_id)
            ->where('tieu_de', 'like', '%'.$baiTap->tieu_de.'%')
            ->forceDelete();

        $baiTap->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bài tập thành công!',
            ]);
        }

        return redirect()->route('mon-hoc.index')->with('status', 'Đã xóa bài tập!');
    }
}
