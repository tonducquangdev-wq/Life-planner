<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BuoiTap;
use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardApiController extends Controller
{
    /**
     * GET /api/dashboard
     * Action-First Personal Assistant Engine Endpoint
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->id;
        $now = Carbon::now();

        // 1. Time-of-day Dynamic Greeting
        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 12) {
            $greetingText = "Good Morning";
        } elseif ($hour >= 12 && $hour < 18) {
            $greetingText = "Good Afternoon";
        } else {
            $greetingText = "Good Evening";
        }

        $greeting = [
            'text'     => $greetingText . ', ' . ($user->ho_ten ?? $user->name ?? 'User'),
            'date_str' => $now->format('l, F j'),
        ];

        // 2. Today's Events (Scope Today)
        $todayStart = $now->copy()->startOfDay();
        $todayEnd   = $now->copy()->endOfDay();

        $todayEvents = SuKien::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->whereBetween('thoi_gian_bat_dau', [$todayStart, $todayEnd])
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        $totalCount     = $todayEvents->count();
        $completedCount = $todayEvents->where('trang_thai', 'da_hoan_thanh')->count();
        $remainingCount = $totalCount - $completedCount;

        // 3. 🔥 NEXT ACTION / HERO EVENT ENGINE
        // Find ongoing event first
        $nowLiveEvent = SuKien::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->where('thoi_gian_bat_dau', '<=', $now)
            ->where('thoi_gian_ket_thuc', '>=', $now)
            ->first();

        $nextAction = null;

        if ($nowLiveEvent) {
            $end = Carbon::parse($nowLiveEvent->thoi_gian_ket_thuc);
            $remainingMinutes = max(1, (int) $now->diffInMinutes($end));

            $nextAction = [
                'status_type'       => 'now_live',
                'status_badge'      => '🔴 Đang diễn ra',
                'id'                => $nowLiveEvent->id,
                'tieu_de'           => $nowLiveEvent->tieu_de,
                'thoi_gian_bat_dau' => Carbon::parse($nowLiveEvent->thoi_gian_bat_dau)->format('H:i A'),
                'thoi_gian_ket_thuc'=> Carbon::parse($nowLiveEvent->thoi_gian_ket_thuc)->format('H:i A'),
                'time_range'        => Carbon::parse($nowLiveEvent->thoi_gian_bat_dau)->format('H:i A') . ' - ' . Carbon::parse($nowLiveEvent->thoi_gian_ket_thuc)->format('H:i A'),
                'countdown_text'    => "Còn {$remainingMinutes} phút",
            ];
        } else {
            // Find next upcoming event today or future
            $upcomingEvent = SuKien::where('user_id', $userId)
                ->whereNull('deleted_at')
                ->where('thoi_gian_bat_dau', '>', $now)
                ->orderBy('thoi_gian_bat_dau', 'asc')
                ->first();

            if ($upcomingEvent) {
                $start = Carbon::parse($upcomingEvent->thoi_gian_bat_dau);
                $diffMinutes = (int) $now->diffInMinutes($start);
                if ($diffMinutes >= 60) {
                    $hours = floor($diffMinutes / 60);
                    $countdownText = "Còn {$hours} tiếng nữa";
                } else {
                    $countdownText = "Còn {$diffMinutes} phút nữa";
                }

                $nextAction = [
                    'status_type'       => 'next_up',
                    'status_badge'      => '🔥 Tiếp theo',
                    'id'                => $upcomingEvent->id,
                    'tieu_de'           => $upcomingEvent->tieu_de,
                    'thoi_gian_bat_dau' => $start->format('H:i A'),
                    'thoi_gian_ket_thuc'=> $upcomingEvent->thoi_gian_ket_thuc ? Carbon::parse($upcomingEvent->thoi_gian_ket_thuc)->format('H:i A') : null,
                    'time_range'        => $start->format('H:i A') . ($upcomingEvent->thoi_gian_ket_thuc ? ' - ' . Carbon::parse($upcomingEvent->thoi_gian_ket_thuc)->format('H:i A') : ''),
                    'countdown_text'    => $countdownText,
                ];
            } else {
                $nextAction = [
                    'status_type'    => 'empty',
                    'status_badge'   => '🎉 Thư giãn',
                    'tieu_de'        => 'Hôm nay không có sự kiện nào',
                    'countdown_text' => 'Tận hưởng thời gian nghỉ ngơi!',
                ];
            }
        }

        // 4. ⚠️ URGENT DEADLINE (Conditional - Single most urgent deadline in 72h)
        $urgentDeadlineModel = BaiTap::whereHas('monHoc', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('trang_thai', '!=', 'da_hoan_thanh')
            ->where('han_nop', '>=', $now)
            ->where('han_nop', '<=', $now->copy()->addDays(3))
            ->orderBy('han_nop', 'asc')
            ->first();

        $urgentDeadline = null;
        if ($urgentDeadlineModel) {
            $hanNop = Carbon::parse($urgentDeadlineModel->han_nop);
            $daysLeft = ceil($now->diffInDays($hanNop, false));
            $urgentDeadline = [
                'id'            => $urgentDeadlineModel->id,
                'tieu_de'       => $urgentDeadlineModel->tieu_de,
                'remaining_text'=> $daysLeft > 0 ? "Còn {$daysLeft} ngày" : "Còn hôm nay",
                'priority'      => 'Ưu tiên cao',
            ];
        }

        // 5. 🏋 TODAY WORKOUT SUMMARY
        $activeWorkout = BuoiTap::whereHas('keHoachTapLuyen', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('is_active', true);
        })->first();

        $todayWorkout = null;
        if ($activeWorkout) {
            $todayWorkout = [
                'id'               => $activeWorkout->id,
                'ten_buoi_tap'     => $activeWorkout->ten_buoi_tap ?? 'Push Day',
                'exercise_count'   => 5,
                'duration_minutes' => 60,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy dữ liệu Action-First Dashboard thành công!',
            'data'    => [
                'greeting'        => $greeting,
                'next_action'     => $nextAction,
                'urgent_deadline' => $urgentDeadline,
                'today_events'    => [
                    'total_count'     => $totalCount,
                    'completed_count' => $completedCount,
                    'remaining_count' => $remainingCount,
                    'items'           => $todayEvents,
                ],
                'today_workout'   => $todayWorkout,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
