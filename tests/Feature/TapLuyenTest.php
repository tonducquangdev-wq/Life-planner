<?php

namespace Tests\Feature;

use App\Models\BaiTapTheChat;
use App\Models\BuoiTap;
use App\Models\ChiTietBuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TapLuyenTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo user với đúng trường ho_ten của bảng users
        $this->user = User::create([
            'ho_ten' => 'Nguyen Van A',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * TEST 01 & 08: Trang theo dõi thể chất tải thành công và hiển thị chuẩn
     */
    public function test_tap_luyen_index_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/tap-luyen');

        $response->assertStatus(200);
        $response->assertSee('Theo dõi Thể chất');
        $response->assertSee('Buổi tập hôm nay');
        $response->assertSee('Tùy chỉnh buổi tập');
    }

    /**
     * TEST 01, 02, 03, 04: Lưu buổi tập kết hợp Strength + Core + Cardio
     */
    public function test_cap_nhat_buoi_tap_with_mixed_strength_core_cardio(): void
    {
        $payload = [
            'ten_buoi_tap' => 'Push — Thử nghiệm kết hợp',
            'exercises' => [
                [
                    'name' => 'Barbell Bench Press',
                    'type' => 'strength',
                    'sets' => 4,
                    'reps' => '8-10',
                ],
                [
                    'name' => 'Incline Press',
                    'type' => 'strength',
                    'sets' => 3,
                    'reps' => '8-12',
                ],
                [
                    'name' => 'Cable Fly',
                    'type' => 'strength',
                    'sets' => 3,
                    'reps' => '10-12',
                ],
                [
                    'name' => 'Triceps Pushdown',
                    'type' => 'strength',
                    'sets' => 3,
                    'reps' => '10-12',
                ],
                [
                    'name' => 'Core (Plank)',
                    'type' => 'core',
                    'sets' => 3,
                    'reps' => '10-12',
                ],
                [
                    'name' => 'Đi bộ',
                    'type' => 'cardio',
                    'duration' => 30,
                    'duration_unit' => 'phut',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/tap-luyen/cap-nhat-buoi-tap', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertCount(6, $data['exercises']);

        // Kiểm tra bài tập thứ 6 là Cardio "Đi bộ" với đúng thời lượng 30 phút, KHÔNG ép sets/reps
        $cardioEx = $data['exercises'][5];
        $this->assertEquals('Đi bộ', $cardioEx['name']);
        $this->assertEquals('cardio', $cardioEx['type']);
        $this->assertEquals(30, $cardioEx['duration']);
        $this->assertEquals('phut', $cardioEx['duration_unit']);
        $this->assertNull($cardioEx['sets']);
        $this->assertNull($cardioEx['reps']);
        $this->assertEquals('30 phút', $cardioEx['metric_display']);
        $this->assertEquals(6, $cardioEx['order']);

        // Kiểm tra bài tập thứ 1 là Strength "Barbell Bench Press" 4 x 8-10
        $strengthEx = $data['exercises'][0];
        $this->assertEquals('Barbell Bench Press', $strengthEx['name']);
        $this->assertEquals('strength', $strengthEx['type']);
        $this->assertEquals(4, $strengthEx['sets']);
        $this->assertEquals('8-10', $strengthEx['reps']);
        $this->assertNull($strengthEx['duration']);
        $this->assertEquals('4 sets × 8-10 reps', $strengthEx['metric_display']);
        $this->assertEquals(1, $strengthEx['order']);

        // Xác thực trong cơ sở dữ liệu (Database Integrity)
        $this->assertDatabaseHas('chi_tiet_buoi_tap', [
            'buoi_tap_id' => $data['buoi_tap_id'],
            'thu_tu' => 6,
            'loai_bai_tap' => 'cardio',
            'thoi_luong' => 30,
            'don_vi_thoi_gian' => 'phut',
            'so_sets' => null,
            'so_reps' => null,
        ]);
    }

    /**
     * TEST: Validation từ chối Cardio không có thời lượng
     */
    public function test_validation_rejects_cardio_without_duration(): void
    {
        $payload = [
            'ten_buoi_tap' => 'Cardio Test',
            'exercises' => [
                [
                    'name' => 'Chạy bộ',
                    'type' => 'cardio',
                    'duration' => null, // Thiếu thời lượng
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/tap-luyen/cap-nhat-buoi-tap', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * TEST: Validation từ chối Strength thiếu Sets hoặc Reps
     */
    public function test_validation_rejects_strength_without_sets_or_reps(): void
    {
        $payload = [
            'ten_buoi_tap' => 'Strength Test',
            'exercises' => [
                [
                    'name' => 'Squat',
                    'type' => 'strength',
                    'sets' => null, // Thiếu sets
                    'reps' => '8-12',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/tap-luyen/cap-nhat-buoi-tap', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * TEST 05, 07: Hoàn thành buổi tập ghi nhận lịch sử thực tế vào DB
     */
    public function test_hoan_thanh_buoi_tap_records_history_successfully(): void
    {
        $payload = [
            'ten_buoi_tap' => 'Push — Tập ngực và tay sau',
            'seconds' => 1800, // 30 phút
            'tong_thoi_luong' => 30,
            'ghi_chu' => 'Hoàn thành buổi tập Push đầy đủ',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/tap-luyen/hoan-thanh', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('lich_su_tap_luyen', [
            'user_id' => $this->user->id,
            'tong_thoi_luong' => 30,
            'ghi_chu' => 'Hoàn thành buổi tập Push đầy đủ',
        ]);
    }
}
