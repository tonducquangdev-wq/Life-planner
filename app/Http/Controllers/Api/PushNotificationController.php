<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct(
        protected WebPushService $webPushService
    ) {}

    /**
     * Lấy VAPID Public Key cho client.
     */
    public function getVapidPublicKey(): JsonResponse
    {
        $publicKey = $this->webPushService->getPublicKey();

        if (! $publicKey) {
            return response()->json([
                'success' => false,
                'message' => 'Hệ thống Web Push chưa cấu hình VAPID keys.',
            ], 503, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'success' => true,
            'publicKey' => $publicKey,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Đăng ký thiết bị nhận thông báo đẩy.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'content_encoding' => 'nullable|string|max:32',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $deviceName = $validated['device_name'] ?? $request->header('User-Agent');
        if ($deviceName) {
            $deviceName = mb_substr(trim($deviceName), 0, 255);
        }

        $subscription = $this->webPushService->subscribe(
            $user,
            $validated,
            $deviceName
        );

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thiết bị nhận thông báo đẩy thành công!',
            'data' => [
                'id' => $subscription->id,
                'created_at' => $subscription->created_at,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Hủy đăng ký thiết bị.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = $request->user();
        $this->webPushService->unsubscribe($user, $validated['endpoint']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đăng ký nhận thông báo đẩy trên thiết bị này.',
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Kiểm tra trạng thái đăng ký của user hiện tại.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $user->pushSubscriptions()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'is_enabled' => (bool) $user->thong_bao_enabled,
                'device_count' => $count,
                'preferences' => [
                    'lich_hoc' => (bool) $user->thong_bao_lich_hoc,
                    'deadline' => (bool) $user->thong_bao_deadline,
                    'cong_viec' => (bool) $user->thong_bao_cong_viec,
                    'tap_luyen' => (bool) $user->thong_bao_tap_luyen,
                    'am_thanh' => (bool) $user->am_thanh_thong_bao,
                ],
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gửi thử nghiệm một thông báo đẩy đến thiết bị đang kết nối.
     */
    public function testPush(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng ký thiết bị nào để nhận thông báo đẩy. Vui lòng bấm Bật thông báo trên trình duyệt trước.',
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $type = $request->input('type', 'test');
        $userName = $user->ho_ten ?? 'bạn';

        [$title, $body, $loaiThongBao, $url] = match ($type) {
            'deadline' => [
                '⏰ Life Planner — Nhắc nhở Deadline',
                "Chào {$userName}! Bạn có hạn chót bài tập/công việc quan trọng cần hoàn thành hôm nay. Hãy kiểm tra ngay nhé!",
                'deadline',
                route('calendar.index'),
            ],
            'lich_hoc' => [
                '🎓 Life Planner — Nhắc nhở Lịch học',
                "Chào {$userName}! Lớp học theo thời khóa biểu của bạn sẽ bắt đầu sau 30 phút nữa. Chuẩn bị vào lớp nhé!",
                'lich_hoc',
                route('mon-hoc.index'),
            ],
            'cong_viec' => [
                '💼 Life Planner — Nhắc nhở Công việc & Dự án',
                "Chào {$userName}! Bạn có công việc quan trọng cần thực hiện trong hôm nay. Đừng quên kiểm tra và cập nhật tiến độ nhé!",
                'cong_viec',
                route('cong-viec.index'),
            ],
            'tap_luyen' => [
                '💪 Life Planner — Giờ rèn luyện thể chất',
                "Chào {$userName}! Đã đến giờ tập luyện theo kế hoạch rèn luyện. Khởi động và bắt đầu buổi tập hôm nay thôi nào!",
                'tap_luyen',
                route('tap-luyen.index'),
            ],
            default => [
                '🔔 Life Planner — Thông báo đẩy PWA',
                "Xin chào {$userName}! Hệ thống Web Push PWA đang hoạt động hoàn hảo trên thiết bị của bạn.",
                'he_thong',
                route('calendar.index'),
            ],
        };

        // Ghi vào bảng thong_bao của ứng dụng để đồng bộ lịch sử
        ThongBao::create([
            'user_id' => $user->id,
            'tieu_de' => $title,
            'noi_dung' => $body,
            'loai_thong_bao' => $loaiThongBao,
            'da_doc' => false,
        ]);

        $results = $this->webPushService->sendToUser(
            $user,
            $title,
            $body,
            $url,
            asset('images/icons/icon-192x192.png'),
            'test-notification-'.time(),
            ['type' => $type]
        );

        if ($results['sent'] === 0 && $results['failed'] > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể gửi thông báo đẩy đến thiết bị ({$results['failed']} lỗi kết nối với máy chủ thông báo). Vui lòng tắt và bấm bật lại thông báo.",
                'results' => $results,
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'success' => true,
            'message' => "Đã gửi thông báo đẩy thành công ({$results['sent']} thiết bị nhận được).",
            'title' => $title,
            'body' => $body,
            'results' => $results,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
