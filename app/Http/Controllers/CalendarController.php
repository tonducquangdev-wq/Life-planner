<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\CongViec;
use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Hiển thị màn hình Lịch (Calendar First)
     */
    public function index()
    {
        $userId = (int) Auth::id();
        $currentMonthYear = date('d/m/Y');

        // 1. Lấy danh sách Deadline sắp tới thực tế từ CSDL
        $sapDenHan = $this->getUpcomingDeadlines($userId);

        // 2. Lịch trình hôm nay thực tế
        $lichTrinhHomNay = $this->getTodaySchedule($userId);

        // 3. Khởi tạo mảng tương thích cho view
        $calendarDays = [];
        $thongKeNhanh = [
            'mon_hoc_tuan_nay' => SuKien::where('user_id', $userId)
                ->whereIn('loai_su_kien', ['hoc_tap', 'hoc-tap'])
                ->whereBetween('thoi_gian_bat_dau', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'buoi_tap_tuan_nay' => SuKien::where('user_id', $userId)
                ->whereIn('loai_su_kien', ['tap_luyen', 'tap-luyen'])
                ->whereBetween('thoi_gian_bat_dau', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'deadline_chua_nop' => count($sapDenHan),
        ];

        return view('calendar', compact(
            'currentMonthYear',
            'calendarDays',
            'lichTrinhHomNay',
            'sapDenHan',
            'thongKeNhanh'
        ));
    }

    /**
     * API JSON lấy danh sách deadline sắp tới thực tế (phục vụ cập nhật real-time trên client)
     */
    public function deadlines(): JsonResponse
    {
        $userId = (int) Auth::id();
        $deadlines = $this->getUpcomingDeadlines($userId);

        return response()->json([
            'success' => true,
            'data' => $deadlines,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Lấy danh sách Deadline sắp tới từ CSDL thực tế (SuKien, BaiTap, CongViec)
     *
     * @return array<int, array{
     *     id: string,
     *     tieu_de: string,
     *     mon_hoc: string,
     *     con_lai: string,
     *     thoi_gian: string,
     *     badge_class: string
     * }>
     */
    public function getUpcomingDeadlines(int $userId): array
    {
        $now = Carbon::now();
        $thresholdPast = $now->copy()->subDays(7)->startOfDay();
        $deadlines = collect();

        // 1. Sự kiện loại deadline trong bảng su_kien
        $suKienDeadlines = SuKien::with(['monHoc', 'congViec.duAn'])
            ->where('user_id', $userId)
            ->whereIn('loai_su_kien', ['deadline', 'han_chot'])
            ->where('thoi_gian_bat_dau', '>=', $thresholdPast)
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        foreach ($suKienDeadlines as $sk) {
            $dueTime = $sk->thoi_gian_bat_dau;
            if (! $dueTime) {
                continue;
            }

            $monHocName = $sk->monHoc?->ten_mon
                ?? $sk->congViec?->duAn?->ten_du_an
                ?? (! empty($sk->mo_ta) ? $sk->mo_ta : 'Deadline');

            $deadlines->push([
                'id' => 'sk-'.$sk->id,
                'tieu_de' => $sk->tieu_de,
                'mon_hoc' => $monHocName,
                'due_time' => $dueTime,
            ]);
        }

        // 2. Bài tập môn học chưa hoàn thành (bảng bai_tap)
        $baiTapDeadlines = BaiTap::with('monHoc')
            ->whereHas('monHoc', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('trang_thai', '!=', 'da_hoan_thanh')
            ->whereNotNull('han_nop')
            ->where('han_nop', '>=', $thresholdPast)
            ->orderBy('han_nop', 'asc')
            ->get();

        foreach ($baiTapDeadlines as $bt) {
            $dueTime = Carbon::parse($bt->han_nop);
            $deadlines->push([
                'id' => 'bt-'.$bt->id,
                'tieu_de' => $bt->tieu_de,
                'mon_hoc' => $bt->monHoc?->ten_mon ?? 'Môn học',
                'due_time' => $dueTime,
            ]);
        }

        // 3. Công việc có deadline chưa hoàn thành (bảng cong_viec)
        $syncedCongViecIds = $suKienDeadlines->pluck('cong_viec_id')->filter()->toArray();

        $congViecDeadlines = CongViec::with('duAn')
            ->where('user_id', $userId)
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('deadline')
            ->where('deadline', '>=', $thresholdPast)
            ->whereNotIn('id', $syncedCongViecIds)
            ->orderBy('deadline', 'asc')
            ->get();

        foreach ($congViecDeadlines as $cv) {
            $dueTime = $cv->deadline;
            if (! $dueTime) {
                continue;
            }

            $deadlines->push([
                'id' => 'cv-'.$cv->id,
                'tieu_de' => $cv->ten_cong_viec,
                'mon_hoc' => $cv->duAn?->ten_du_an ?? 'Công việc',
                'due_time' => $dueTime,
            ]);
        }

        // Sắp xếp tăng dần theo thời hạn (Quá hạn -> Hôm nay -> Ngày mai -> Tương lai)
        return $deadlines->sortBy('due_time')->values()->take(10)->map(function ($item) use ($now) {
            /** @var Carbon $dueTime */
            $dueTime = $item['due_time'];

            if ($dueTime->lt($now) && ! $dueTime->isToday()) {
                $conLai = 'Quá hạn';
                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            } elseif ($dueTime->isToday()) {
                $hoursLeft = (int) $now->diffInHours($dueTime, false);
                $conLai = $hoursLeft > 0 ? "Còn {$hoursLeft}h" : 'Hôm nay';
                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            } elseif ($dueTime->isTomorrow()) {
                $conLai = 'Ngày mai';
                $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
            } else {
                $days = max(1, (int) ceil($now->floatDiffInDays($dueTime, false)));
                $conLai = "Còn {$days} ngày";
                $badgeClass = $days <= 3
                    ? 'bg-warning-subtle text-warning border border-warning-subtle'
                    : 'bg-primary-subtle text-primary border border-primary-subtle';
            }

            return [
                'id' => $item['id'],
                'tieu_de' => $item['tieu_de'],
                'mon_hoc' => $item['mon_hoc'],
                'con_lai' => $conLai,
                'thoi_gian' => $dueTime->format('H:i d/m'),
                'badge_class' => $badgeClass,
            ];
        })->all();
    }

    /**
     * Lấy danh sách lịch trình hôm nay từ CSDL
     *
     * @return array<int, array{
     *     thoi_gian: string,
     *     tieu_de: string,
     *     dia_diem: string,
     *     badge: string,
     *     badge_class: string
     * }>
     */
    protected function getTodaySchedule(int $userId): array
    {
        $today = Carbon::today();

        return SuKien::where('user_id', $userId)
            ->whereDate('thoi_gian_bat_dau', $today->toDateString())
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->take(6)
            ->get()
            ->map(function ($evt) {
                $start = $evt->thoi_gian_bat_dau ? $evt->thoi_gian_bat_dau->format('H:i') : '08:00';
                $end = $evt->thoi_gian_ket_thuc ? $evt->thoi_gian_ket_thuc->format('H:i') : null;
                $timeStr = $end ? "{$start} - {$end}" : $start;

                $type = str_replace('_', '-', $evt->loai_su_kien ?? '');

                return [
                    'thoi_gian' => $timeStr,
                    'tieu_de' => $evt->tieu_de,
                    'dia_diem' => $evt->mo_ta ?: 'Chưa có địa điểm',
                    'badge' => match ($type) {
                        'hoc-tap' => 'Học tập',
                        'tap-luyen' => 'Tập luyện',
                        'deadline' => 'Deadline',
                        'cong-viec' => 'Công việc',
                        default => 'Cá nhân',
                    },
                    'badge_class' => match ($type) {
                        'hoc-tap' => 'bg-primary-subtle text-primary border border-primary-subtle',
                        'tap-luyen' => 'bg-success-subtle text-success border border-success-subtle',
                        'deadline' => 'bg-danger-subtle text-danger border border-danger-subtle',
                        'cong-viec' => 'bg-warning-subtle text-warning border border-warning-subtle',
                        default => 'bg-purple-subtle text-purple border border-purple-subtle',
                    },
                ];
            })
            ->all();
    }
}
