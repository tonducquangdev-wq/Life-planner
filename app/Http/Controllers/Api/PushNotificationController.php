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

        $title = 'Life Planner — Thông báo thử nghiệm';
        $body = 'Xin chào '.($user->ho_ten ?? 'bạn').'! Hệ thống Web Push trên PWA Life Planner đang hoạt động chính xác.';
        $url = route('calendar.index');

        // Ghi vào bảng thong_bao của ứng dụng để đồng bộ
        ThongBao::create([
            'user_id' => $user->id,
            'tieu_de' => $title,
            'noi_dung' => $body,
            'loai_thong_bao' => 'he_thong',
            'da_doc' => false,
        ]);

        $results = $this->webPushService->sendToUser(
            $user,
            $title,
            $body,
            $url,
            asset('images/icons/icon-192x192.png'),
            'test-notification',
            ['type' => 'test']
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
            'message' => "Đã gửi thông báo đẩy: thành công {$results['sent']}, thất bại {$results['failed']}.",
            'results' => $results,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
