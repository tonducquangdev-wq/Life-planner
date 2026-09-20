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
     * Định hướng lịch tập tuần chuẩn (Schedule Orientation)
     * Đây là định hướng kế hoạch, KHÔNG PHẢI dữ liệu lịch sử đã tập.
     */
    private const DICH_HUONG_TUAN = [
        1 => ['thu' => 'Thứ 2', 'ten' => 'Push', 'mo_ta' => 'Ngực, Vai, Tay sau'],
        2 => ['thu' => 'Thứ 3', 'ten' => 'Pull', 'mo_ta' => 'Lưng xô, Tay trước'],
        3 => ['thu' => 'Thứ 4', 'ten' => 'Legs', 'mo_ta' => 'Chân, Mông, Bắp chân'],
        4 => ['thu' => 'Thứ 5', 'ten' => 'Rest', 'mo_ta' => 'Nghỉ ngơi phục hồi'],
        5 => ['thu' => 'Thứ 6', 'ten' => 'Arms + Shoulders', 'mo_ta' => 'Tay & Vai nâng cao'],
        6 => ['thu' => 'Thứ 7', 'ten' => 'Chest + Back', 'mo_ta' => 'Ngực & Lưng phối hợp'],
        0 => ['thu' => 'Chủ Nhật', 'ten' => 'Rest', 'mo_ta' => 'Nghỉ ngơi chuẩn bị tuần mới'],
    ];

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

            // Chỉ tính streak nếu ngày tập gần nhất là hôm nay hoặc hôm qua
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
            ->get();

        // 6. Xác định định hướng tập luyện cho hôm nay
        $dayOfWeek = Carbon::now()->dayOfWeek; // 0 = CN, 1 = T2, ..., 6 = T7
        $dinhHuongHomNay = self::DICH_HUONG_TUAN[$dayOfWeek] ?? self::DICH_HUONG_TUAN[1];

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

        // 8. Dữ liệu Heatmap cho tháng hiện tại từ DB thật (không dùng sample ảo)
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

        // 9. Dữ liệu các bài tập trong kế hoạch hiện tại (từ Database thật 100%)
        $firstPlan = $keHoachList->first();
        $currentBuoiTap = null;
        $danhSachBaiTap = [];
        $workoutPlansByType = [];

        if ($firstPlan && $firstPlan->buoiTaps->isNotEmpty()) {
            foreach ($firstPlan->buoiTaps as $bt) {
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
                $workoutPlansByType[mb_strtolower($bt->ten_buoi_tap)] = [
                    'id' => $bt->id,
                    'title' => $bt->ten_buoi_tap,
                    'exercises' => $exercises,
                ];
            }

            // Tìm buổi tập theo định hướng hoặc lấy buổi đầu tiên
            $currentBuoiTap = $firstPlan->buoiTaps->first(function ($bt) use ($dinhHuongHomNay) {
                return mb_strtolower($bt->ten_buoi_tap) === mb_strtolower($dinhHuongHomNay['ten']);
            }) ?? $firstPlan->buoiTaps->first();

            if ($currentBuoiTap && isset($workoutPlansByType[mb_strtolower($currentBuoiTap->ten_buoi_tap)])) {
                $danhSachBaiTap = $workoutPlansByType[mb_strtolower($currentBuoiTap->ten_buoi_tap)]['exercises'];
            }
        }

        return view('tap_luyen.index', [
            'buoiTapTuanNay' => $buoiTapTuanNay,
            'thoiGianTapHomNay' => $thoiGianTapHomNay,
            'tongBuoiTap' => $tongBuoiTap,
            'chuoiNgayTap' => $chuoiNgayTap,
            'keHoachList' => $keHoachList,
            'currentBuoiTap' => $currentBuoiTap,
            'danhSachBaiTap' => $danhSachBaiTap,
            'workoutPlansByType' => $workoutPlansByType,
            'dinhHuongTuan' => self::DICH_HUONG_TUAN,
            'dinhHuongHomNay' => $dinhHuongHomNay,
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

        // Đảm bảo buoi_tap_id hợp lệ với foreign key constraint
        if ($buoiTapId) {
            $exists = BuoiTap::where('id', $buoiTapId)->exists();
            if (!$exists) {
                $buoiTapId = null;
            }
        }

        if (!$buoiTapId) {
            // Tìm buổi tập bất kỳ của user
            $userBuoiTap = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->first();

            if ($userBuoiTap) {
                $buoiTapId = $userBuoiTap->id;
            } else {
                // Tạo kế hoạch và buổi tập mặc định cho user nếu chưa từng tạo
                $keHoach = KeHoachTapLuyen::firstOrCreate(
                    ['user_id' => $userId, 'ten_ke_hoach' => 'Kế hoạch cá nhân'],
                    ['mo_ta' => 'Kế hoạch tập luyện được tạo tự động khi hoàn thành buổi tập']
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

        // Cập nhật lại số liệu thống kê mới
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
     * Dữ liệu được lưu thật vào Database (chi_tiet_buoi_tap & bai_tap_the_chat)
     */
    public function capNhatBuoiTap(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'buoi_tap_id' => 'nullable|integer',
            'ten_buoi_tap' => 'required|string|max:255',
            'exercises' => 'present|array',
            'exercises.*.name' => 'required|string|max:255',
            'exercises.*.type' => 'required|string|in:strength,core,cardio,other',
            'exercises.*.sets' => 'nullable|integer|min:1|max:50',
            'exercises.*.reps' => 'nullable|string|max:50',
            'exercises.*.duration' => 'nullable|integer|min:1|max:1440',
            'exercises.*.duration_unit' => 'nullable|string|in:phut,giay',
        ]);

        $exercisesInput = $request->input('exercises', []);

        // Validation nghiệp vụ cụ thể cho từng loại bài tập (Section 7)
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
        $keHoach = KeHoachTapLuyen::firstOrCreate(
            ['user_id' => $userId, 'ten_ke_hoach' => 'Kế hoạch cá nhân'],
            ['mo_ta' => 'Kế hoạch tập luyện được quản lý tự động']
        );

        // 2. Tìm hoặc khởi tạo buổi tập
        $buoiTapId = $request->input('buoi_tap_id');
        $tenBuoiTap = trim($request->input('ten_buoi_tap'));

        $buoiTap = null;
        if ($buoiTapId) {
            $buoiTap = BuoiTap::where('id', $buoiTapId)
                ->where('ke_hoach_tap_luyen_id', $keHoach->id)
                ->first();
        }

        if (!$buoiTap) {
            $buoiTap = BuoiTap::firstOrCreate(
                ['ke_hoach_tap_luyen_id' => $keHoach->id, 'ten_buoi_tap' => $tenBuoiTap],
                ['mo_ta' => "Buổi tập {$tenBuoiTap}", 'thu_tu' => 1]
            );
        } else {
            $buoiTap->update(['ten_buoi_tap' => $tenBuoiTap]);
        }

        // 3. Thực hiện đồng bộ chi tiết bài tập trong Transaction
        DB::transaction(function () use ($buoiTap, $exercisesInput) {
            // Xóa chi tiết bài tập cũ để lưu danh sách mới nhất
            ChiTietBuoiTap::where('buoi_tap_id', $buoiTap->id)->delete();

            foreach ($exercisesInput as $idx => $item) {
                $order = $idx + 1; // Re-index thứ tự tăng dần liên tục: 1, 2, 3...
                $type = mb_strtolower($item['type'] ?? 'strength');
                $name = trim($item['name']);

                $nhomCo = match ($type) {
                    'cardio' => 'Cardio',
                    'core' => 'Core',
                    default => 'Toàn thân',
                };

                // Master record bài tập thể chất
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
                'exercises' => $resultList,
            ],
        ]);
    }
}
