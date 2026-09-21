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
}
