<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCongViecRequest;
use App\Http\Requests\UpdateCongViecRequest;
use App\Models\CongViec;
use App\Models\DuAn;
use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CongViecController extends Controller
{
    /**
     * Danh sách công việc (Kanban & List view)
     */
    public function index(Request $request)
    {
        $query = CongViec::where('user_id', Auth::id())
            ->with(['duAn:id,ten_du_an,mau_nhan', 'suKien']);

        if ($request->filled('du_an_id')) {
            $query->where('du_an_id', $request->input('du_an_id'));
        }

        if ($request->filled('uu_tien')) {
            $query->where('uu_tien', $request->input('uu_tien'));
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->input('trang_thai'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('ten_cong_viec', 'like', "%{$search}%");
        }

        $congViecs = $query->orderBy('deadline', 'asc')->get();
        $duAns = DuAn::where('user_id', Auth::id())->select('id', 'ten_du_an', 'mau_nhan')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $congViecs,
            ]);
        }

        // Dữ liệu phân nhóm Kanban
        $kanban = [
            'can_lam' => $congViecs->where('trang_thai', 'can_lam')->values(),
            'dang_lam' => $congViecs->where('trang_thai', 'dang_lam')->values(),
            'cho_duyet' => $congViecs->where('trang_thai', 'cho_duyet')->values(),
            'hoan_thanh' => $congViecs->where('trang_thai', 'hoan_thanh')->values(),
        ];

        return view('cong_viec.index', compact('congViecs', 'kanban', 'duAns'));
    }

    /**
     * Tạo mới công việc (xử lý tùy chọn Đồng bộ Calendar)
     */
    public function store(StoreCongViecRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['dong_bo_calendar'] = $request->boolean('dong_bo_calendar');

        if (($validated['trang_thai'] ?? '') === 'hoan_thanh') {
            $validated['tien_do'] = 100;
        }

        $congViec = CongViec::create($validated);

        // Xử lý đồng bộ lên Lịch cá nhân nếu được chọn
        if ($congViec->dong_bo_calendar && $congViec->deadline) {
            $this->syncTaskToCalendar($congViec);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo công việc thành công.',
                'data' => $congViec->load(['duAn', 'suKien']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Tạo công việc thành công.');
    }

    /**
     * Xem chi tiết 1 công việc
     */
    public function show(Request $request, int $id)
    {
        $congViec = CongViec::where('user_id', Auth::id())
            ->with(['duAn', 'suKien'])
            ->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $congViec,
            ]);
        }

        return view('cong_viec.show', compact('congViec'));
    }

    /**
     * Cập nhật công việc (Đồng bộ Lịch cá nhân linh hoạt)
     */
    public function update(UpdateCongViecRequest $request, int $id)
    {
        $congViec = CongViec::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validated();

        if (isset($validated['trang_thai']) && $validated['trang_thai'] === 'hoan_thanh') {
            $validated['tien_do'] = 100;
        }

        if ($request->has('dong_bo_calendar')) {
            $validated['dong_bo_calendar'] = $request->boolean('dong_bo_calendar');
        }

        $congViec->update($validated);
        $congViec->refresh();

        // Xử lý đồng bộ hoặc gỡ bỏ sự kiện trên Calendar
        if ($congViec->dong_bo_calendar && $congViec->deadline) {
            $this->syncTaskToCalendar($congViec);
        } else {
            // Nếu bỏ chọn đồng bộ -> Xóa sự kiện liên kết trên Calendar
            SuKien::where('cong_viec_id', $congViec->id)->delete();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật công việc thành công.',
                'data' => $congViec->load(['duAn', 'suKien']),
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật công việc thành công.');
    }

    /**
     * Cập nhật nhanh trạng thái qua AJAX (Kanban Drag & Drop)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'trang_thai' => 'required|string|in:can_lam,dang_lam,cho_duyet,hoan_thanh',
        ]);

        $congViec = CongViec::where('user_id', Auth::id())->findOrFail($id);
        $newStatus = $request->input('trang_thai');

        $updateData = ['trang_thai' => $newStatus];
        if ($newStatus === 'hoan_thanh') {
            $updateData['tien_do'] = 100;
        }

        $congViec->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái công việc thành công.',
            'data' => $congViec,
        ]);
    }

    /**
     * Cập nhật nhanh tiến độ % qua AJAX Slider
     */
    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'tien_do' => 'required|integer|min:0|max:100',
        ]);

        $congViec = CongViec::where('user_id', Auth::id())->findOrFail($id);
        $tienDo = (int) $request->input('tien_do');

        $updateData = ['tien_do' => $tienDo];
        if ($tienDo === 100) {
            $updateData['trang_thai'] = 'hoan_thanh';
        }

        $congViec->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tiến độ thành công.',
            'data' => $congViec,
        ]);
    }

    /**
     * Toggle nút đồng bộ Lịch
     */
    public function syncToCalendar(Request $request, int $id): JsonResponse
    {
        $congViec = CongViec::where('user_id', Auth::id())->findOrFail($id);
        $sync = $request->boolean('dong_bo');

        $congViec->update(['dong_bo_calendar' => $sync]);

        if ($sync && $congViec->deadline) {
            $this->syncTaskToCalendar($congViec);
        } else {
            SuKien::where('cong_viec_id', $congViec->id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $sync ? 'Đã đồng bộ công việc lên Lịch.' : 'Đã hủy đồng bộ khỏi Lịch.',
        ]);
    }

    /**
     * Xóa công việc
     */
    public function destroy(Request $request, int $id)
    {
        $congViec = CongViec::where('user_id', Auth::id())->findOrFail($id);
        $congViec->delete(); // Linked SuKien automatically deleted via foreign key cascade

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa công việc thành công.',
            ]);
        }

        return redirect()->back()->with('success', 'Xóa công việc thành công.');
    }

    /**
     * Helper đồng bộ hoặc cập nhật bản ghi SuKien cho công việc
     */
    protected function syncTaskToCalendar(CongViec $task): SuKien
    {
        $startTime = Carbon::parse($task->deadline);
        $endTime = $startTime->copy()->addHour();

        return SuKien::updateOrCreate(
            ['cong_viec_id' => $task->id],
            [
                'user_id' => $task->user_id,
                'tieu_de' => '[Công việc] ' . $task->ten_cong_viec,
                'mo_ta' => $task->mo_ta ?? ('Hạn chót công việc: ' . $task->ten_cong_viec),
                'loai_su_kien' => 'cong_viec',
                'thoi_gian_bat_dau' => $startTime->toDateTimeString(),
                'thoi_gian_ket_thuc' => $endTime->toDateTimeString(),
                'mau_hien_thi' => '#6366F1', // Indigo color for Tasks
                'bat_thong_bao' => true,
                'so_ngay_nhac' => 1,
                'quy_tac_lap' => 'once',
            ]
        );
    }
}
