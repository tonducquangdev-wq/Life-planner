<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PwaAndPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Kiểm tra file Web App Manifest tồn tại và hợp lệ theo chuẩn PWA W3C.
     */
    public function test_manifest_file_exists_and_has_valid_json_and_required_keys(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath, 'File manifest.json phải tồn tại trong thư mục public/');

        $content = File::get($manifestPath);
        $data = json_decode($content, true);

        $this->assertIsArray($data, 'Nội dung manifest.json phải là JSON hợp lệ.');
        $this->assertEquals('Life Planner', $data['name'] ?? null);
        $this->assertEquals('Life Planner', $data['short_name'] ?? null);
        $this->assertEquals('/', $data['start_url'] ?? null);
        $this->assertEquals('standalone', $data['display'] ?? null);
        $this->assertNotEmpty($data['theme_color'] ?? null);
        $this->assertNotEmpty($data['background_color'] ?? null);
        $this->assertNotEmpty($data['icons'] ?? []);

        // Kiểm tra tất cả icon được khai báo trong manifest đều thực sự tồn tại
        foreach ($data['icons'] as $icon) {
            $src = ltrim($icon['src'], '/');
            $this->assertFileExists(public_path($src), "Icon {$src} được khai báo trong manifest phải tồn tại trên ổ đĩa.");
        }
    }

    /**
     * Kiểm tra Service Worker file sw.js tồn tại và cài đặt đầy đủ các handler cốt lõi.
     */
    public function test_service_worker_file_exists_and_contains_lifecycle_and_push_handlers(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath, 'File sw.js phải tồn tại trong thư mục public/');

        $content = File::get($swPath);
        $this->assertStringContainsString("addEventListener('install'", $content);
        $this->assertStringContainsString("addEventListener('activate'", $content);
        $this->assertStringContainsString("addEventListener('fetch'", $content);
        $this->assertStringContainsString("addEventListener('push'", $content);
        $this->assertStringContainsString("addEventListener('notificationclick'", $content);
    }

    /**
     * Kiểm tra trang ngoại tuyến (Offline fallback) phản hồi 200 OK và chứa nút kết nối lại.
     */
    public function test_offline_page_is_accessible_and_has_retry_action(): void
    {
        $response = $this->get('/offline');
        $response->assertOk();
        $response->assertSee('Life Planner');
        $response->assertSee('offline');

        $staticOfflinePath = public_path('offline.html');
        $this->assertFileExists($staticOfflinePath, 'public/offline.html phải tồn tại để Service Worker nạp ngoại tuyến độc lập.');
    }

    /**
     * Kiểm tra các trang chính của ứng dụng chứa thẻ meta manifest và script PWA.
     */
    public function test_authenticated_view_includes_pwa_head_and_manager(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('calendar.index'));
        $response->assertOk();
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('manifest.json');
        $response->assertSee('pwa-manager.js');
        $response->assertSee('apple-touch-icon');
    }

    /**
     * Endpoint lấy VAPID public key phải được bảo vệ bởi xác thực người dùng.
     */
    public function test_vapid_public_key_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/push/vapid-key');
        $response->assertUnauthorized();
    }

    /**
     * Người dùng đã đăng nhập có thể lấy VAPID public key.
     */
    public function test_authenticated_user_can_retrieve_vapid_public_key(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/push/vapid-key');
        $response->assertOk();
        $response->assertJsonStructure(['publicKey']);
        $this->assertNotEmpty($response->json('publicKey'));
    }

    /**
     * Người dùng có thể đăng ký nhận Push Subscription và lưu vào cơ sở dữ liệu.
     */
    public function test_authenticated_user_can_subscribe_to_push_notifications(): void
    {
        $user = User::factory()->create();

        $payload = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-token-'.uniqid(),
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4Ywf_c3i',
                'auth' => 'tBHItJI5svbpez7KI4CCXg',
            ],
            'device_name' => 'Chrome Windows Test Device',
        ];

        $response = $this->actingAs($user)->postJson('/push/subscribe', $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => $payload['endpoint'],
            'public_key' => $payload['keys']['p256dh'],
            'auth_token' => $payload['keys']['auth'],
            'device_name' => 'Chrome Windows Test Device',
        ]);
    }

    /**
     * Đăng ký thành công ngay cả khi device_name là User-Agent di động dài (> 120 ký tự).
     */
    public function test_authenticated_user_can_subscribe_with_long_mobile_user_agent(): void
    {
        $user = User::factory()->create();
        $longMobileUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1';

        $payload = [
            'endpoint' => 'https://web.push.apple.com/test-endpoint-'.uniqid(),
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4Ywf_c3i',
                'auth' => 'tBHItJI5svbpez7KI4CCXg',
            ],
            'device_name' => $longMobileUA,
        ];

        $response = $this->actingAs($user)->postJson('/push/subscribe', $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => $payload['endpoint'],
        ]);
    }

    /**
     * Người dùng có thể kiểm tra trạng thái Push Subscription của mình.
     */
    public function test_authenticated_user_can_check_push_status(): void
    {
        $user = User::factory()->create();

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/test-endpoint-'.uniqid(),
            'public_key' => 'sample_p256dh_key',
            'auth_token' => 'sample_auth_token',
        ]);

        $response = $this->actingAs($user)->getJson('/push/status');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertEquals(1, $response->json('data.device_count'));
    }

    /**
     * Người dùng có thể hủy đăng ký Push Subscription.
     */
    public function test_authenticated_user_can_unsubscribe_from_push(): void
    {
        $user = User::factory()->create();
        $endpoint = 'https://fcm.googleapis.com/test-endpoint-unsub';

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => $endpoint,
            'public_key' => 'sample_p256dh_key',
            'auth_token' => 'sample_auth_token',
        ]);

        $response = $this->actingAs($user)->postJson('/push/unsubscribe', [
            'endpoint' => $endpoint,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => $endpoint,
        ]);
    }

    /**
     * Lệnh Artisan quét và gửi nhắc nhở chạy thành công mà không gặp lỗi.
     */
    public function test_push_send_reminders_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('push:send-reminders', ['--window' => 30]);
        $this->assertEquals(0, $exitCode, 'Lệnh push:send-reminders phải hoàn tất với exit code 0.');
    }

    /**
     * Khi chưa có thiết bị nào đăng ký push, gọi /push/test trả về lỗi 400 hợp lệ.
     */
    public function test_user_cannot_send_test_push_without_subscriptions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/push/test');
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
