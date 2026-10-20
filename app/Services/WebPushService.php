<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    /**
     * @var array{subject: string, publicKey: string, privateKey: string}|null
     */
    protected ?array $auth = null;

    public function __construct()
    {
        $subject = config('webpush.vapid.subject');
        $publicKey = config('webpush.vapid.public_key');
        $privateKey = config('webpush.vapid.private_key');

        if ($subject && $publicKey && $privateKey) {
            $subject = trim($subject);
            // Apple APNs yêu cầu mailto: hoặc URL có domain công khai hợp lệ (từ chối tên miền .local)
            if (str_ends_with(strtolower($subject), '.local')) {
                $subject = 'mailto:quang@gmail.com';
            }
            if (! str_starts_with($subject, 'mailto:') && ! str_starts_with($subject, 'http://') && ! str_starts_with($subject, 'https://')) {
                $subject = 'mailto:'.$subject;
            }

            $this->auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => trim($publicKey),
                    'privateKey' => trim($privateKey),
                ],
            ];
        }
    }

    /**
     * Kiểm tra hệ thống WebPush đã được cấu hình đủ VAPID keys chưa.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->auth);
    }

    /**
     * Lấy VAPID Public Key an toàn để gửi cho trình duyệt client.
     */
    public function getPublicKey(): ?string
    {
        return config('webpush.vapid.public_key');
    }

    /**
     * Đăng ký hoặc cập nhật Push Subscription của thiết bị người dùng.
     *
     * @param array{
     *     endpoint: string,
     *     keys?: array{p256dh?: string, auth?: string},
     *     content_encoding?: string
     * } $subscriptionData
     */
    public function subscribe(User $user, array $subscriptionData, ?string $deviceName = null): PushSubscription
    {
        $endpoint = $subscriptionData['endpoint'];
        $endpointHash = PushSubscription::hashEndpoint($endpoint);
        $publicKey = $subscriptionData['keys']['p256dh'] ?? ($subscriptionData['public_key'] ?? '');
        $authToken = $subscriptionData['keys']['auth'] ?? ($subscriptionData['auth_token'] ?? '');
        $contentEncoding = $subscriptionData['content_encoding'] ?? 'aes128gcm';
        $deviceName = $deviceName ? mb_substr(trim($deviceName), 0, 255) : null;

        // Tự động bật thông báo tổng cho user khi họ đăng ký thiết bị mới
        if ($user->thong_bao_enabled === false) {
            $user->update(['thong_bao_enabled' => true]);
        }

        return PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'content_encoding' => $contentEncoding,
                'device_name' => $deviceName,
            ]
        );
    }

    /**
     * Hủy đăng ký một thiết bị.
     */
    public function unsubscribe(User $user, string $endpoint): bool
    {
        $endpointHash = PushSubscription::hashEndpoint($endpoint);

        return (bool) PushSubscription::where('user_id', $user->id)
            ->where('endpoint_hash', $endpointHash)
            ->delete();
    }

    /**
     * Gửi Web Push Notification cho toàn bộ thiết bị đã đăng ký của User.
     *
     * @param  array<string, mixed>  $extraData
     * @return array{sent: int, failed: int, deleted_expired: int}
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        ?string $url = null,
        ?string $icon = null,
        ?string $tag = null,
        array $extraData = []
    ): array {
        // Kiểm tra cài đặt thông báo tổng của User
        if ($user->thong_bao_enabled === false) {
            Log::info("WebPushService: User #{$user->id} đã tắt nhận thông báo.");

            return ['sent' => 0, 'failed' => 0, 'deleted_expired' => 0];
        }

        $subscriptions = $user->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return ['sent' => 0, 'failed' => 0, 'deleted_expired' => 0];
        }

        if (! $this->isConfigured()) {
            Log::warning('WebPushService: Chưa cấu hình VAPID keys.');

            return ['sent' => 0, 'failed' => 0, 'deleted_expired' => 0];
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => $icon ?? asset('images/icons/icon-192x192.png'),
            'badge' => asset('images/icons/icon-192x192.png'),
            'url' => $url ?? url('/calendar'),
            'tag' => $tag ?? 'life-planner-notification',
            'data' => array_merge($extraData, [
                'url' => $url ?? url('/calendar'),
                'timestamp' => now()->timestamp,
            ]),
        ], JSON_UNESCAPED_UNICODE);

        $clientOptions = array_merge([
            'timeout' => 8,
            'connect_timeout' => 5,
            'verify' => config('webpush.client_options.verify', false),
            'http_errors' => false,
        ], config('webpush.client_options', []));

        $httpClient = new GuzzleClient($clientOptions);
        $defaultOptions = config('webpush.default_options', []);
        $webPush = new WebPush($this->auth, $defaultOptions, $httpClient);
        $webPush->setReuseVAPIDHeaders(true);

        $sentCount = 0;
        $failedCount = 0;
        $expiredCount = 0;

        /** @var array<int, PushSubscription> $subModels */
        $subModels = [];

        foreach ($subscriptions as $sub) {
            try {
                $subscriptionObj = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                ]);

                $webPush->queueNotification($subscriptionObj, $payload);
                $subModels[] = $sub;
            } catch (\Throwable $e) {
                Log::error("WebPush queue failed for sub #{$sub->id}: ".$e->getMessage());
                $failedCount++;
            }
        }

        foreach ($webPush->flush() as $idx => $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            $currentSub = $subModels[$idx] ?? null;

            if ($report->isSuccess()) {
                $sentCount++;
            } else {
                $failedCount++;
                $reason = $report->getReason();
                $statusCode = $report->getResponse()?->getStatusCode();
                Log::warning("WebPush send failed for endpoint {$endpoint}: {$reason}".($statusCode ? " (HTTP {$statusCode})" : ''));

                // Nếu subscription đã hết hạn hoặc không còn tồn tại trên server của Google/Apple (404, 410)
                if ($report->isSubscriptionExpired() || in_array($statusCode, [404, 410], true)) {
                    if ($currentSub) {
                        $currentSub->delete();
                    } else {
                        PushSubscription::where('endpoint_hash', PushSubscription::hashEndpoint($endpoint))->delete();
                    }
                    $expiredCount++;
                    Log::info("WebPush: Đã xóa subscription hết hạn cho endpoint {$endpoint}.");
                }
            }
        }

        return [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'deleted_expired' => $expiredCount,
        ];
    }
}
