<?php

namespace Tests\Feature;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TapLuyenCustomScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'ho_ten' => 'Tran Thi B',
            'email' => 'schedule_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * TEST 01: Khởi tạo kế hoạch mặc định khi người dùng mới truy cập /tap-luyen
     */
    public function test_new_user_gets_default_plan_and_custom_schedule(): void
    {
        $response = $this->actingAs($this->user)->get('/tap-luyen');

        $response->assertStatus(200);
        $response->assertSee('Quản lý lịch tập');
        $response->assertSee('Theo dõi Thể chất');

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
    }

    /**
     * TEST 02: Lịch tuần động nhận diện chính xác ngày nghỉ (Rest Day) khi ngày đó không có buổi tập
     */
    public function test_weekly_schedule_identifies_rest_days_for_unassigned_weekdays(): void
    {
        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Upper / Lower 2 ngày',
            'mo_ta' => 'Chỉ tập Thứ 2 và Thứ 5',
            'is_active' => true,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Upper Body Focus',
            'thu_tu' => 1,
            'ngay_trong_tuan' => 1, // Thứ 2
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Lower Body Focus',
            'thu_tu' => 2,
            'ngay_trong_tuan' => 4, // Thứ 5
        ]);

        $controller = new \App\Http\Controllers\TapLuyenController();
        $lichTuan = $controller->buildLichTuan($plan);

        // Thứ 2: Có buổi tập
        $this->assertFalse($lichTuan[1]['is_rest']);
        $this->assertEquals('Upper Body Focus', $lichTuan[1]['ten']);

        // Thứ 3: Là ngày nghỉ (Rest Day)
        $this->assertTrue($lichTuan[2]['is_rest']);
        $this->assertEquals('Nghỉ ngơi', $lichTuan[2]['ten']);

        // Thứ 4: Là ngày nghỉ (Rest Day)
        $this->assertTrue($lichTuan[3]['is_rest']);
        $this->assertEquals('Nghỉ ngơi', $lichTuan[3]['ten']);

        // Thứ 5: Có buổi tập
        $this->assertFalse($lichTuan[4]['is_rest']);
        $this->assertEquals('Lower Body Focus', $lichTuan[4]['ten']);

        // Thứ 6, 7, CN: Là ngày nghỉ
        $this->assertTrue($lichTuan[5]['is_rest']);
        $this->assertTrue($lichTuan[6]['is_rest']);
        $this->assertTrue($lichTuan[7]['is_rest']);
    }

    /**
     * TEST 03: Thêm buổi tập mới có chỉ định ngày trong tuần
     */
    public function test_user_can_add_custom_session_with_weekday(): void
    {
        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Custom Split Plan',
            'is_active' => true,
        ]);

        $payload = [
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Cardio HIIT Cuối tuần',
            'mo_ta' => 'Chạy biến tốc 30 phút',
            'ngay_trong_tuan' => 6, // Thứ 7
        ];

        $response = $this->actingAs($this->user)->postJson('/tap-luyen/buoi-tap', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'ten_buoi_tap' => 'Cardio HIIT Cuối tuần',
                'ngay_trong_tuan' => 6,
                'ten_thu' => 'Thứ 7',
            ],
        ]);

        $this->assertDatabaseHas('buoi_tap', [
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Cardio HIIT Cuối tuần',
            'ngay_trong_tuan' => 6,
        ]);
    }

    /**
     * TEST 04: Cập nhật và Xóa buổi tập trong kế hoạch
     */
    public function test_user_can_update_and_delete_session(): void
    {
        $plan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Plan Test',
            'is_active' => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Session A',
            'thu_tu' => 1,
            'ngay_trong_tuan' => 2,
        ]);

        $updatePayload = [
            'ten_buoi_tap' => 'Session A Đã đổi tên',
            'mo_ta' => 'Mô tả mới',
            'ngay_trong_tuan' => 3, // Chuyển sang Thứ 4
        ];

        $updateRes = $this->actingAs($this->user)->putJson("/tap-luyen/buoi-tap/{$session->id}", $updatePayload);
        $updateRes->assertStatus(200);
        $updateRes->assertJson(['success' => true]);

        $this->assertDatabaseHas('buoi_tap', [
            'id' => $session->id,
            'ten_buoi_tap' => 'Session A Đã đổi tên',
            'ngay_trong_tuan' => 3,
        ]);

        $deleteRes = $this->actingAs($this->user)->deleteJson("/tap-luyen/buoi-tap/{$session->id}");
        $deleteRes->assertStatus(200);
        $deleteRes->assertJson(['success' => true]);

        $this->assertDatabaseMissing('buoi_tap', ['id' => $session->id]);
    }

    /**
     * TEST 05: Tạo kế hoạch mới và Kích hoạt kế hoạch (Plan Switching)
     */
    public function test_user_can_create_and_switch_active_plan(): void
    {
        $plan1 = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Kế hoạch 1 - Đang dùng',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/tap-luyen/ke-hoach', [
            'ten_ke_hoach' => 'Kế hoạch 2 - 4 Buổi',
            'mo_ta' => 'Split mới cho mùa đông',
            'is_active' => false,
        ]);

        $response->assertStatus(200);
        $plan2Id = $response->json('data.id');

        $activateRes = $this->actingAs($this->user)->postJson("/tap-luyen/ke-hoach/{$plan2Id}/kich-hoat");
        $activateRes->assertStatus(200);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'id' => $plan1->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'id' => $plan2Id,
            'is_active' => true,
        ]);
    }
}
