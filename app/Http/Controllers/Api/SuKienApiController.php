<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SuKienApiController extends Controller
{
    /**
     * 1. GET /api/su-kien : Lấy danh sách sự kiện Calendar của Auth User
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->suKiens();

        // 1. Lọc theo start_date
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $query->where('thoi_gian_bat_dau', '>=', $startDate);
        }

        // 2. Lọc theo end_date
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $query->where('thoi_gian_bat_dau', '<=', $endDate);
        }

        // 3. Lọc theo tháng (month)
        if ($request->filled('month')) {
            $query->whereMonth('thoi_gian_bat_dau', (int) $request->input('month'));
        }

        // 4. Lọc theo năm (year)
        if ($request->filled('year')) {
            $query->whereYear('thoi_gian_bat_dau', (int) $request->input('year'));
        }

        // 5. Lọc theo loại sự kiện (loai_su_kien) - hỗ trợ cả hoc_tap và hoc-tap
        if ($request->filled('loai_su_kien')) {
            $type = str_replace('-', '_', trim($request->input('loai_su_kien')));
            $query->where('loai_su_kien', $type);
        }

        $danhSach = $query->orderBy('thoi_gian_bat_dau', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách sự kiện thành công!',
            'data'    => $danhSach,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 2. POST /api/su-kien : Thêm sự kiện Calendar mới (Hỗ trợ chuỗi sự kiện lặp)
     */
    public function store(Request $request): JsonResponse
    {
        $quyTacLap = $request->input('quy_tac_lap', 'once');

        $rules = [
            'tieu_de'           => 'required|string|max:255',
            'mo_ta'             => 'nullable|string',
            'loai_su_kien'      => 'nullable|string|in:hoc_tap,deadline,tap_luyen,ca_nhan,hoc-tap,tap-luyen,ca-nhan',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'required|date|after_or_equal:thoi_gian_bat_dau',
            'mau_hien_thi'      => 'nullable|string|max:20',
            'bat_thong_bao'     => 'nullable|boolean',
            'so_ngay_nhac'      => 'nullable|integer|min:1|max:30',
            'quy_tac_lap'       => 'nullable|string|in:once,daily,weekly,monthly',
            'ngay_ket_thuc_lap' => 'nullable|date',
            'cong_viec_id'      => 'nullable|integer|exists:cong_viec,id',
            'force'             => 'nullable|boolean',
            'van_them'          => 'nullable|boolean',
        ];

        if (in_array($quyTacLap, ['daily', 'weekly', 'monthly'])) {
            $rules['ngay_ket_thuc_lap'] = 'required|date';
            if ($request->filled('thoi_gian_bat_dau')) {
                $startDateStr = Carbon::parse($request->input('thoi_gian_bat_dau'))->toDateString();
                $rules['ngay_ket_thuc_lap'] .= '|after_or_equal:' . $startDateStr;
            }
        }

        $validated = $request->validate($rules);

        if (! $request->boolean('force') && ! $request->boolean('van_them')) {
            $conflicts = $this->checkScheduleConflicts($validated, $request->user()->id);
            if (! empty($conflicts)) {
                return response()->json([
                    'success'      => false,
                    'has_conflict' => true,
                    'message'      => 'Phát hiện lịch trình bị trùng.',
                    'conflicts'    => $conflicts,
                ], 409, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        $validated['user_id'] = $request->user()->id;
        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien'] ?? 'ca_nhan');
        $validated['mau_hien_thi'] = $validated['mau_hien_thi'] ?? '#3B82F6';
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao', false);
        $validated['so_ngay_nhac'] = $validated['so_ngay_nhac'] ?? 1;
        $validated['quy_tac_lap'] = $quyTacLap;

        unset($validated['force'], $validated['van_them']);

        if (in_array($quyTacLap, ['daily', 'weekly', 'monthly'])) {
            $nhomLapId = (string) Str::uuid();
            $validated['nhom_lap_id'] = $nhomLapId;

            $start = Carbon::parse($validated['thoi_gian_bat_dau']);
            $end = ! empty($validated['thoi_gian_ket_thuc'])
                ? Carbon::parse($validated['thoi_gian_ket_thuc'])
                : null;
            $durationInSeconds = $end ? $start->diffInSeconds($end) : null;
            $ngayKetThucLap = Carbon::parse($validated['ngay_ket_thuc_lap'])->endOfDay();

            $createdEvents = [];
            $i = 0;
            while (true) {
                $instanceStart = match ($quyTacLap) {
                    'daily'   => $start->copy()->addDays($i),
                    'weekly'  => $start->copy()->addWeeks($i),
                    'monthly' => $start->copy()->addMonthsNoOverflow($i),
                    default   => $start->copy(),
                };

                if ($instanceStart->gt($ngayKetThucLap)) {
                    break;
                }

                $instanceData = $validated;
                $instanceData['thoi_gian_bat_dau'] = $instanceStart->toDateTimeString();
                if ($end && $durationInSeconds !== null) {
                    $instanceData['thoi_gian_ket_thuc'] = $instanceStart->copy()->addSeconds($durationInSeconds)->toDateTimeString();
                }

                $createdEvents[] = SuKien::create($instanceData);
                $i++;
                if ($i >= 1000) {
                    break;
                }
            }

            $suKien = $createdEvents[0] ?? null;
            $soSuKienDaTao = count($createdEvents);
        } else {
            $validated['quy_tac_lap'] = 'once';
            $validated['ngay_ket_thuc_lap'] = null;
            $validated['nhom_lap_id'] = null;

            $suKien = SuKien::create($validated);
            $soSuKienDaTao = 1;
        }

        return response()->json([
            'success'           => true,
            'message'           => 'Tạo sự kiện thành công!',
            'so_su_kien_da_tao' => $soSuKienDaTao,
            'data'              => $suKien,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 3. GET /api/su-kien/{id} : Chi tiết sự kiện (Anti-IDOR)
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $suKien = $request->user()->suKiens()->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền truy cập.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết sự kiện.',
            'data'    => $suKien,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 4. PUT /api/su-kien/{id} : Cập nhật sự kiện (Anti-IDOR + Advanced Recurring Support)
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $suKien = $request->user()->suKiens()->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền cập nhật.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $rules = [
            'tieu_de'           => 'sometimes|required|string|max:255',
            'mo_ta'             => 'nullable|string',
            'loai_su_kien'      => 'nullable|string|in:hoc_tap,deadline,tap_luyen,ca_nhan,hoc-tap,tap-luyen,ca-nhan',
            'mau_hien_thi'      => 'nullable|string|max:20',
            'bat_thong_bao'     => 'nullable|boolean',
            'so_ngay_nhac'      => 'nullable|integer|min:1|max:30',
            'quy_tac_lap'       => 'nullable|string|in:once,daily,weekly,monthly',
            'ngay_ket_thuc_lap' => 'nullable|date',
            'cong_viec_id'      => 'nullable|integer|exists:cong_viec,id',
            'force'             => 'nullable|boolean',
            'van_them'          => 'nullable|boolean',
            'update_mode'       => 'nullable|string|in:single,all',
        ];

        $newQuyTacLap = $request->input('quy_tac_lap', $suKien->quy_tac_lap ?? 'once');
        if ($request->has('quy_tac_lap') && in_array($newQuyTacLap, ['daily', 'weekly', 'monthly'])) {
            if (empty($suKien->ngay_ket_thuc_lap) && ! $request->filled('ngay_ket_thuc_lap')) {
                $rules['ngay_ket_thuc_lap'] = 'required|date';
            }
        }

        $effectiveStart = $request->filled('thoi_gian_bat_dau')
            ? $request->input('thoi_gian_bat_dau')
            : ($suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toDateTimeString() : null);

        $hasStartTime = $request->has('thoi_gian_bat_dau');
        $hasEndTime   = $request->has('thoi_gian_ket_thuc');

        if ($hasStartTime && $hasEndTime) {
            $rules['thoi_gian_bat_dau'] = 'required|date';
            $rules['thoi_gian_ket_thuc'] = 'required|date|after_or_equal:thoi_gian_bat_dau';
        } elseif ($hasEndTime) {
            $rules['thoi_gian_ket_thuc'] = 'required|date' . ($effectiveStart ? '|after_or_equal:' . $effectiveStart : '');
        } elseif ($hasStartTime) {
            $rules['thoi_gian_bat_dau'] = 'required|date';
            if ($suKien->thoi_gian_ket_thuc) {
                $endTime = Carbon::parse($suKien->thoi_gian_ket_thuc)->toDateTimeString();
                $rules['thoi_gian_bat_dau'] .= '|before_or_equal:' . $endTime;
            }
        }

        if ($effectiveStart && $request->filled('ngay_ket_thuc_lap')) {
            $startDate = Carbon::parse($effectiveStart)->toDateString();
            $rules['ngay_ket_thuc_lap'] .= '|after_or_equal:' . $startDate;
        }

        $validated = $request->validate($rules);

        $updateMode = $request->input('update_mode', 'single');

        if (! $request->boolean('force') && ! $request->boolean('van_them')) {
            $verifyQuyTacLap = ($updateMode === 'all')
                ? $newQuyTacLap
                : (($suKien->quy_tac_lap === 'once' && in_array($newQuyTacLap, ['daily', 'weekly', 'monthly'])) ? $newQuyTacLap : 'once');

            $dataToVerify = array_merge([
                'thoi_gian_bat_dau'  => $suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toDateTimeString() : null,
                'thoi_gian_ket_thuc' => $suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toDateTimeString() : null,
                'quy_tac_lap'        => $verifyQuyTacLap,
                'ngay_ket_thuc_lap'  => $validated['ngay_ket_thuc_lap'] ?? ($suKien->ngay_ket_thuc_lap ? $suKien->ngay_ket_thuc_lap->format('Y-m-d') : null),
            ], $validated);
            $dataToVerify['quy_tac_lap'] = $verifyQuyTacLap;

            $excludeId = ($updateMode === 'single') ? $suKien->id : null;
            $excludeGroup = ($updateMode === 'all') ? $suKien->nhom_lap_id : null;

            $conflicts = $this->checkScheduleConflicts($dataToVerify, $request->user()->id, $excludeId, $excludeGroup);
            if (! empty($conflicts)) {
                return response()->json([
                    'success'      => false,
                    'has_conflict' => true,
                    'message'      => 'Phát hiện lịch trình bị trùng.',
                    'conflicts'    => $conflicts,
                ], 409, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        if (isset($validated['loai_su_kien'])) {
            $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        }
        if ($request->has('bat_thong_bao')) {
            $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        }
        $validated['quy_tac_lap'] = $newQuyTacLap;
        if ($newQuyTacLap === 'once') {
            $validated['ngay_ket_thuc_lap'] = null;
        }
        unset($validated['force'], $validated['van_them'], $validated['update_mode']);

        if ($updateMode === 'all' && ! empty($suKien->nhom_lap_id)) {
            $updateData = [];
            if (isset($validated['tieu_de'])) {
                $updateData['tieu_de'] = $validated['tieu_de'];
            }
            if (isset($validated['loai_su_kien'])) {
                $updateData['loai_su_kien'] = $validated['loai_su_kien'];
            }
            if (array_key_exists('mo_ta', $validated)) {
                $updateData['mo_ta'] = $validated['mo_ta'];
            }
            if (isset($validated['mau_hien_thi'])) {
                $updateData['mau_hien_thi'] = $validated['mau_hien_thi'];
            }
            if (isset($validated['bat_thong_bao'])) {
                $updateData['bat_thong_bao'] = $validated['bat_thong_bao'];
            }
            if (isset($validated['so_ngay_nhac'])) {
                $updateData['so_ngay_nhac'] = $validated['so_ngay_nhac'];
            }
            if (isset($validated['quy_tac_lap'])) {
                $updateData['quy_tac_lap'] = $validated['quy_tac_lap'];
            }
            if (array_key_exists('ngay_ket_thuc_lap', $validated)) {
                $updateData['ngay_ket_thuc_lap'] = $validated['ngay_ket_thuc_lap'];
            }

            if (! empty($updateData)) {
                SuKien::where('user_id', $request->user()->id)
                    ->where('nhom_lap_id', $suKien->nhom_lap_id)
                    ->update($updateData);
            }

            $effectiveNgayKetThucLap = $validated['ngay_ket_thuc_lap'] ?? ($suKien->ngay_ket_thuc_lap ? $suKien->ngay_ket_thuc_lap->format('Y-m-d') : null);

            if (! empty($effectiveNgayKetThucLap)) {
                $ngayKetThucLimit = Carbon::parse($effectiveNgayKetThucLap)->endOfDay();

                // 2. Nếu ngày kết thúc lặp bị rút ngắn: Soft Delete các sự kiện thoi_gian_bat_dau > ngay_ket_thuc_lap mới
                SuKien::where('user_id', $request->user()->id)
                    ->where('nhom_lap_id', $suKien->nhom_lap_id)
                    ->where('thoi_gian_bat_dau', '>', $ngayKetThucLimit)
                    ->delete();

                // 3. Nếu ngày kết thúc lặp được kéo dài: Tự động tạo thêm các bản ghi lặp còn thiếu (không tạo trùng)
                $firstEvent = SuKien::where('user_id', $request->user()->id)
                    ->where('nhom_lap_id', $suKien->nhom_lap_id)
                    ->orderBy('thoi_gian_bat_dau', 'asc')
                    ->first();

                if ($firstEvent) {
                    $baseStart = Carbon::parse($firstEvent->thoi_gian_bat_dau);
                    $baseEnd = $firstEvent->thoi_gian_ket_thuc ? Carbon::parse($firstEvent->thoi_gian_ket_thuc) : null;
                    $durationInSec = $baseEnd ? $baseStart->diffInSeconds($baseEnd) : null;

                    $existingStarts = SuKien::where('user_id', $request->user()->id)
                        ->where('nhom_lap_id', $suKien->nhom_lap_id)
                        ->pluck('thoi_gian_bat_dau')
                        ->map(fn($dt) => Carbon::parse($dt)->toDateTimeString())
                        ->toArray();

                    $i = 0;
                    while (true) {
                        $instanceStart = match ($newQuyTacLap) {
                            'daily'   => $baseStart->copy()->addDays($i),
                            'weekly'  => $baseStart->copy()->addWeeks($i),
                            'monthly' => $baseStart->copy()->addMonthsNoOverflow($i),
                            default   => $baseStart->copy(),
                        };

                        if ($instanceStart->gt($ngayKetThucLimit)) {
                            break;
                        }

                        $instanceStartStr = $instanceStart->toDateTimeString();
                        if (! in_array($instanceStartStr, $existingStarts)) {
                            $newInstance = [
                                'user_id'           => $request->user()->id,
                                'tieu_de'           => $firstEvent->tieu_de,
                                'loai_su_kien'      => $firstEvent->loai_su_kien,
                                'mo_ta'             => $firstEvent->mo_ta,
                                'mau_hien_thi'      => $firstEvent->mau_hien_thi ?? '#3B82F6',
                                'bat_thong_bao'     => $firstEvent->bat_thong_bao,
                                'so_ngay_nhac'      => $firstEvent->so_ngay_nhac,
                                'quy_tac_lap'       => $newQuyTacLap,
                                'ngay_ket_thuc_lap' => $effectiveNgayKetThucLap,
                                'nhom_lap_id'       => $suKien->nhom_lap_id,
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
            // CASE 1: update_mode = single
            // Nếu sự kiện hiện tại quy_tac_lap = once và request cập nhật thành daily/weekly/monthly có ngay_ket_thuc_lap:
            if (($suKien->quy_tac_lap === 'once' || empty($suKien->nhom_lap_id))
                && in_array($newQuyTacLap, ['daily', 'weekly', 'monthly'])
                && ! empty($validated['ngay_ket_thuc_lap'])
            ) {
                $nhomLapId = (string) Str::uuid();
                $validated['nhom_lap_id'] = $nhomLapId;

                $startStr = $validated['thoi_gian_bat_dau'] ?? ($suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toDateTimeString() : null);
                $start = Carbon::parse($startStr);
                $endStr = $validated['thoi_gian_ket_thuc'] ?? ($suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toDateTimeString() : null);
                $end = $endStr ? Carbon::parse($endStr) : null;
                $durationInSeconds = $end ? $start->diffInSeconds($end) : null;
                $ngayKetThucLap = Carbon::parse($validated['ngay_ket_thuc_lap'])->endOfDay();

                $suKien->update($validated);

                $i = 1;
                while (true) {
                    $instanceStart = match ($newQuyTacLap) {
                        'daily'   => $start->copy()->addDays($i),
                        'weekly'  => $start->copy()->addWeeks($i),
                        'monthly' => $start->copy()->addMonthsNoOverflow($i),
                        default   => $start->copy(),
                    };

                    if ($instanceStart->gt($ngayKetThucLap)) {
                        break;
                    }

                    $instanceData = array_merge($suKien->toArray(), $validated);
                    unset($instanceData['id'], $instanceData['created_at'], $instanceData['updated_at'], $instanceData['deleted_at']);
                    $instanceData['user_id'] = $request->user()->id;
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
            'message' => 'Cập nhật sự kiện thành công!',
            'data'    => $suKien->fresh(),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 5. DELETE /api/su-kien/{id} : Xóa sự kiện (Anti-IDOR + SoftDeletes + Advanced Mode Support)
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $suKien = $request->user()->suKiens()->find($id);

        if (! $suKien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sự kiện hoặc bạn không có quyền xóa.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $mode = $request->input('mode', $request->query('mode', 'single'));

        if ($mode === 'all' && ! empty($suKien->nhom_lap_id)) {
            SuKien::where('user_id', $request->user()->id)
                ->where('nhom_lap_id', $suKien->nhom_lap_id)
                ->delete();
        } else {
            $suKien->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa sự kiện thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Helper kiểm tra trùng lịch (Conflict Checker Engine - Strict Same Date & Deduplicated)
     */
    protected function checkScheduleConflicts(array $validatedData, int $userId, ?int $excludeId = null, ?string $excludeNhomLapId = null): array
    {
        $quyTacLap = $validatedData['quy_tac_lap'] ?? 'once';

        $start = Carbon::parse($validatedData['thoi_gian_bat_dau']);
        $end = ! empty($validatedData['thoi_gian_ket_thuc'])
            ? Carbon::parse($validatedData['thoi_gian_ket_thuc'])
            : $start->copy()->addHour();

        $durationInSeconds = $start->diffInSeconds($end);
        if ($durationInSeconds <= 0) {
            $durationInSeconds = 3600;
        }

        $ngayKetThucLap = (! empty($validatedData['ngay_ket_thuc_lap']) && in_array($quyTacLap, ['daily', 'weekly', 'monthly']))
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
            $targetDate = $instanceStart->toDateString();

            $existEvents = SuKien::where('user_id', $userId)
                ->whereNull('deleted_at')
                ->whereDate('thoi_gian_bat_dau', $targetDate)
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

                if ($instanceStart->lt($evtEnd) && $instanceEnd->gt($evtStart)) {
                    if (! isset($seenConflictIds[$evt->id])) {
                        $seenConflictIds[$evt->id] = true;

                        $conflicts[] = [
                            'date'    => $evtStart->format('d/m/Y'),
                            'title'   => $evt->tieu_de,
                            'tieu_de' => $evt->tieu_de,
                            'type'    => str_replace('_', '-', $evt->loai_su_kien),
                            'time'    => $evtStart->format('H:i') . ' - ' . $evtEnd->format('H:i'),
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
}
