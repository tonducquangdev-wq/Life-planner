<?php

namespace App\Console\Commands;

use App\Models\CongViec;
use App\Models\LichHoc;
use App\Models\SuKien;
use App\Models\ThongBao;
use App\Services\WebPushService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendPushRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:send-reminders {--window=30 : Số phút kiểm tra các sự kiện sắp diễn ra}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quét và gửi thông báo đẩy (Web Push) cho sự kiện lịch, lịch học, deadline và buổi tập sắp đến';

    /**
     * Execute the console command.
     */
    public function handle(WebPushService $webPushService): int
    {
        $windowMinutes = (int) $this->option('window');
        $now = now();
        $targetTime = $now->copy()->addMinutes($windowMinutes);

        $this->info("Bắt đầu quét nhắc nhở từ {$now->toDateTimeString()} đến {$targetTime->toDateTimeString()} (cửa sổ {$windowMinutes} phút)...");

        $sentCount = 0;

        // 1. NHẮC NHỞ SỰ KIỆN LỊCH (Bảng su_kien)
        // Lấy các sự kiện bật thông báo, chưa quá giờ bắt đầu quá 15 phút và trong vòng 7 ngày tới
        $suKiens = SuKien::with(['user', 'monHoc', 'congViec'])
            ->where('bat_thong_bao', true)
            ->where('thoi_gian_bat_dau', '>=', $now->copy()->subMinutes(15))
            ->where('thoi_gian_bat_dau', '<=', $now->copy()->addDays(7))
            ->get();

        foreach ($suKiens as $suKien) {
            $user = $suKien->user;
            if (! $user || ! $user->thong_bao_enabled) {
                continue;
            }

            // Kiểm tra tùy chọn loại thông báo của người dùng
            $shouldNotify = match ($suKien->loai_su_kien) {
                'hoc_tap' => (bool) $user->thong_bao_lich_hoc,
                'deadline' => (bool) $user->thong_bao_deadline,
                'cong_viec' => (bool) $user->thong_bao_cong_viec,
                'tap_luyen' => (bool) $user->thong_bao_tap_luyen,
                default => true,
            };

            if (! $shouldNotify) {
                continue;
            }

            // Tính thời gian cần nhắc (phút): nếu giá trị 1 thì hiểu là 1440 phút (1 ngày)
            $remindMinutes = ($suKien->so_ngay_nhac === 1) ? 1440 : (int) ($suKien->so_ngay_nhac ?? 15);
            $remindAt = $suKien->thoi_gian_bat_dau->copy()->subMinutes($remindMinutes);

            // Kiểm tra xem đã đến thời điểm cần nhắc chưa
            // Sự kiện chỉ được gửi nếu:
            // 1. Thời điểm hiện tại đã đến hoặc qua mốc nhắc ($now >= $remindAt)
            // 2. Sự kiện chưa bắt đầu quá 15 phút ($now <= $suKien->thoi_gian_bat_dau + 15 phút)
            if ($now->lt($remindAt) || $now->gt($suKien->thoi_gian_bat_dau->copy()->addMinutes(15))) {
                continue;
            }

            // Tránh gửi lặp lại trong vòng 6 giờ
            $cacheKey = "push_notif_sukien_{$suKien->id}_".$suKien->thoi_gian_bat_dau->format('YmdHi');
            if (Cache::has($cacheKey)) {
                continue;
            }

            $diffMinutes = (int) $now->diffInMinutes($suKien->thoi_gian_bat_dau, false);
            if ($diffMinutes <= 0) {
                $timeText = 'đang diễn ra ngay bây giờ';
            } elseif ($diffMinutes >= 1440) {
                $days = (int) round($diffMinutes / 1440);
                $timeText = "sau {$days} ngày nữa";
            } elseif ($diffMinutes >= 60) {
                $hours = (int) round($diffMinutes / 60);
                $timeText = "sau {$hours} giờ nữa";
            } else {
                $timeText = "sau {$diffMinutes} phút nữa";
            }

            $typeLabel = match ($suKien->loai_su_kien) {
                'hoc_tap' => 'Sắp đến giờ học',
                'deadline' => 'Sắp đến hạn chót (Deadline)',
                'cong_viec' => 'Công việc sắp đến hạn',
                'tap_luyen' => 'Sắp đến giờ tập luyện',
                default => 'Nhắc nhở sự kiện',
            };

            $title = "Life Planner — {$typeLabel}";
            $timeDisplay = $suKien->thoi_gian_bat_dau->format('H:i');
            $body = "Sự kiện \"{$suKien->tieu_de}\" sẽ diễn ra {$timeText} ({$timeDisplay}).";
            $url = route('calendar.index');

            // Ghi vào thông báo in-app
            ThongBao::create([
                'user_id' => $user->id,
                'tieu_de' => $title,
                'noi_dung' => $body,
                'loai_thong_bao' => match ($suKien->loai_su_kien) {
                    'hoc_tap' => 'lich_hoc',
                    'deadline' => 'deadline',
                    'cong_viec' => 'cong_viec',
                    'tap_luyen' => 'tap_luyen',
                    default => 'he_thong',
                },
                'da_doc' => false,
            ]);

            $result = $webPushService->sendToUser(
                $user,
                $title,
                $body,
                $url,
                asset('images/icons/icon-192x192.png'),
                "event-{$suKien->id}",
                ['event_id' => $suKien->id, 'type' => $suKien->loai_su_kien]
            );

            if ($result['sent'] > 0) {
                $sentCount += $result['sent'];
                $this->line("-> Đã gửi thông báo cho User #{$user->id}: {$title}");
            }

            Cache::put($cacheKey, true, now()->addHours(6));
        }

        // 2. NHẮC NHỞ CÔNG VIỆC CÓ DEADLINE (Bảng cong_viec)
        $congViecs = CongViec::with('user')
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [$now, $targetTime])
            ->get();

        foreach ($congViecs as $cv) {
            $user = $cv->user;
            if (! $user || ! $user->thong_bao_enabled || ! $user->thong_bao_cong_viec) {
                continue;
            }

            $cacheKey = "push_notif_congviec_{$cv->id}_".$cv->deadline->format('YmdHi');
            if (Cache::has($cacheKey)) {
                continue;
            }

            $minutesLeft = max(1, (int) $now->diffInMinutes($cv->deadline, false));
            $title = 'Life Planner — Công việc sắp đến hạn';
            $body = "Công việc \"{$cv->ten_cong_viec}\" sắp hết hạn trong {$minutesLeft} phút nữa (".$cv->deadline->format('H:i d/m').').';
            $url = route('cong-viec.index');

            ThongBao::create([
                'user_id' => $user->id,
                'tieu_de' => $title,
                'noi_dung' => $body,
                'loai_thong_bao' => 'cong_viec',
                'da_doc' => false,
            ]);

            $result = $webPushService->sendToUser(
                $user,
                $title,
                $body,
                $url,
                asset('images/icons/icon-192x192.png'),
                "task-{$cv->id}",
                ['task_id' => $cv->id]
            );

            if ($result['sent'] > 0) {
                $sentCount += $result['sent'];
            }

            Cache::put($cacheKey, true, now()->addHours(2));
        }

        // 3. NHẮC NHỞ LỊCH HỌC TRONG NGÀY (Bảng lich_hoc)
        $todayDayOfWeek = $now->dayOfWeekIso; // 1 (Thứ 2) đến 7 (Chủ nhật)
        $nowTimeStr = $now->format('H:i:s');
        $targetTimeStr = $targetTime->format('H:i:s');

        $lichHocs = LichHoc::with(['user', 'monHoc'])
            ->where('ngay_trong_tuan', $todayDayOfWeek)
            ->whereBetween('gio_bat_dau', [$nowTimeStr, $targetTimeStr])
            ->get();

        foreach ($lichHocs as $lh) {
            $user = $lh->user;
            if (! $user || ! $user->thong_bao_enabled || ! $user->thong_bao_lich_hoc) {
                continue;
            }

            $cacheKey = "push_notif_lichhoc_{$lh->id}_".$now->format('Ymd');
            if (Cache::has($cacheKey)) {
                continue;
            }

            $monHocName = $lh->monHoc?->ten_mon ?? 'Môn học';
            $title = 'Life Planner — Sắp đến giờ học';
            $body = "Bạn có lịch học môn \"{$monHocName}\" vào lúc ".substr($lh->gio_bat_dau, 0, 5).'.';
            $url = route('mon-hoc.index');

            ThongBao::create([
                'user_id' => $user->id,
                'tieu_de' => $title,
                'noi_dung' => $body,
                'loai_thong_bao' => 'lich_hoc',
                'da_doc' => false,
            ]);

            $result = $webPushService->sendToUser(
                $user,
                $title,
                $body,
                $url,
                asset('images/icons/icon-192x192.png'),
                "class-{$lh->id}",
                ['class_id' => $lh->id]
            );

            if ($result['sent'] > 0) {
                $sentCount += $result['sent'];
            }

            Cache::put($cacheKey, true, now()->addHours(4));
        }

        $this->info("Hoàn tất! Tổng cộng đã gửi {$sentCount} thông báo đẩy.");

        return Command::SUCCESS;
    }
}
