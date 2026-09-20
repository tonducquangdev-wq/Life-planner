<?php

namespace App\Http\Controllers;

use App\Models\BaiTapTheChat;
use App\Models\BuoiTap;
use App\Models\ChiTietBuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TapLuyenController extends Controller
{
    /**
     * Tên các ngày trong tuần chuẩn ISO-8601 (1 = Thứ 2 ... 7 = Chủ Nhật)
     */
    public const MAP_THU = [
        1 => ['thu' => 'Thứ 2', 'short' => 'T2'],
        2 => ['thu' => 'Thứ 3', 'short' => 'T3'],
        3 => ['thu' => 'Thứ 4', 'short' => 'T4'],
        4 => ['thu' => 'Thứ 5', 'short' => 'T5'],
        5 => ['thu' => 'Thứ 6', 'short' => 'T6'],
        6 => ['thu' => 'Thứ 7', 'short' => 'T7'],
        7 => ['thu' => 'Chủ Nhật', 'short' => 'CN'],
    ];

    /**
     * Xây dựng dữ liệu lịch 7 ngày trong tuần từ Database kế hoạch tập luyện
     * Ngày không có buổi tập gán tự động được xem là Ngày Nghỉ (Rest Day)
     */
    public function buildLichTuan(?KeHoachTapLuyen $activePlan): array
    {
        $buoiTapsByDay = [];
        if ($activePlan && $activePlan->buoiTaps) {
            foreach ($activePlan->buoiTaps as $bt) {
                if ($bt->ngay_trong_tuan && $bt->ngay_trong_tuan >= 1 && $bt->ngay_trong_tuan <= 7) {
                    $buoiTapsByDay[$bt->ngay_trong_tuan][] = $bt;
                }
            }
        }

        $lichTuan = [];
        for ($day = 1; $day <= 7; $day++) {
            $sessions = $buoiTapsByDay[$day] ?? [];
            if (!empty($sessions)) {
                $bt = $sessions[0];
                $soBai = ($bt->chiTietBuoiTaps) ? $bt->chiTietBuoiTaps->count() : 0;
                $lichTuan[$day] = [
                    'ngay_iso' => $day,
                    'thu' => self::MAP_THU[$day]['thu'],
                    'short' => self::MAP_THU[$day]['short'],
                    'is_rest' => false,
                    'buoi_tap_id' => $bt->id,
                    'ten' => $bt->ten_buoi_tap,
                    'mo_ta' => $bt->mo_ta ?: ($soBai > 0 ? "{$soBai} bài tập" : 'Buổi rèn luyện'),
                    'so_bai' => $soBai,
                ];
            } else {
                $lichTuan[$day] = [
                    'ngay_iso' => $day,
                    'thu' => self::MAP_THU[$day]['thu'],
                    'short' => self::MAP_THU[$day]['short'],
                    'is_rest' => true,
                    'buoi_tap_id' => null,
                    'ten' => 'Nghỉ ngơi',
                    'mo_ta' => 'Nghỉ ngơi phục hồi cơ bắp',
                    'so_bai' => 0,
                ];
            }
        }

        return $lichTuan;
    }

    /**
     * Hiển thị trang theo dõi thể chất và tập luyện (Dữ liệu thật 100% từ Database)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Thống kê số buổi tập tuần này từ database thật (0 nếu chưa có)
        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        // 2. Tổng thời gian tập hôm nay (tính bằng phút từ DB thật)
        $thoiGianTapHomNay = (int) LichSuTapLuyen::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->sum('tong_thoi_luong');

        // 3. Tổng số buổi tập đã hoàn thành tích lũy
        $tongBuoiTap = LichSuTapLuyen::where('user_id', $userId)->count();

        // 4. Tính chuỗi ngày rèn luyện liên tục (Streak) dựa trên ngày tập thực tế
        $dates = LichSuTapLuyen::where('user_id', $userId)
            ->orderByDesc('thoi_gian_bat_dau')
            ->pluck('thoi_gian_bat_dau')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $chuoiNgayTap = 0;
        if ($dates->isNotEmpty()) {
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
            $firstDate = $dates->first();

            if ($firstDate === $today || $firstDate === $yesterday) {
                $currentCheck = Carbon::parse($firstDate);
                foreach ($dates as $dateStr) {
                    if ($dateStr === $currentCheck->toDateString()) {
                        $chuoiNgayTap++;
                        $currentCheck->subDay();
                    } else {
                        break;
                    }
                }
            }
        }

        // 5. Lấy danh sách kế hoạch tập luyện của người dùng từ DB
        $keHoachList = KeHoachTapLuyen::where('user_id', $userId)
            ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        // Xác định kế hoạch hoạt động (Active Plan)
        $activePlan = $keHoachList->firstWhere('is_active', true);
        if (!$activePlan && $keHoachList->isNotEmpty()) {
            $activePlan = $keHoachList->first();
            $activePlan->update(['is_active' => true]);
        }

        // Nếu người dùng chưa có kế hoạch nào, tạo mặc định 1 kế hoạch cá nhân kèm lịch mẫu
        if (!$activePlan) {
            $activePlan = KeHoachTapLuyen::create([
                'user_id' => $userId,
                'ten_ke_hoach' => 'Kế hoạch cá nhân (PPL)',
                'mo_ta' => 'Lịch tập phân bổ theo nhóm cơ: Push - Pull - Legs',
                'is_active' => true,
            ]);

            $bPush = BuoiTap::create([
                'ke_hoach_tap_luyen_id' => $activePlan->id,
                'ten_buoi_tap' => 'Push',
                'mo_ta' => 'Ngực, Vai, Tay sau',
                'thu_tu' => 1,
                'ngay_trong_tuan' => 1, // Thứ 2
            ]);

            $bPull = BuoiTap::create([
                'ke_hoach_tap_luyen_id' => $activePlan->id,
                'ten_buoi_tap' => 'Pull',
                'mo_ta' => 'Lưng xô, Tay trước',
                'thu_tu' => 2,
                'ngay_trong_tuan' => 2, // Thứ 3
            ]);

            $bLegs = BuoiTap::create([
                'ke_hoach_tap_luyen_id' => $activePlan->id,
                'ten_buoi_tap' => 'Legs',
                'mo_ta' => 'Chân, Mông, Bắp chân',
                'thu_tu' => 3,
                'ngay_trong_tuan' => 3, // Thứ 4
            ]);

            BuoiTap::create([
                'ke_hoach_tap_luyen_id' => $activePlan->id,
                'ten_buoi_tap' => 'Cardio & Core',
                'mo_ta' => 'Tim mạch và cơ trọng tâm',
                'thu_tu' => 4,
                'ngay_trong_tuan' => 5, // Thứ 6
            ]);

            $bench = BaiTapTheChat::firstOrCreate(['ten_bai_tap' => 'Barbell Bench Press'], ['nhom_co' => 'Ngực', 'loai_bai_tap' => 'strength']);
            $incline = BaiTapTheChat::firstOrCreate(['ten_bai_tap' => 'Incline Dumbbell Press'], ['nhom_co' => 'Ngực', 'loai_bai_tap' => 'strength']);
            $tricep = BaiTapTheChat::firstOrCreate(['ten_bai_tap' => 'Triceps Pushdown'], ['nhom_co' => 'Tay sau', 'loai_bai_tap' => 'strength']);
            ChiTietBuoiTap::create(['buoi_tap_id' => $bPush->id, 'bai_tap_the_chat_id' => $bench->id, 'thu_tu' => 1, 'loai_bai_tap' => 'strength', 'so_sets' => 4, 'so_reps' => '8-10']);
            ChiTietBuoiTap::create(['buoi_tap_id' => $bPush->id, 'bai_tap_the_chat_id' => $incline->id, 'thu_tu' => 2, 'loai_bai_tap' => 'strength', 'so_sets' => 3, 'so_reps' => '10-12']);
            ChiTietBuoiTap::create(['buoi_tap_id' => $bPush->id, 'bai_tap_the_chat_id' => $tricep->id, 'thu_tu' => 3, 'loai_bai_tap' => 'strength', 'so_sets' => 3, 'so_reps' => '12-15']);

            $keHoachList = KeHoachTapLuyen::where('user_id', $userId)
                ->with(['buoiTaps.chiTietBuoiTaps.baiTapTheChat'])
                ->get();
            $activePlan = $keHoachList->first();
        }

        // 6. Xây dựng lịch tuần động từ Database (100% data-driven)
        $lichTuan = $this->buildLichTuan($activePlan);
        $todayIso = Carbon::now()->dayOfWeekIso; // 1 = Thứ 2 ... 7 = Chủ Nhật
        $dinhHuongHomNay = $lichTuan[$todayIso] ?? $lichTuan[1];

        // 7. Lịch sử tập luyện thực tế (Hoạt động gần đây)
        $lichSuRecords = LichSuTapLuyen::where('user_id', $userId)
            ->with(['buoiTap.chiTietBuoiTaps'])
            ->orderByDesc('thoi_gian_bat_dau')
            ->take(8)
            ->get();

        $hoatDongGanDay = $lichSuRecords->map(function ($lichSu) {
            $buoiTap = $lichSu->buoiTap;
            $tenBuoiTap = $buoiTap ? $buoiTap->ten_buoi_tap : ($lichSu->ghi_chu ?: 'Tập tự do');
            $soBai = ($buoiTap && $buoiTap->chiTietBuoiTaps) ? $buoiTap->chiTietBuoiTaps->count() : 0;

            $startedAt = Carbon::parse($lichSu->thoi_gian_bat_dau);
            if ($startedAt->isToday()) {
                $thoiDiem = 'Hôm nay ' . $startedAt->format('H:i');
            } elseif ($startedAt->isYesterday()) {
                $thoiDiem = 'Hôm qua ' . $startedAt->format('H:i');
            } else {
                $thoiDiem = $startedAt->diffForHumans();
            }

            $color = 'primary';
            $icon = 'bi-activity';
            $loaiLower = mb_strtolower($tenBuoiTap);
            if (str_contains($loaiLower, 'push')) {
                $color = 'danger';
                $icon = 'bi-fire';
            } elseif (str_contains($loaiLower, 'pull')) {
                $color = 'primary';
                $icon = 'bi-lightning-charge-fill';
            } elseif (str_contains($loaiLower, 'leg')) {
                $color = 'success';
                $icon = 'bi-layers-fill';
            } elseif (str_contains($loaiLower, 'cardio')) {
                $color = 'info';
                $icon = 'bi-heart-pulse-fill';
            }

            return [
                'id' => $lichSu->id,
                'loai' => $tenBuoiTap,
                'ten' => $tenBuoiTap,
                'thoi_gian' => ($lichSu->tong_thoi_luong ?: 0) . ' phút',
                'so_bai' => $soBai > 0 ? ($soBai . ' bài tập') : 'Buổi tập',
                'thoi_diem' => $thoiDiem,
                'icon' => $icon,
                'color' => $color,
            ];
        });

        // 8. Dữ liệu Heatmap cho tháng hiện tại từ DB thật
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $startOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth();

        $monthlyHeatmap = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->thoi_gian_bat_dau)->format('Y-m-d'))
            ->map(fn($items) => [
                'count' => $items->count(),
                'duration' => (int) $items->sum('tong_thoi_luong'),
            ]);

        // 9. Dữ liệu các buổi tập và bài tập trong Kế hoạch đang chọn
        $currentBuoiTap = null;
        $danhSachBaiTap = [];
        $buoiTapList = [];
        $workoutPlansByType = [];

        if ($activePlan && $activePlan->buoiTaps->isNotEmpty()) {
            foreach ($activePlan->buoiTaps as $bt) {
                $exercises = [];
                $sortedChiTiet = $bt->chiTietBuoiTaps ? $bt->chiTietBuoiTaps->sortBy('thu_tu') : collect();
                foreach ($sortedChiTiet as $ct) {
                    if ($ct->baiTapTheChat) {
                        $exercises[] = [
                            'id' => $ct->id,
                            'name' => $ct->baiTapTheChat->ten_bai_tap,
                            'type' => $ct->loai_bai_tap ?: ($ct->baiTapTheChat->loai_bai_tap ?: 'strength'),
                            'sets' => $ct->so_sets,
                            'reps' => $ct->so_reps,
                            'duration' => $ct->thoi_luong,
                            'duration_unit' => $ct->don_vi_thoi_gian ?: 'phut',
                            'order' => $ct->thu_tu,
                            'group' => $ct->baiTapTheChat->nhom_co,
                            'metric_display' => $ct->dinh_dang_thong_so,
                        ];
                    }
                }

                $sessionData = [
                    'id' => $bt->id,
                    'title' => $bt->ten_buoi_tap,
                    'mo_ta' => $bt->mo_ta,
                    'thu_tu' => $bt->thu_tu,
                    'ngay_trong_tuan' => $bt->ngay_trong_tuan,
                    'ten_thu' => $bt->ten_thu,
                    'exercises' => $exercises,
                ];

                $buoiTapList[] = $sessionData;
                $workoutPlansByType[mb_strtolower($bt->ten_buoi_tap)] = $sessionData;
            }

            // Chọn buổi tập theo lịch hôm nay nếu có, hoặc buổi đầu tiên
            if ($dinhHuongHomNay['buoi_tap_id']) {
                $currentBuoiTap = $activePlan->buoiTaps->firstWhere('id', $dinhHuongHomNay['buoi_tap_id']);
            }

            if (!$currentBuoiTap) {
                $currentBuoiTap = $activePlan->buoiTaps->first();
            }

            if ($currentBuoiTap) {
                $matchedSession = collect($buoiTapList)->firstWhere('id', $currentBuoiTap->id);
                if ($matchedSession) {
                    $danhSachBaiTap = $matchedSession['exercises'];
                }
            }
        }

        return view('tap_luyen.index', [
            'buoiTapTuanNay' => $buoiTapTuanNay,
            'thoiGianTapHomNay' => $thoiGianTapHomNay,
            'tongBuoiTap' => $tongBuoiTap,
            'chuoiNgayTap' => $chuoiNgayTap,
            'activePlan' => $activePlan,
            'keHoachList' => $keHoachList,
            'currentBuoiTap' => $currentBuoiTap,
            'danhSachBaiTap' => $danhSachBaiTap,
            'buoiTapList' => $buoiTapList,
            'workoutPlansByType' => $workoutPlansByType,
            'lichTuan' => $lichTuan,
            'dinhHuongHomNay' => $dinhHuongHomNay,
            'todayIso' => $todayIso,
            'hoatDongGanDay' => $hoatDongGanDay,
            'monthlyHeatmap' => $monthlyHeatmap,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Ghi nhận hoàn thành buổi tập thực tế vào cơ sở dữ liệu
     */
    public function hoanThanh(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'buoi_tap_id' => 'nullable|integer',
            'ten_buoi_tap' => 'nullable|string|max:255',
            'seconds' => 'nullable|integer|min:0',
            'tong_thoi_luong' => 'nullable|integer|min:0',
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $seconds = $request->input('seconds', 0);
        $minutes = $request->input('tong_thoi_luong');
        if ($minutes === null || $minutes <= 0) {
            $minutes = max(1, (int) round($seconds / 60));
        }

        $buoiTapId = $request->input('buoi_tap_id');

        if ($buoiTapId) {
            $exists = BuoiTap::where('id', $buoiTapId)->exists();
            if (!$exists) {
                $buoiTapId = null;
            }
        }

        if (!$buoiTapId) {
            $userBuoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->first();

            if ($userBuoiTap) {
                $buoiTapId = $userBuoiTap->id;
            } else {
                $keHoach = KeHoachTapLuyen::firstOrCreate(
                    ['user_id' => $userId, 'ten_ke_hoach' => 'Kế hoạch cá nhân'],
                    ['mo_ta' => 'Kế hoạch tập luyện được tạo tự động khi hoàn thành buổi tập', 'is_active' => true]
                );

                $tenBuoiTap = $request->input('ten_buoi_tap') ?: 'Tập tự do';
                $buoiTap = BuoiTap::firstOrCreate(
                    ['ke_hoach_tap_luyen_id' => $keHoach->id, 'ten_buoi_tap' => $tenBuoiTap],
                    ['mo_ta' => 'Buổi tập được ghi nhận tự động', 'thu_tu' => 1]
                );

                $buoiTapId = $buoiTap->id;
            }
        }

        $now = Carbon::now();
        $startTime = $now->copy()->subMinutes($minutes);

        $lichSu = LichSuTapLuyen::create([
            'user_id' => $userId,
            'buoi_tap_id' => $buoiTapId,
            'thoi_gian_bat_dau' => $startTime,
            'thoi_gian_ket_thuc' => $now,
            'tong_thoi_luong' => $minutes,
            'ghi_chu' => $request->input('ghi_chu') ?: ($request->input('ten_buoi_tap') ?: 'Hoàn thành buổi tập'),
        ]);

        $buoiTapTuanNay = LichSuTapLuyen::where('user_id', $userId)
            ->whereBetween('thoi_gian_bat_dau', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $thoiGianTapHomNay = (int) LichSuTapLuyen::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', Carbon::today())
            ->sum('tong_thoi_luong');

        $tongBuoiTap = LichSuTapLuyen::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận buổi tập vào lịch sử thành công!',
            'data' => [
                'id' => $lichSu->id,
                'ten' => $request->input('ten_buoi_tap') ?: 'Buổi tập',
                'thoi_gian' => $minutes . ' phút',
                'thoi_diem' => 'Hôm nay ' . $now->format('H:i'),
                'buoiTapTuanNay' => $buoiTapTuanNay,
                'thoiGianTapHomNay' => $thoiGianTapHomNay,
                'tongBuoiTap' => $tongBuoiTap,
            ],
        ]);
    }

    /**
     * Tùy chỉnh và lưu cấu hình bài tập cho Buổi tập (Hỗ trợ Strength, Core, Cardio, Other)
     */
    public function capNhatBuoiTap(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'buoi_tap_id' => 'nullable|integer',
            'ten_buoi_tap' => 'required|string|max:255',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
            'exercises' => 'present|array',
            'exercises.*.name' => 'required|string|max:255',
            'exercises.*.type' => 'required|string|in:strength,core,cardio,other',
            'exercises.*.sets' => 'nullable|integer|min:1|max:50',
            'exercises.*.reps' => 'nullable|string|max:50',
            'exercises.*.duration' => 'nullable|integer|min:1|max:1440',
            'exercises.*.duration_unit' => 'nullable|string|in:phut,giay',
        ]);

        $exercisesInput = $request->input('exercises', []);

        foreach ($exercisesInput as $idx => $ex) {
            $type = mb_strtolower($ex['type'] ?? 'strength');
            $pos = $idx + 1;
            $name = trim($ex['name'] ?? "Bài tập #{$pos}");

            if ($type === 'strength') {
                if (empty($ex['sets']) || empty($ex['reps'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bài tập #{$pos} \"{$name}\" (Strength) bắt buộc nhập số Sets và Reps.",
                    ], 422);
                }
            } elseif ($type === 'cardio') {
                if (empty($ex['duration'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bài tập #{$pos} \"{$name}\" (Cardio) bắt buộc nhập Thời lượng.",
                    ], 422);
                }
            } elseif ($type === 'core' || $type === 'other') {
                $hasReps = !empty($ex['sets']) && !empty($ex['reps']);
                $hasDuration = !empty($ex['duration']);
                if (!$hasReps && !$hasDuration) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bài tập #{$pos} \"{$name}\" phải chọn ít nhất Sets/Reps hoặc Thời lượng.",
                    ], 422);
                }
            }
        }

        // 1. Tìm hoặc khởi tạo kế hoạch tập luyện của User
        $keHoach = KeHoachTapLuyen::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$keHoach) {
            $keHoach = KeHoachTapLuyen::firstOrCreate(
                ['user_id' => $userId, 'ten_ke_hoach' => 'Kế hoạch cá nhân'],
                ['mo_ta' => 'Kế hoạch tập luyện được quản lý tự động', 'is_active' => true]
            );
        }

        // 2. Tìm hoặc khởi tạo buổi tập
        $buoiTapId = $request->input('buoi_tap_id');
        $tenBuoiTap = trim($request->input('ten_buoi_tap'));
        $ngayTrongTuan = $request->input('ngay_trong_tuan');

        $buoiTap = null;
        if ($buoiTapId) {
            $buoiTap = BuoiTap::where('id', $buoiTapId)
                ->where('ke_hoach_tap_luyen_id', $keHoach->id)
                ->first();
        }

        if (!$buoiTap) {
            $count = BuoiTap::where('ke_hoach_tap_luyen_id', $keHoach->id)->count();
            $buoiTap = BuoiTap::create([
                'ke_hoach_tap_luyen_id' => $keHoach->id,
                'ten_buoi_tap' => $tenBuoiTap,
                'mo_ta' => "Buổi tập {$tenBuoiTap}",
                'thu_tu' => $count + 1,
                'ngay_trong_tuan' => $ngayTrongTuan,
            ]);
        } else {
            $updateData = ['ten_buoi_tap' => $tenBuoiTap];
            if ($request->has('ngay_trong_tuan')) {
                $updateData['ngay_trong_tuan'] = $ngayTrongTuan;
            }
            $buoiTap->update($updateData);
        }

        // 3. Thực hiện đồng bộ chi tiết bài tập trong Transaction
        DB::transaction(function () use ($buoiTap, $exercisesInput) {
            ChiTietBuoiTap::where('buoi_tap_id', $buoiTap->id)->delete();

            foreach ($exercisesInput as $idx => $item) {
                $order = $idx + 1;
                $type = mb_strtolower($item['type'] ?? 'strength');
                $name = trim($item['name']);

                $nhomCo = match ($type) {
                    'cardio' => 'Cardio',
                    'core' => 'Core',
                    default => 'Toàn thân',
                };

                $baiTap = BaiTapTheChat::firstOrCreate(
                    ['ten_bai_tap' => $name],
                    [
                        'nhom_co' => $nhomCo,
                        'loai_bai_tap' => $type,
                        'mo_ta' => "Bài tập {$type}"
                    ]
                );

                if (empty($baiTap->loai_bai_tap)) {
                    $baiTap->update(['loai_bai_tap' => $type]);
                }

                ChiTietBuoiTap::create([
                    'buoi_tap_id' => $buoiTap->id,
                    'bai_tap_the_chat_id' => $baiTap->id,
                    'thu_tu' => $order,
                    'loai_bai_tap' => $type,
                    'so_sets' => !empty($item['sets']) ? (int)$item['sets'] : null,
                    'so_reps' => !empty($item['reps']) ? trim($item['reps']) : null,
                    'thoi_luong' => !empty($item['duration']) ? (int)$item['duration'] : null,
                    'don_vi_thoi_gian' => !empty($item['duration_unit']) ? trim($item['duration_unit']) : 'phut',
                ]);
            }
        });

        // 4. Lấy danh sách kết quả đã được sắp xếp chuẩn để trả về client
        $buoiTap->load(['chiTietBuoiTaps.baiTapTheChat']);
        $resultList = [];

        foreach ($buoiTap->chiTietBuoiTaps->sortBy('thu_tu') as $ct) {
            if ($ct->baiTapTheChat) {
                $resultList[] = [
                    'id' => $ct->id,
                    'name' => $ct->baiTapTheChat->ten_bai_tap,
                    'type' => $ct->loai_bai_tap ?: ($ct->baiTapTheChat->loai_bai_tap ?: 'strength'),
                    'sets' => $ct->so_sets,
                    'reps' => $ct->so_reps,
                    'duration' => $ct->thoi_luong,
                    'duration_unit' => $ct->don_vi_thoi_gian ?: 'phut',
                    'order' => $ct->thu_tu,
                    'group' => $ct->baiTapTheChat->nhom_co,
                    'metric_display' => $ct->dinh_dang_thong_so,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật {$buoiTap->ten_buoi_tap} với " . count($resultList) . " bài tập thành công!",
            'data' => [
                'buoi_tap_id' => $buoiTap->id,
                'ten_buoi_tap' => $buoiTap->ten_buoi_tap,
                'ngay_trong_tuan' => $buoiTap->ngay_trong_tuan,
                'ten_thu' => $buoiTap->ten_thu,
                'exercises' => $resultList,
            ],
        ]);
    }

    /**
     * Tạo Kế hoạch tập luyện mới
     */
    public function taoKeHoach(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'ten_ke_hoach' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active', true);

        if ($isActive) {
            KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        }

        $keHoach = KeHoachTapLuyen::create([
            'user_id' => $userId,
            'ten_ke_hoach' => $request->input('ten_ke_hoach'),
            'mo_ta' => $request->input('mo_ta'),
            'is_active' => $isActive,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo kế hoạch \"{$keHoach->ten_ke_hoach}\" thành công!",
            'data' => $keHoach,
        ]);
    }

    /**
     * Kích hoạt Kế hoạch tập luyện được chọn
     */
    public function kichHoatKeHoach(Request $request, $id)
    {
        $userId = Auth::id();

        $keHoach = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        KeHoachTapLuyen::where('user_id', $userId)->update(['is_active' => false]);
        $keHoach->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Đã kích hoạt kế hoạch \"{$keHoach->ten_ke_hoach}\"!",
            'data' => $keHoach,
        ]);
    }

    /**
     * Xóa Kế hoạch tập luyện
     */
    public function xoaKeHoach(Request $request, $id)
    {
        $userId = Auth::id();

        $keHoach = KeHoachTapLuyen::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $wasActive = $keHoach->is_active;
        $keHoach->delete();

        if ($wasActive) {
            $nextPlan = KeHoachTapLuyen::where('user_id', $userId)->first();
            if ($nextPlan) {
                $nextPlan->update(['is_active' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa kế hoạch thành công!',
        ]);
    }

    /**
     * Thêm Buổi tập mới vào kế hoạch (Custom Session)
     */
    public function themBuoiTap(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'ke_hoach_tap_luyen_id' => 'required|integer',
            'ten_buoi_tap' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
        ]);

        $keHoach = KeHoachTapLuyen::where('id', $request->input('ke_hoach_tap_luyen_id'))
            ->where('user_id', $userId)
            ->firstOrFail();

        $count = BuoiTap::where('ke_hoach_tap_luyen_id', $keHoach->id)->count();

        $buoiTap = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $keHoach->id,
            'ten_buoi_tap' => $request->input('ten_buoi_tap'),
            'mo_ta' => $request->input('mo_ta'),
            'thu_tu' => $count + 1,
            'ngay_trong_tuan' => $request->input('ngay_trong_tuan'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã thêm buổi tập \"{$buoiTap->ten_buoi_tap}\" thành công!",
            'data' => [
                'id' => $buoiTap->id,
                'ten_buoi_tap' => $buoiTap->ten_buoi_tap,
                'mo_ta' => $buoiTap->mo_ta,
                'ngay_trong_tuan' => $buoiTap->ngay_trong_tuan,
                'ten_thu' => $buoiTap->ten_thu,
            ],
        ]);
    }

    /**
     * Sửa Buổi tập (Tên, mô tả, ngày trong tuần)
     */
    public function suaBuoiTap(Request $request, $id)
    {
        $userId = Auth::id();

        $request->validate([
            'ten_buoi_tap' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:1000',
            'ngay_trong_tuan' => 'nullable|integer|between:1,7',
        ]);

        $buoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->firstOrFail();

        $buoiTap->update([
            'ten_buoi_tap' => $request->input('ten_buoi_tap'),
            'mo_ta' => $request->input('mo_ta'),
            'ngay_trong_tuan' => $request->input('ngay_trong_tuan'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật buổi tập \"{$buoiTap->ten_buoi_tap}\" thành công!",
            'data' => [
                'id' => $buoiTap->id,
                'ten_buoi_tap' => $buoiTap->ten_buoi_tap,
                'mo_ta' => $buoiTap->mo_ta,
                'ngay_trong_tuan' => $buoiTap->ngay_trong_tuan,
                'ten_thu' => $buoiTap->ten_thu,
            ],
        ]);
    }

    /**
     * Xóa Buổi tập
     */
    public function xoaBuoiTap(Request $request, $id)
    {
        $userId = Auth::id();

        $buoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', $id)->firstOrFail();

        $ten = $buoiTap->ten_buoi_tap;
        $buoiTap->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa buổi tập \"{$ten}\"!",
        ]);
    }
}
