<?php

namespace Tests\Feature;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TapLuyenPresetRoutineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * TEST 01: Áp dụng Lịch tập mẫu và tự động tạo sự kiện lặp trên Calendar
     */
    public function test_user_can_apply_preset_plan_and_sync_events_to_calendar(): void
    {
        $payload = [
            'preset_id' => 'ppl_3',
            'ten_ke_hoach' => 'Lịch Tập Push Pull Legs Chuẩn',
            'mo_ta' => 'Giáo án 3 buổi/tuần',
            'dong_bo_calendar' => true,
            'so_tuan_lap' => 4,
            'bat_thong_bao' => true,
            'sessions' => [
                [
                    'ten_buoi_tap' => 'Push (Ngực, Vai, Tay sau)',
                    'ngay_trong_tuan' => 1, // Thứ 2
                    'gio_bat_dau' => '17:30',
                    'gio_ket_thuc' => '18:30',
                    'exercises' => [
                        ['name' => 'Bench Press', 'type' => 'strength', 'group' => 'Ngực', 'sets' => 4, 'reps' => '8-10'],
                        ['name' => 'Overhead Press', 'type' => 'strength', 'group' => 'Vai', 'sets' => 3, 'reps' => '10'],
                    ],
                ],
                [
                    'ten_buoi_tap' => 'Pull (Lưng, Xô, Tay trước)',
                    'ngay_trong_tuan' => 3, // Thứ 4
                    'gio_bat_dau' => '18:00',
                    'gio_ket_thuc' => '19:00',
                    'exercises' => [
                        ['name' => 'Lat Pulldown', 'type' => 'strength', 'group' => 'Lưng', 'sets' => 4, 'reps' => '10-12'],
                    ],
                ],
                [
                    'ten_buoi_tap' => 'Legs & Core (Chân, Mông, Bụng)',
                    'ngay_trong_tuan' => 5, // Thứ 6
                    'gio_bat_dau' => '17:30',
                    'gio_ket_thuc' => '18:30',
                    'exercises' => [
                        ['name' => 'Barbell Squat', 'type' => 'strength', 'group' => 'Chân', 'sets' => 4, 'reps' => '8'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->postJson('/tap-luyen/ke-hoach-mau/ap-dung', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // 1. Kế hoạch mới được tạo và đang active
        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Lịch Tập Push Pull Legs Chuẩn',
            'is_active' => true,
        ]);

        $plan = KeHoachTapLuyen::where('user_id', $this->user->id)->first();
        $this->assertNotNull($plan);

        // 2. 3 Buổi tập được tạo
        $this->assertEquals(3, BuoiTap::where('ke_hoach_tap_luyen_id', $plan->id)->count());

        // 3. Sự kiện Calendar (SuKien) được tạo: 3 buổi * 4 tuần = 12 sự kiện
        $this->assertEquals(12, SuKien::where('user_id', $this->user->id)->count());

        // Kiểm tra loại sự kiện là tap_luyen
        $firstEvent = SuKien::where('user_id', $this->user->id)->first();
        $this->assertEquals('tap_luyen', $firstEvent->loai_su_kien);
        $this->assertEquals('weekly', $firstEvent->quy_tac_lap);
        $this->assertNotEmpty($firstEvent->nhom_lap_id);
    }

    /**
     * TEST 02: Áp dụng lịch mẫu mới sẽ tự động hủy kích hoạt kế hoạch cũ
     */
    public function test_applying_preset_deactivates_previous_plan(): void
    {
        $oldPlan = KeHoachTapLuyen::create([
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Kế hoạch cũ',
            'is_active' => true,
        ]);

        $payload = [
            'preset_id' => 'fullbody_3',
            'ten_ke_hoach' => 'Full Body Mới',
            'dong_bo_calendar' => false,
            'sessions' => [
                [
                    'ten_buoi_tap' => 'Full Body A',
                    'ngay_trong_tuan' => 1,
                    'gio_bat_dau' => '07:00',
                    'gio_ket_thuc' => '08:00',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->postJson('/tap-luyen/ke-hoach-mau/ap-dung', $payload);
        $response->assertStatus(200);

        $oldPlan->refresh();
        $this->assertFalse($oldPlan->is_active);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'user_id' => $this->user->id,
            'ten_ke_hoach' => 'Full Body Mới',
            'is_active' => true,
        ]);

        // Vì dong_bo_calendar = false nên không tạo SuKien
        $this->assertEquals(0, SuKien::where('user_id', $this->user->id)->count());
    }

    /**
     * TEST 03: Xác thực dữ liệu bắt buộc (Validation)
     */
    public function test_validation_errors_when_sessions_missing(): void
    {
        $response = $this->actingAs($this->user)->postJson('/tap-luyen/ke-hoach-mau/ap-dung', [
            'ten_ke_hoach' => '',
            'sessions' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ten_ke_hoach', 'sessions']);
    }
}
