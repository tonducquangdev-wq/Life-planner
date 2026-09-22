<?php

namespace Tests\Feature\Api;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TapLuyenApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'ho_ten' => 'API Test User',
            'email' => 'api_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * TEST: API /api/tap-luyen/schedule yêu cầu authentication Sanctum
     */
    public function test_schedule_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/api/tap-luyen/schedule');
        $response->assertStatus(401);
    }

    /**
     * TEST: API /api/tap-luyen/schedule trả về lịch tuần động 7 ngày và trạng thái hôm nay
     */
    public function test_schedule_endpoint_returns_dynamic_weekly_schedule(): void
    {
        Sanctum::actingAs($this->user);

        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Full Body 3 Days',
            'is_active' => true,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Full Body A',
            'ngay_trong_tuan' => 1, // Thứ 2
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Full Body B',
            'ngay_trong_tuan' => 3, // Thứ 4
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Full Body C',
            'ngay_trong_tuan' => 5, // Thứ 6
        ]);

        $response = $this->getJson('/api/tap-luyen/schedule');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertCount(7, $data['weekly_schedule']);
        $this->assertEquals('Full Body 3 Days', $data['active_plan']['ten_ke_hoach']);

        // Kiểm tra Thứ 2 (index 0)
        $this->assertEquals('Full Body A', $data['weekly_schedule'][0]['ten']);
        $this->assertFalse($data['weekly_schedule'][0]['is_rest']);

        // Kiểm tra Thứ 3 (index 1) - Ngày nghỉ
        $this->assertEquals('Nghỉ ngơi', $data['weekly_schedule'][1]['ten']);
        $this->assertTrue($data['weekly_schedule'][1]['is_rest']);
    }

    /**
     * TEST: API CRUD Kế hoạch tập luyện (/api/ke-hoach-tap-luyen)
     */
    public function test_api_plans_crud(): void
    {
        Sanctum::actingAs($this->user);

        // Tạo kế hoạch
        $createRes = $this->postJson('/api/ke-hoach-tap-luyen', [
            'ten_ke_hoach' => 'Bro Split 5 Days',
            'mo_ta' => 'Chuyên sâu từng nhóm cơ',
            'is_active' => true,
        ]);

        $createRes->assertStatus(201);
        $planId = $createRes->json('data.id');

        // Lấy danh sách kế hoạch
        $listRes = $this->getJson('/api/ke-hoach-tap-luyen');
        $listRes->assertStatus(200);
        $this->assertCount(1, $listRes->json('data'));

        // Xóa kế hoạch
        $deleteRes = $this->deleteJson("/api/ke-hoach-tap-luyen/{$planId}");
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('ke_hoach_tap_luyen', ['id' => $planId]);
    }

    /**
     * TEST: API CRUD Buổi tập (/api/buoi-tap)
     */
    public function test_api_sessions_crud(): void
    {
        Sanctum::actingAs($this->user);

        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'My Split',
            'is_active' => true,
        ]);

        // Tạo buổi tập
        $createRes = $this->postJson('/api/buoi-tap', [
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Leg Day Extra',
            'ngay_trong_tuan' => 6,
        ]);

        $createRes->assertStatus(201);
        $sessionId = $createRes->json('data.id');

        // Cập nhật buổi tập
        $updateRes = $this->putJson("/api/buoi-tap/{$sessionId}", [
            'ten_buoi_tap' => 'Leg Day Extreme',
            'ngay_trong_tuan' => 7,
        ]);

        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('buoi_tap', [
            'id' => $sessionId,
            'ten_buoi_tap' => 'Leg Day Extreme',
            'ngay_trong_tuan' => 7,
        ]);

        // Xóa buổi tập
        $deleteRes = $this->deleteJson("/api/buoi-tap/{$sessionId}");
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('buoi_tap', ['id' => $sessionId]);
    }

    /**
     * TEST: User A KHÔNG THỂ hoàn thành buổi tập của User B (Anti-IDOR)
     */
    public function test_user_cannot_complete_other_users_session(): void
    {
        $otherUser = User::create([
            'ho_ten' => 'Other User',
            'email' => 'other_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        $otherPlan = KeHoachTapLuyen::create([
            'user_id' => $otherUser->id,
            'ten_ke_hoach' => 'Other Plan',
            'is_active' => true,
        ]);

        $otherSession = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $otherPlan->id,
            'ten_buoi_tap' => 'Other Chest Day',
        ]);

        Sanctum::actingAs($this->user);

        // User A gửi buoi_tap_id của User B
        $response = $this->postJson('/api/tap-luyen/hoan-thanh', [
            'buoi_tap_id' => $otherSession->id,
            'ten_buoi_tap' => 'Hack Session',
            'tong_thoi_luong' => 45,
        ]);

        // Bắt buộc phải bị REJECT (422 Unprocessable do validation failure hoặc 403 Forbidden)
        $this->assertTrue(in_array($response->status(), [403, 422]));
    }

    /**
     * TEST: API /api/tap-luyen/lich-su trả về 200 kèm nested eager loading buoiTap.keHoachTapLuyen
     */
    public function test_workout_history_returns_correct_nested_relationships(): void
    {
        Sanctum::actingAs($this->user);

        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'My Split',
            'is_active' => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Leg Day',
        ]);

        // Tạo 1 bản ghi lịch sử tập
        $this->postJson('/api/tap-luyen/hoan-thanh', [
            'buoi_tap_id' => $session->id,
            'tong_thoi_luong' => 30,
        ])->assertStatus(200);

        // Gọi API lấy lịch sử
        $res = $this->getJson('/api/tap-luyen/lich-su');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Leg Day', $data[0]['buoi_tap']['ten_buoi_tap']);
        $this->assertEquals('My Split', $data[0]['buoi_tap']['ke_hoach_tap_luyen']['ten_ke_hoach']);
    }

    /**
     * TEST: Validation enum loai_su_kien và Partial Update ngày kết thúc
     */
    public function test_su_kien_validation_and_partial_update(): void
    {
        Sanctum::actingAs($this->user);

        // 1. Tạo sự kiện với loai_su_kien không hợp lệ -> Phải trả về 422
        $invalidRes = $this->postJson('/api/su-kien', [
            'tieu_de' => 'Học nhóm',
            'loai_su_kien' => 'invalid_type',
            'thoi_gian_bat_dau' => '2026-09-25 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-25 10:00:00',
        ]);
        $invalidRes->assertStatus(422);

        // 2. Tạo sự kiện hợp lệ
        $createRes = $this->postJson('/api/su-kien', [
            'tieu_de' => 'Ôn tập giải tích',
            'loai_su_kien' => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-09-25 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-25 10:00:00',
        ]);
        $createRes->assertStatus(201);
        $eventId = $createRes->json('data.id');

        // 3. Partial update chỉ gửi thoi_gian_ket_thuc hợp lệ (sau thoi_gian_bat_dau trong DB)
        $validUpdate = $this->putJson("/api/su-kien/{$eventId}", [
            'thoi_gian_ket_thuc' => '2026-09-25 11:30:00',
        ]);
        $validUpdate->assertStatus(200);

        // 4. Partial update chỉ gửi thoi_gian_ket_thuc trước thoi_gian_bat_dau trong DB -> Phải fail 422
        $invalidUpdate = $this->putJson("/api/su-kien/{$eventId}", [
            'thoi_gian_ket_thuc' => '2026-09-25 07:00:00',
        ]);
        $invalidUpdate->assertStatus(422);
    }

    /**
     * TEST: Throttle Login API (Rate Limiting)
     */
    public function test_login_rate_limiting(): void
    {
        // Gửi 5 request login sai
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // Request thứ 6 phải bị throttle (429 Too Many Requests)
        $res = $this->postJson('/api/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'wrongpassword',
        ]);

        $res->assertStatus(429);
    }
}

