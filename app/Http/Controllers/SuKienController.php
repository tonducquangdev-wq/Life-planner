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
                'ngay_ket_thuc_lap' => $evt->ngay_ket_thuc_lap ? $evt->ngay_ket_thuc_lap->format('Y-m-d') : null,
                'nhom_lap_id' => $evt->nhom_lap_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Helper kiểm tra trùng lịch (Conflict Checker Engine - Strict Same Date & Deduplicated)
     * Quy tắc trùng: Cùng user + Cùng ngày phát sinh thực tế + (S1 < E2 AND E1 > S2)
     */
    protected function checkScheduleConflicts(array $validatedData, ?int $excludeId = null, ?string $excludeNhomLapId = null): array
    {
        $userId = Auth::id();
        $quyTacLap = $validatedData['quy_tac_lap'] ?? 'once';

        $start = Carbon::parse($validatedData['thoi_gian_bat_dau']);
        $end = !empty($validatedData['thoi_gian_ket_thuc'])
            ? Carbon::parse($validatedData['thoi_gian_ket_thuc'])
            : $start->copy()->addHour();

        $durationInSeconds = $start->diffInSeconds($end);
        if ($durationInSeconds <= 0) {
            $durationInSeconds = 3600;
        }

        $ngayKetThucLap = (!empty($validatedData['ngay_ket_thuc_lap']) && in_array($quyTacLap, ['daily', 'weekly', 'monthly']))
            ? Carbon::parse($validatedData['ngay_ket_thuc_lap'])->endOfDay()
            : null;

        $conflicts = [];
        $seenConflictIds = [];

        $i = 0;
        while (true) {
            $instanceStart = match ($quyTacLap) {
                'daily' => $start->copy()->addDays($i),
                'weekly' => $start->copy()->addWeeks($i),
                'monthly' => $start->copy()->addMonthsNoOverflow($i),
                default => $start->copy(),
            };

            if ($i > 0 && ($quyTacLap === 'once' || ($ngayKetThucLap && $instanceStart->gt($ngayKetThucLap)))) {
                break;
            }
            if ($i === 0 && $ngayKetThucLap && $instanceStart->gt($ngayKetThucLap)) {
                break;
            }

            $instanceEnd = $instanceStart->copy()->addSeconds($durationInSeconds);
            $targetDate = $instanceStart->toDateString(); // YYYY-MM-DD
            $newStartStr = $instanceStart->toDateTimeString();
            $newEndStr = $instanceEnd->toDateTimeString();

            // Query lấy sự kiện CÙNG NGÀY của user
            $existEvents = SuKien::where('user_id', $userId)
                ->whereNull('deleted_at')
                ->whereDate('thoi_gian_bat_dau', $targetDate) // BẮT BUỘC CÙNG NGÀY
                ->when($excludeId, function ($q) use ($excludeId) {
                    $q->where('id', '!=', $excludeId);
                })
                ->when($excludeNhomLapId, function ($q) use ($excludeNhomLapId) {
                    $q->where(function ($sub) use ($excludeNhomLapId) {
                        $sub->whereNull('nhom_lap_id')
                            ->orWhere('nhom_lap_id', '!=', $excludeNhomLapId);
                    });
                })
                ->get();

            foreach ($existEvents as $evt) {
                $evtStart = Carbon::parse($evt->thoi_gian_bat_dau);
                $evtEnd = $evt->thoi_gian_ket_thuc
                    ? Carbon::parse($evt->thoi_gian_ket_thuc)
                    : $evtStart->copy()->addHour();

                // Kiểm tra giao nhau: instanceStart < evtEnd AND instanceEnd > evtStart
                if ($instanceStart->lt($evtEnd) && $instanceEnd->gt($evtStart)) {
                    if (!isset($seenConflictIds[$evt->id])) {
                        $seenConflictIds[$evt->id] = true;

                        $conflicts[] = [
                            'date' => $evtStart->format('d/m/Y'),
                            'title' => $evt->tieu_de,
                            'tieu_de' => $evt->tieu_de,
                            'type' => str_replace('_', '-', $evt->loai_su_kien),
                            'time' => $evtStart->format('H:i') . ' - ' . $evtEnd->format('H:i'),
                        ];
                    }
                }
            }

            if ($quyTacLap === 'once') {
                break;
            }

            $i++;
            if ($i > 1000) {
                break;
            }
        }

        return $conflicts;
    }

    /**
     * Tạo mới sự kiện cho user hiện tại (Hỗ trợ lặp hằng ngày, hằng tuần, hằng tháng với ngày kết thúc lặp)
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
            'ngay_ket_thuc_lap' => 'nullable|date',
            'force' => 'nullable|boolean',
            'van_them' => 'nullable|boolean',
        ];

        $quyTacLap = $request->input('quy_tac_lap', 'once');
        if (in_array($quyTacLap, ['daily', 'weekly', 'monthly'])) {
            $rules['ngay_ket_thuc_lap'] = 'required|date';
        }

        if ($request->filled('thoi_gian_bat_dau') && $request->filled('thoi_gian_ket_thuc')) {
            $rules['thoi_gian_ket_thuc'] .= '|after_or_equal:thoi_gian_bat_dau';
        }

        if ($request->filled('thoi_gian_bat_dau') && $request->filled('ngay_ket_thuc_lap')) {
            $startDate = Carbon::parse($request->input('thoi_gian_bat_dau'))->toDateString();
            $rules['ngay_ket_thuc_lap'] .= '|after_or_equal:' . $startDate;
        }

        $validated = $request->validate($rules, [
            'ngay_ket_thuc_lap.required' => 'Vui lòng chọn ngày kết thúc lặp.',
            'ngay_ket_thuc_lap.after_or_equal' => 'Ngày kết thúc lặp không được nhỏ hơn ngày bắt đầu.',
            'thoi_gian_ket_thuc.after_or_equal' => 'Thời gian kết thúc không được nhỏ hơn thời gian bắt đầu.',
        ], [
            'thoi_gian_bat_dau' => 'thời gian bắt đầu',
            'thoi_gian_ket_thuc' => 'thời gian kết thúc',
            'ngay_ket_thuc_lap' => 'ngày kết thúc lặp',
        ]);

        // Kiểm tra trùng lịch nếu chưa chọn lưu đè (force / van_them)
        if (!$request->boolean('force') && !$request->boolean('van_them')) {
            $conflicts = $this->checkScheduleConflicts($validated);
            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'has_conflict' => true,
                    'message' => 'Phát hiện lịch bị trùng thời gian.',
                    'conflicts' => $conflicts,
                ], 409);
            }
        }

        $validated['user_id'] = Auth::id();
        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);
        $validated['quy_tac_lap'] = $quyTacLap;

        if (in_array($quyTacLap, ['daily', 'weekly', 'monthly'])) {
            $nhomLapId = (string) Str::uuid();
            $validated['nhom_lap_id'] = $nhomLapId;

            $start = Carbon::parse($validated['thoi_gian_bat_dau']);
            $end = !empty($validated['thoi_gian_ket_thuc']) ? Carbon::parse($validated['thoi_gian_ket_thuc']) : null;
            $durationInSeconds = $end ? $start->diffInSeconds($end) : null;
            $ngayKetThucLap = Carbon::parse($validated['ngay_ket_thuc_lap'])->endOfDay();

            $createdEvents = [];
            $i = 0;
            while (true) {
                $instanceStart = match ($quyTacLap) {
                    'daily' => $start->copy()->addDays($i),
                    'weekly' => $start->copy()->addWeeks($i),
                    'monthly' => $start->copy()->addMonthsNoOverflow($i),
                    default => $start->copy(),
                };

                if ($instanceStart->gt($ngayKetThucLap)) {
                    break;
                }

                $instanceData = $validated;
                unset($instanceData['force']);
                $instanceData['thoi_gian_bat_dau'] = $instanceStart->toDateTimeString();
                if ($end && $durationInSeconds !== null) {
                    $instanceData['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSeconds)->toDateTimeString();
                }

                $createdEvents[] = SuKien::create($instanceData);
                $i++;
                if ($i > 1000) {
                    break;
                }
            }
            $suKien = $createdEvents[0] ?? null;
        } else {
            $validated['quy_tac_lap'] = 'once';
            $validated['ngay_ket_thuc_lap'] = null;
            $validated['nhom_lap_id'] = null;
            unset($validated['force']);
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
                'ngay_ket_thuc_lap' => $suKien->ngay_ket_thuc_lap ? $suKien->ngay_ket_thuc_lap->format('Y-m-d') : null,
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
            'ngay_ket_thuc_lap' => 'nullable|date',
            'update_mode' => 'nullable|string|in:single,all',
            'force' => 'nullable|boolean',
            'van_them' => 'nullable|boolean',
        ];

        $newQuyTacLap = $request->input('quy_tac_lap', $suKien->quy_tac_lap ?? 'once');
        if (in_array($newQuyTacLap, ['daily', 'weekly', 'monthly'])) {
            $rules['ngay_ket_thuc_lap'] = 'required|date';
        }

        $effectiveStart = $request->filled('thoi_gian_bat_dau')
            ? $request->input('thoi_gian_bat_dau')
            : ($suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toDateTimeString() : null);

        if ($effectiveStart && $request->filled('thoi_gian_ket_thuc')) {
            $rules['thoi_gian_ket_thuc'] .= '|after_or_equal:' . $effectiveStart;
        }

        if ($effectiveStart && $request->filled('ngay_ket_thuc_lap')) {
            $startDate = Carbon::parse($effectiveStart)->toDateString();
            $rules['ngay_ket_thuc_lap'] .= '|after_or_equal:' . $startDate;
        }

        $validated = $request->validate($rules, [
            'ngay_ket_thuc_lap.required' => 'Vui lòng chọn ngày kết thúc lặp.',
            'ngay_ket_thuc_lap.after_or_equal' => 'Ngày kết thúc lặp không được nhỏ hơn ngày bắt đầu.',
            'thoi_gian_ket_thuc.after_or_equal' => 'Thời gian kết thúc không được nhỏ hơn thời gian bắt đầu.',
        ], [
            'thoi_gian_bat_dau' => 'thời gian bắt đầu',
            'thoi_gian_ket_thuc' => 'thời gian kết thúc',
            'ngay_ket_thuc_lap' => 'ngày kết thúc lặp',
        ]);

        $updateMode = $request->input('update_mode', 'single');

        // Kiểm tra trùng lịch nếu chưa chọn lưu đè (force / van_them)
        if (!$request->boolean('force') && !$request->boolean('van_them')) {
            $excludeId = ($updateMode === 'single') ? $suKien->id : null;
            $excludeGroup = ($updateMode === 'all') ? $suKien->nhom_lap_id : null;

            $conflicts = $this->checkScheduleConflicts($validated, $excludeId, $excludeGroup);
            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'has_conflict' => true,
                    'message' => 'Phát hiện lịch bị trùng thời gian.',
                    'conflicts' => $conflicts,
                ], 409);
            }
        }

        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);
        $validated['quy_tac_lap'] = $newQuyTacLap;
        if ($newQuyTacLap === 'once') {
            $validated['ngay_ket_thuc_lap'] = null;
        }
        unset($validated['force']);

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
                    'ngay_ket_thuc_lap' => $validated['ngay_ket_thuc_lap'] ?? null,
                ]);

            // Nếu có ngay_ket_thuc_lap mới, xử lý xóa các buổi lặp thừa hoặc sinh thêm buổi lặp thiếu
            if (!empty($validated['ngay_ket_thuc_lap'])) {
                $ngayKetThucLimit = Carbon::parse($validated['ngay_ket_thuc_lap'])->endOfDay();

                // Xóa các lần lặp phát sinh SAU ngày kết thúc mới
                SuKien::where('user_id', Auth::id())
                    ->where('nhom_lap_id', $suKien->nhom_lap_id)
                    ->where('thoi_gian_bat_dau', '>', $ngayKetThucLimit)
                    ->delete();

                // Sinh bổ sung các lần lặp còn thiếu đến ngày kết thúc mới
                $firstEvent = SuKien::where('user_id', Auth::id())
                    ->where('nhom_lap_id', $suKien->nhom_lap_id)
                    ->orderBy('thoi_gian_bat_dau', 'asc')
                    ->first();

                if ($firstEvent) {
                    $baseStart = Carbon::parse($firstEvent->thoi_gian_bat_dau);
                    $baseEnd = $firstEvent->thoi_gian_ket_thuc ? Carbon::parse($firstEvent->thoi_gian_ket_thuc) : null;
                    $durationInSec = $baseEnd ? $baseStart->diffInSeconds($baseEnd) : null;

                    $existingStarts = SuKien::where('user_id', Auth::id())
                        ->where('nhom_lap_id', $suKien->nhom_lap_id)
                        ->pluck('thoi_gian_bat_dau')
                        ->map(fn($dt) => Carbon::parse($dt)->toDateTimeString())
                        ->toArray();

                    $i = 0;
                    while (true) {
                        $instanceStart = match ($newQuyTacLap) {
                            'daily' => $baseStart->copy()->addDays($i),
                            'weekly' => $baseStart->copy()->addWeeks($i),
                            'monthly' => $baseStart->copy()->addMonthsNoOverflow($i),
                            default => $baseStart->copy(),
                        };

                        if ($instanceStart->gt($ngayKetThucLimit)) {
                            break;
                        }

                        $instanceStartStr = $instanceStart->toDateTimeString();
                        if (!in_array($instanceStartStr, $existingStarts)) {
                            $newInstance = [
                                'user_id' => Auth::id(),
                                'tieu_de' => $validated['tieu_de'],
                                'loai_su_kien' => $validated['loai_su_kien'],
                                'mo_ta' => $validated['mo_ta'] ?? null,
                                'mau_hien_thi' => $validated['mau_hien_thi'] ?? '#3B82F6',
                                'bat_thong_bao' => $validated['bat_thong_bao'],
                                'so_ngay_nhac' => $validated['so_ngay_nhac'],
                                'quy_tac_lap' => $validated['quy_tac_lap'],
                                'ngay_ket_thuc_lap' => $validated['ngay_ket_thuc_lap'],
                                'nhom_lap_id' => $suKien->nhom_lap_id,
                                'thoi_gian_bat_dau' => $instanceStartStr,
                            ];
                            if ($baseEnd && $durationInSec !== null) {
                                $newInstance['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSec)->toDateTimeString();
                            }
                            SuKien::create($newInstance);
                        }

                        $i++;
                        if ($i > 1000) {
                            break;
                        }
                    }
                }
            }

            $suKien->refresh();
        } else {
            // Nếu đổi từ 'once' sang chuỗi lặp mới khi sửa 1 buổi đơn
            if ($suKien->quy_tac_lap === 'once' && in_array($newQuyTacLap, ['daily', 'weekly', 'monthly']) && !empty($validated['ngay_ket_thuc_lap'])) {
                $nhomLapId = (string) Str::uuid();
                $validated['nhom_lap_id'] = $nhomLapId;

                $start = Carbon::parse($validated['thoi_gian_bat_dau']);
                $end = !empty($validated['thoi_gian_ket_thuc']) ? Carbon::parse($validated['thoi_gian_ket_thuc']) : null;
                $durationInSeconds = $end ? $start->diffInSeconds($end) : null;
                $ngayKetThucLap = Carbon::parse($validated['ngay_ket_thuc_lap'])->endOfDay();

                // Cập nhật sự kiện hiện tại thành buổi đầu
                $suKien->update($validated);

                // Tạo các buổi tương lai tiếp theo
                $i = 1;
                while (true) {
                    $instanceStart = match ($newQuyTacLap) {
                        'daily' => $start->copy()->addDays($i),
                        'weekly' => $start->copy()->addWeeks($i),
                        'monthly' => $start->copy()->addMonthsNoOverflow($i),
                        default => $start->copy(),
                    };

                    if ($instanceStart->gt($ngayKetThucLap)) {
                        break;
                    }

                    $instanceData = $validated;
                    $instanceData['thoi_gian_bat_dau'] = $instanceStart->toDateTimeString();
                    if ($end && $durationInSeconds !== null) {
                        $instanceData['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSeconds)->toDateTimeString();
                    }

                    SuKien::create($instanceData);
                    $i++;
                    if ($i > 1000) {
                        break;
                    }
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
                'ngay_ket_thuc_lap' => $suKien->ngay_ket_thuc_lap ? $suKien->ngay_ket_thuc_lap->format('Y-m-d') : null,
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
