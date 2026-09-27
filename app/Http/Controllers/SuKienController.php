<?php

namespace App\Http\Controllers;

use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuKienController extends Controller
{
    /**
     * Danh sách sự kiện của user hiện tại
     */
    public function index(): JsonResponse
    {
        $suKiens = SuKien::where('user_id', Auth::id())
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        $data = $suKiens->map(function ($evt) {
            return [
                'id' => $evt->id,
                'title' => $evt->tieu_de,
                'start' => $evt->thoi_gian_bat_dau ? $evt->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $evt->thoi_gian_ket_thuc ? $evt->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $evt->loai_su_kien),
                'color' => $evt->mau_hien_thi ?? '#3B82F6',
                'location' => $evt->mo_ta,
                'bat_thong_bao' => (bool) $evt->bat_thong_bao,
                'so_ngay_nhac' => (int) ($evt->so_ngay_nhac ?? 1),
                'quy_tac_lap' => $evt->quy_tac_lap ?? 'once',
                'nhom_lap_id' => $evt->nhom_lap_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Tạo mới sự kiện cho user hiện tại (Hỗ trợ lặp hằng ngày, hằng tuần, hằng tháng)
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'tieu_de' => 'required|string|max:255',
            'loai_su_kien' => 'required|string',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'nullable|date',
            'mo_ta' => 'nullable|string',
            'mau_hien_thi' => 'nullable|string',
            'bat_thong_bao' => 'nullable|boolean',
            'so_ngay_nhac' => 'nullable|integer|in:1,2',
            'quy_tac_lap' => 'nullable|string|in:once,daily,weekly,monthly',
        ];

        if ($request->filled('thoi_gian_bat_dau') && $request->filled('thoi_gian_ket_thuc')) {
            $rules['thoi_gian_ket_thuc'] .= '|after_or_equal:thoi_gian_bat_dau';
        }

        $validated = $request->validate($rules, [
            'thoi_gian_ket_thuc.after_or_equal' => 'The thời gian kết thúc field must be a date after or equal to thời gian bắt đầu.',
        ], [
            'thoi_gian_bat_dau' => 'thời gian bắt đầu',
            'thoi_gian_ket_thuc' => 'thời gian kết thúc',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);

        $quyTacLap = $request->input('quy_tac_lap', 'once');
        $validated['quy_tac_lap'] = $quyTacLap;

        if (in_array($quyTacLap, ['daily', 'weekly', 'monthly'])) {
            $nhomLapId = (string) Str::uuid();
            $validated['nhom_lap_id'] = $nhomLapId;

            $start = Carbon::parse($validated['thoi_gian_bat_dau']);
            $end = !empty($validated['thoi_gian_ket_thuc']) ? Carbon::parse($validated['thoi_gian_ket_thuc']) : null;
            $durationInSeconds = $end ? $start->diffInSeconds($end) : null;

            $count = match ($quyTacLap) {
                'daily' => 30,    // Tạo 30 ngày lặp liên tiếp
                'weekly' => 12,   // Tạo 12 tuần lặp liên tiếp (3 tháng)
                'monthly' => 6,   // Tạo 6 tháng lặp liên tiếp
                default => 1,
            };

            $createdEvents = [];
            for ($i = 0; $i < $count; $i++) {
                $instanceStart = match ($quyTacLap) {
                    'daily' => $start->copy()->addDays($i),
                    'weekly' => $start->copy()->addWeeks($i),
                    'monthly' => $start->copy()->addMonths($i),
                    default => $start->copy(),
                };

                $instanceData = $validated;
                $instanceData['thoi_gian_bat_dau'] = $instanceStart->toDateTimeString();
                if ($end && $durationInSeconds !== null) {
                    $instanceData['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSeconds)->toDateTimeString();
                }

                $createdEvents[] = SuKien::create($instanceData);
            }
            $suKien = $createdEvents[0];
        } else {
            $validated['quy_tac_lap'] = 'once';
            $validated['nhom_lap_id'] = null;
            $suKien = SuKien::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm sự kiện thành công.',
            'data' => [
                'id' => $suKien->id,
                'title' => $suKien->tieu_de,
                'start' => $suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $suKien->loai_su_kien),
                'color' => $suKien->mau_hien_thi ?? '#3B82F6',
                'location' => $suKien->mo_ta,
                'bat_thong_bao' => (bool) $suKien->bat_thong_bao,
                'so_ngay_nhac' => (int) ($suKien->so_ngay_nhac ?? 1),
                'quy_tac_lap' => $suKien->quy_tac_lap ?? 'once',
                'nhom_lap_id' => $suKien->nhom_lap_id,
            ],
        ], 201);
    }

    /**
     * Cập nhật sự kiện của user hiện tại (Anti-IDOR)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->findOrFail($id);

        $rules = [
            'tieu_de' => 'required|string|max:255',
            'loai_su_kien' => 'required|string',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'nullable|date',
            'mo_ta' => 'nullable|string',
            'mau_hien_thi' => 'nullable|string',
            'bat_thong_bao' => 'nullable|boolean',
            'so_ngay_nhac' => 'nullable|integer|in:1,2',
            'quy_tac_lap' => 'nullable|string|in:once,daily,weekly,monthly',
            'update_mode' => 'nullable|string|in:single,all',
        ];

        $effectiveStart = $request->filled('thoi_gian_bat_dau')
            ? $request->input('thoi_gian_bat_dau')
            : ($suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toDateTimeString() : null);

        if ($effectiveStart && $request->filled('thoi_gian_ket_thuc')) {
            $rules['thoi_gian_ket_thuc'] .= '|after_or_equal:' . $effectiveStart;
        }

        $validated = $request->validate($rules, [
            'thoi_gian_ket_thuc.after_or_equal' => 'The thời gian kết thúc field must be a date after or equal to thời gian bắt đầu.',
        ], [
            'thoi_gian_bat_dau' => 'thời gian bắt đầu',
            'thoi_gian_ket_thuc' => 'thời gian kết thúc',
        ]);

        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);

        $newQuyTacLap = $request->input('quy_tac_lap', $suKien->quy_tac_lap ?? 'once');
        $validated['quy_tac_lap'] = $newQuyTacLap;

        $updateMode = $request->input('update_mode', 'single');

        if ($updateMode === 'all' && !empty($suKien->nhom_lap_id)) {
            // Cập nhật thông tin dùng chung cho tất cả các buổi lặp trong nhóm
            SuKien::where('user_id', Auth::id())
                ->where('nhom_lap_id', $suKien->nhom_lap_id)
                ->update([
                    'tieu_de' => $validated['tieu_de'],
                    'loai_su_kien' => $validated['loai_su_kien'],
                    'mo_ta' => $validated['mo_ta'] ?? null,
                    'mau_hien_thi' => $validated['mau_hien_thi'] ?? '#3B82F6',
                    'bat_thong_bao' => $validated['bat_thong_bao'],
                    'so_ngay_nhac' => $validated['so_ngay_nhac'],
                    'quy_tac_lap' => $validated['quy_tac_lap'],
                ]);
            $suKien->refresh();
        } else {
            // Nếu đổi từ 'once' sang chuỗi lặp mới khi sửa 1 buổi đơn
            if ($suKien->quy_tac_lap === 'once' && in_array($newQuyTacLap, ['daily', 'weekly', 'monthly'])) {
                $nhomLapId = (string) Str::uuid();
                $validated['nhom_lap_id'] = $nhomLapId;

                $start = Carbon::parse($validated['thoi_gian_bat_dau']);
                $end = !empty($validated['thoi_gian_ket_thuc']) ? Carbon::parse($validated['thoi_gian_ket_thuc']) : null;
                $durationInSeconds = $end ? $start->diffInSeconds($end) : null;

                $count = match ($newQuyTacLap) {
                    'daily' => 30,
                    'weekly' => 12,
                    'monthly' => 6,
                    default => 1,
                };

                // Cập nhật sự kiện hiện tại thành buổi đầu
                $suKien->update($validated);

                // Tạo các buổi tương lai tiếp theo
                for ($i = 1; $i < $count; $i++) {
                    $instanceStart = match ($newQuyTacLap) {
                        'daily' => $start->copy()->addDays($i),
                        'weekly' => $start->copy()->addWeeks($i),
                        'monthly' => $start->copy()->addMonths($i),
                        default => $start->copy(),
                    };

                    $instanceData = $validated;
                    $instanceData['thoi_gian_bat_dau'] = $instanceStart->toDateTimeString();
                    if ($end && $durationInSeconds !== null) {
                        $instanceData['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSeconds)->toDateTimeString();
                    }

                    SuKien::create($instanceData);
                }
            } else {
                $suKien->update($validated);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sự kiện thành công.',
            'data' => [
                'id' => $suKien->id,
                'title' => $suKien->tieu_de,
                'start' => $suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $suKien->loai_su_kien),
                'color' => $suKien->mau_hien_thi ?? '#3B82F6',
                'location' => $suKien->mo_ta,
                'bat_thong_bao' => (bool) $suKien->bat_thong_bao,
                'so_ngay_nhac' => (int) ($suKien->so_ngay_nhac ?? 1),
                'quy_tac_lap' => $suKien->quy_tac_lap ?? 'once',
                'nhom_lap_id' => $suKien->nhom_lap_id,
            ],
        ]);
    }

    /**
     * Xóa sự kiện (Soft Delete) của user hiện tại (Anti-IDOR)
     */
    public function destroy(int $id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->findOrFail($id);

        $mode = request()->input('mode', request()->query('mode', 'single'));

        if ($mode === 'all' && !empty($suKien->nhom_lap_id)) {
            SuKien::where('user_id', Auth::id())
                ->where('nhom_lap_id', $suKien->nhom_lap_id)
                ->delete();
        } else {
            $suKien->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa sự kiện thành công.',
        ]);
    }
}
