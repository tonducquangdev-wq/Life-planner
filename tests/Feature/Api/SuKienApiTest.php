<?php

namespace Tests\Feature\Api;

use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuKienApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/su-kien : User lấy danh sách sự kiện của chính mình thành công.
     */
    public function test_authenticated_user_can_get_their_events(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        SuKien::create([
            'user_id'           => $userA->id,
            'tieu_de'           => 'Họp Team A',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-10 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-10 10:00:00',
        ]);

        SuKien::create([
            'user_id'           => $userA->id,
            'tieu_de'           => 'Họp Khách Hàng A',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-11 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-11 15:00:00',
        ]);

        SuKien::create([
            'user_id'           => $userB->id,
            'tieu_de'           => 'Sự kiện riêng của B',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-12 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-12 10:00:00',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/su-kien');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách sự kiện thành công!',
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonFragment(['tieu_de' => 'Họp Team A'])
            ->assertJsonFragment(['tieu_de' => 'Họp Khách Hàng A'])
            ->assertJsonMissing(['tieu_de' => 'Sự kiện riêng của B']);
    }

    /**
     * 2. Guest gọi API sự kiện nhận 401 Unauthorized.
     */
    public function test_guest_cannot_access_su_kien_api(): void
    {
        $this->getJson('/api/su-kien')->assertStatus(401);
        $this->postJson('/api/su-kien', [])->assertStatus(401);
        $this->putJson('/api/su-kien/1', [])->assertStatus(401);
        $this->deleteJson('/api/su-kien/1')->assertStatus(401);
    }

    /**
     * 3. POST /api/su-kien : User tạo sự kiện mới thành công.
     */
    public function test_user_can_create_event_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $payload = [
            'tieu_de'           => 'Thi Cuối Kỳ Lập Trình Web',
            'mo_ta'             => 'Thi phòng A201',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 10:00:00',
            'mau_hien_thi'      => '#EF4444',
            'bat_thong_bao'     => true,
            'so_ngay_nhac'      => 2,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo sự kiện thành công!',
                'data'    => [
                    'user_id'      => $user->id,
                    'tieu_de'      => 'Thi Cuối Kỳ Lập Trình Web',
                    'loai_su_kien' => 'hoc_tap',
                ],
            ]);

        $this->assertDatabaseHas('su_kien', [
            'user_id' => $user->id,
            'tieu_de' => 'Thi Cuối Kỳ Lập Trình Web',
        ]);
    }

    /**
     * 4. POST /api/su-kien : Lỗi Validation khi thiếu tiêu đề hoặc thời gian kết thúc trước thời gian bắt đầu.
     */
    public function test_create_event_fails_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => '',
                'thoi_gian_bat_dau' => '2026-10-15 10:00:00',
                'thoi_gian_ket_thuc' => '2026-10-15 08:00:00', // Kết thúc trước khi bắt đầu
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tieu_de', 'thoi_gian_ket_thuc']);
    }

    /**
     * 5. PUT /api/su-kien/{id} : User cập nhật sự kiện của chính mình thành công.
     */
    public function test_user_can_update_their_own_event(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $suKien = SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Tên Sự Kiện Cũ',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 10:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $suKien->id, [
                'tieu_de' => 'Tên Sự Kiện Đã Được Đổi',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật sự kiện thành công!',
                'data'    => [
                    'id'      => $suKien->id,
                    'tieu_de' => 'Tên Sự Kiện Đã Được Đổi',
                ],
            ]);

        $this->assertDatabaseHas('su_kien', [
            'id'      => $suKien->id,
            'tieu_de' => 'Tên Sự Kiện Đã Được Đổi',
        ]);
    }

    /**
     * 6. PUT /api/su-kien/{id} : Anti-IDOR - Không thể sửa sự kiện của User khác (404).
     */
    public function test_user_cannot_update_other_users_event(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $suKienB = SuKien::create([
            'user_id'           => $userB->id,
            'tieu_de'           => 'Sự kiện của B',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 10:00:00',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/su-kien/' . $suKienB->id, [
                'tieu_de' => 'Hack tên sự kiện của B',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('su_kien', [
            'id'      => $suKienB->id,
            'tieu_de' => 'Sự kiện của B',
        ]);
    }

    /**
     * 7. DELETE /api/su-kien/{id} : User xóa sự kiện của chính mình thành công (Soft Delete).
     */
    public function test_user_can_delete_their_own_event(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $suKien = SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện sắp xóa',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 10:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/su-kien/' . $suKien->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sự kiện thành công!',
            ]);

        $this->assertSoftDeleted('su_kien', [
            'id' => $suKien->id,
        ]);
    }

    /**
     * 8. DELETE /api/su-kien/{id} : Anti-IDOR - Không thể xóa sự kiện của User khác (404).
     */
    public function test_user_cannot_delete_other_users_event(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $suKienB = SuKien::create([
            'user_id'           => $userB->id,
            'tieu_de'           => 'Sự kiện quan trọng của B',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 10:00:00',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/su-kien/' . $suKienB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('su_kien', [
            'id'         => $suKienB->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * 9. GET /api/su-kien?start_date=2026-10-10&end_date=2026-10-15 : Lọc theo dải ngày.
     */
    public function test_user_can_filter_events_by_date_range(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện Tháng 9',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-09-30 09:00:00',
        ]);

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện Trong Khung',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-12 10:00:00',
        ]);

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện Tháng 11',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-11-01 09:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/su-kien?start_date=2026-10-01&end_date=2026-10-31');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['tieu_de' => 'Sự kiện Trong Khung'])
            ->assertJsonMissing(['tieu_de' => 'Sự kiện Tháng 9'])
            ->assertJsonMissing(['tieu_de' => 'Sự kiện Tháng 11']);
    }

    /**
     * 10. GET /api/su-kien?month=10&year=2026 : Lọc theo Tháng và Năm.
     */
    public function test_user_can_filter_events_by_month_and_year(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện tháng 10 năm 2026',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
        ]);

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện tháng 10 năm 2025',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2025-10-15 08:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/su-kien?month=10&year=2026');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['tieu_de' => 'Sự kiện tháng 10 năm 2026'])
            ->assertJsonMissing(['tieu_de' => 'Sự kiện tháng 10 năm 2025']);
    }

    /**
     * 11. GET /api/su-kien?loai_su_kien=hoc_tap : Lọc theo loại sự kiện.
     */
    public function test_user_can_filter_events_by_type(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Buổi Học Nhóm',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-15 08:00:00',
        ]);

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Đi Chơi Sinh Nhật',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-15 19:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/su-kien?loai_su_kien=hoc-tap');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['tieu_de' => 'Buổi Học Nhóm'])
            ->assertJsonMissing(['tieu_de' => 'Đi Chơi Sinh Nhật']);
    }

    /**
     * 12. GET /api/su-kien : Kết hợp nhiều filter cùng lúc.
     */
    public function test_user_can_combine_multiple_event_filters(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Thi Cuối Kỳ',
            'loai_su_kien'      => 'deadline',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
        ]);

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Tập Gym',
            'loai_su_kien'      => 'tap_luyen',
            'thoi_gian_bat_dau' => '2026-10-20 18:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/su-kien?year=2026&month=10&loai_su_kien=deadline');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['tieu_de' => 'Thi Cuối Kỳ'])
            ->assertJsonMissing(['tieu_de' => 'Tập Gym']);
    }

    /**
     * 13. POST /api/su-kien : Trả về 409 khi phát hiện trùng lịch.
     */
    public function test_create_event_returns_409_when_conflict_detected(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Học Lập Trình Web',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:30:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Nhóm Đồ Án',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'success'      => false,
                'has_conflict' => true,
                'message'      => 'Phát hiện lịch trình bị trùng.',
            ]);

        $this->assertCount(1, $response->json('conflicts'));
    }

    /**
     * 14. POST /api/su-kien : Bỏ qua trùng lịch khi gửi force=true.
     */
    public function test_create_event_with_force_true_bypasses_conflict(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Học Lập Trình Web',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:30:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Nhóm Ép Thêm',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
                'force'             => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('su_kien', ['tieu_de' => 'Họp Nhóm Ép Thêm']);
    }

    /**
     * 15. POST /api/su-kien : Bỏ qua trùng lịch khi gửi van_them=true.
     */
    public function test_create_event_with_van_them_true_bypasses_conflict(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Học Lập Trình Web',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:30:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Vẫn Thêm',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
                'van_them'          => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('su_kien', ['tieu_de' => 'Họp Vẫn Thêm']);
    }

    /**
     * 16. PUT /api/su-kien/{id} : Trả về 409 khi cập nhật bị trùng thời gian với sự kiện khác.
     */
    public function test_update_event_returns_409_when_conflict_detected(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện 1',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:00:00',
        ]);

        $event2 = SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện 2 chiều',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-20 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 16:00:00',
        ]);

        // Đổi sự kiện 2 sang trùng với sự kiện 1
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $event2->id, [
                'thoi_gian_bat_dau'  => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'success'      => false,
                'has_conflict' => true,
            ]);
    }

    /**
     * 17. PUT /api/su-kien/{id} : Bỏ qua trùng lịch khi cập nhật có force=true.
     */
    public function test_update_event_with_force_true_bypasses_conflict(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện 1',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:00:00',
        ]);

        $event2 = SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự kiện 2',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-20 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 16:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $event2->id, [
                'thoi_gian_bat_dau'  => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
                'force'              => true,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * 18. Response 409 chứa mảng conflicts đúng cấu trúc chi tiết.
     */
    public function test_conflict_response_contains_conflicts_array(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Lớp Thuật Toán',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:00:00',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Trùng Lịch Thuật Toán',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-20 09:00:00',
                'thoi_gian_ket_thuc' => '2026-10-20 11:00:00',
            ]);

        $response->assertStatus(409);
        $conflicts = $response->json('conflicts');

        $this->assertIsArray($conflicts);
        $this->assertNotEmpty($conflicts);
        $this->assertEquals('Lớp Thuật Toán', $conflicts[0]['tieu_de']);
        $this->assertArrayHasKey('date', $conflicts[0]);
        $this->assertArrayHasKey('time', $conflicts[0]);
        $this->assertArrayHasKey('type', $conflicts[0]);
    }

    /**
     * 19. Guest không thể gọi API với force=true nếu không có authentication Sanctum.
     */
    public function test_guest_cannot_bypass_conflict_without_authentication(): void
    {
        $response = $this->postJson('/api/su-kien', [
            'tieu_de'           => 'Lịch Hack Guest',
            'thoi_gian_bat_dau' => '2026-10-20 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 10:00:00',
            'force'             => true,
        ]);

        $response->assertStatus(401);
    }

    /**
     * 20. POST /api/su-kien : Tạo sự kiện lặp hằng ngày (daily).
     */
    public function test_can_create_daily_recurring_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Tập Gym Sáng Hằng Ngày',
                'loai_su_kien'      => 'tap_luyen',
                'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-05', // Oct 1, 2, 3, 4, 5 -> 5 instances
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success'           => true,
                'so_su_kien_da_tao' => 5,
            ]);

        $this->assertEquals(5, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 21. POST /api/su-kien : Tạo sự kiện lặp hằng tuần (weekly).
     */
    public function test_can_create_weekly_recurring_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Học Lập Trình Laravel Hằng Tuần',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
                'quy_tac_lap'       => 'weekly',
                'ngay_ket_thuc_lap' => '2026-10-22', // Oct 1, 8, 15, 22 -> 4 instances
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success'           => true,
                'so_su_kien_da_tao' => 4,
            ]);

        $this->assertEquals(4, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 22. POST /api/su-kien : Tạo sự kiện lặp hằng tháng (monthly) với addMonthsNoOverflow.
     */
    public function test_can_create_monthly_recurring_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Báo Cáo Tháng',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-01-31 09:00:00',
                'thoi_gian_ket_thuc' => '2026-01-31 10:00:00',
                'quy_tac_lap'       => 'monthly',
                'ngay_ket_thuc_lap' => '2026-04-30', // Jan 31, Feb 28, Mar 31, Apr 30 -> 4 instances
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success'           => true,
                'so_su_kien_da_tao' => 4,
            ]);

        $this->assertEquals(4, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 23. POST /api/su-kien : Lỗi validation 422 khi sự kiện lặp thiếu ngay_ket_thuc_lap.
     */
    public function test_recurring_event_requires_end_date(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Lặp Thiếu Ngày Kết Thúc',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
                'quy_tac_lap'       => 'daily',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ngay_ket_thuc_lap']);
    }

    /**
     * 24. POST /api/su-kien : Các bản ghi sự kiện lặp dùng chung nhom_lap_id UUID.
     */
    public function test_recurring_events_share_same_group_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Nhóm Lặp 3 Ngày',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $response->assertStatus(201);
        $events = SuKien::where('user_id', $user->id)->get();
        $firstNhomLapId = $events->first()->nhom_lap_id;

        $this->assertNotNull($firstNhomLapId);
        $this->assertEquals(3, $events->where('nhom_lap_id', $firstNhomLapId)->count());
    }

    /**
     * 25. POST /api/su-kien : Giới hạn tối đa 1000 lần lặp để tránh infinite loop.
     */
    public function test_recurring_event_respects_iteration_limit(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Sự Kiện Lặp Quá Dài',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2036-10-01', // 10 years out
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'so_su_kien_da_tao' => 1000,
            ]);

        $this->assertEquals(1000, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 26. POST /api/su-kien : Sự kiện đơn (once) tạo đúng 1 bản ghi với nhom_lap_id = null.
     */
    public function test_single_event_still_works(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Sự Kiện Đơn Lẻ',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
                'quy_tac_lap'       => 'once',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success'           => true,
                'so_su_kien_da_tao' => 1,
            ]);

        $event = SuKien::where('user_id', $user->id)->first();
        $this->assertNull($event->nhom_lap_id);
    }

    /**
     * 27. PUT /api/su-kien/{id} : Cập nhật 1 sự kiện lặp (update_mode = single).
     */
    public function test_update_single_recurring_event(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Lớp Học Hằng Ngày',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $events = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->get();
        $firstEvent = $events[0];
        $secondEvent = $events[1];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $firstEvent->id, [
                'tieu_de'     => 'Tiêu đề đổi riêng cho ngày 1',
                'update_mode' => 'single',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'      => $firstEvent->id,
                    'tieu_de' => 'Tiêu đề đổi riêng cho ngày 1',
                ],
            ]);

        $this->assertDatabaseHas('su_kien', [
            'id'      => $firstEvent->id,
            'tieu_de' => 'Tiêu đề đổi riêng cho ngày 1',
        ]);

        $this->assertDatabaseHas('su_kien', [
            'id'      => $secondEvent->id,
            'tieu_de' => 'Lớp Học Hằng Ngày',
        ]);
    }

    /**
     * 28. PUT /api/su-kien/{id} : Cập nhật toàn bộ sự kiện lặp (update_mode = all).
     */
    public function test_update_all_recurring_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Tiêu Đề Ban Đầu',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $events = SuKien::where('user_id', $user->id)->get();
        $firstEvent = $events->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $firstEvent->id, [
                'tieu_de'     => 'Tiêu Đề Mới Cho Cả Nhóm',
                'update_mode' => 'all',
            ]);

        $response->assertStatus(200);

        $updatedEvents = SuKien::where('user_id', $user->id)->get();
        foreach ($updatedEvents as $evt) {
            $this->assertEquals('Tiêu Đề Mới Cho Cả Nhóm', $evt->tieu_de);
        }
    }

    /**
     * 29. PUT /api/su-kien/{id} : Rút ngắn ngày kết thúc lặp làm soft delete các sự kiện tương lai (update_mode = all).
     */
    public function test_update_all_can_prune_future_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Tập Thể Dục 5 Ngày',
                'loai_su_kien'      => 'tap_luyen',
                'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-05', // 5 instances
            ]);

        $this->assertEquals(5, SuKien::where('user_id', $user->id)->count());

        $firstEvent = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $firstEvent->id, [
                'ngay_ket_thuc_lap' => '2026-10-03', // Rút ngắn còn 3 ngày
                'update_mode'       => 'all',
            ]);

        $response->assertStatus(200);

        $this->assertEquals(3, SuKien::where('user_id', $user->id)->count());
        $this->assertEquals(2, SuKien::onlyTrashed()->where('user_id', $user->id)->count());
    }

    /**
     * 30. PUT /api/su-kien/{id} : Kéo dài ngày kết thúc lặp sinh thêm các sự kiện thiếu (update_mode = all).
     */
    public function test_update_all_can_generate_missing_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Nhóm 3 Ngày',
                'loai_su_kien'      => 'ca_nhan',
                'thoi_gian_bat_dau' => '2026-10-01 10:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 11:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03', // 3 instances
            ]);

        $this->assertEquals(3, SuKien::where('user_id', $user->id)->count());

        $firstEvent = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $firstEvent->id, [
                'ngay_ket_thuc_lap' => '2026-10-05', // Kéo dài thêm 2 ngày
                'update_mode'       => 'all',
            ]);

        $response->assertStatus(200);

        $this->assertEquals(5, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 31. PUT /api/su-kien/{id} : Chuyển sự kiện đơn thành sự kiện lặp (update_mode = single).
     */
    public function test_update_single_can_convert_once_to_recurring(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $singleEvent = SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự Kiện Ban Đầu Đơn Lẻ',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-01 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 15:00:00',
            'quy_tac_lap'       => 'once',
            'nhom_lap_id'       => null,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $singleEvent->id, [
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
                'update_mode'       => 'single',
            ]);

        $response->assertStatus(200);

        $singleEvent->refresh();
        $this->assertNotNull($singleEvent->nhom_lap_id);
        $this->assertEquals('daily', $singleEvent->quy_tac_lap);
        $this->assertEquals(3, SuKien::where('user_id', $user->id)->where('nhom_lap_id', $singleEvent->nhom_lap_id)->count());
    }

    /**
     * 32. DELETE /api/su-kien/{id} : Xóa 1 sự kiện lặp (mode = single).
     */
    public function test_delete_single_recurring_event(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Sự Kiện Lặp 3 Ngày',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $events = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->get();
        $targetEvent = $events[1]; // Ngày 2

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/su-kien/' . $targetEvent->id . '?mode=single');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sự kiện thành công!',
            ]);

        $this->assertSoftDeleted('su_kien', ['id' => $targetEvent->id]);
        $this->assertEquals(2, SuKien::where('user_id', $user->id)->count());
    }

    /**
     * 33. DELETE /api/su-kien/{id} : Xóa toàn bộ sự kiện lặp trong nhóm (mode = all).
     */
    public function test_delete_all_recurring_events(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Sự Kiện Lặp 3 Ngày Xóa Tất Cả',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $events = SuKien::where('user_id', $user->id)->get();
        $firstEvent = $events->first();
        $groupUuid = $firstEvent->nhom_lap_id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/su-kien/' . $firstEvent->id . '?mode=all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sự kiện thành công!',
            ]);

        $this->assertEquals(0, SuKien::where('user_id', $user->id)->where('nhom_lap_id', $groupUuid)->count());
        $this->assertEquals(3, SuKien::onlyTrashed()->where('user_id', $user->id)->where('nhom_lap_id', $groupUuid)->count());
    }

    /**
     * 34. PUT /api/su-kien/{id} : Anti-IDOR khi update_mode = all (Không thể sửa nhóm lặp của User khác).
     */
    public function test_update_all_respects_user_ownership(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tokenA = $userA->createToken('auth_token')->plainTextToken;
        $uuid = (string) \Illuminate\Support\Str::uuid();

        $eventB = SuKien::create([
            'user_id'           => $userB->id,
            'tieu_de'           => 'Sự Kiện Của User B',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
            'quy_tac_lap'       => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-03',
            'nhom_lap_id'       => $uuid,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/su-kien/' . $eventB->id, [
                'tieu_de'     => 'User A Hack Tiêu Đề',
                'update_mode' => 'all',
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('su_kien', [
            'id'      => $eventB->id,
            'tieu_de' => 'Sự Kiện Của User B',
        ]);
    }

    /**
     * 35. DELETE /api/su-kien/{id} : Anti-IDOR khi mode = all (Không thể xóa nhóm lặp của User khác).
     */
    public function test_delete_all_respects_user_ownership(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tokenA = $userA->createToken('auth_token')->plainTextToken;
        $uuid = (string) \Illuminate\Support\Str::uuid();

        $eventB = SuKien::create([
            'user_id'           => $userB->id,
            'tieu_de'           => 'Sự Kiện Của User B',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
            'quy_tac_lap'       => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-03',
            'nhom_lap_id'       => $uuid,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/su-kien/' . $eventB->id . '?mode=all');

        $response->assertStatus(404);

        $this->assertDatabaseHas('su_kien', [
            'id'         => $eventB->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * 36. PUT /api/su-kien/{id} : update_mode = all trả về 409 khi nhóm lặp cập nhật bị trùng với sự kiện khác.
     */
    public function test_update_all_returns_409_when_conflict_detected(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Sự kiện đơn cố định ngày 2 tháng 10 lúc 14:00 - 16:00
        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Sự Kiện Khác Ngày 2',
            'loai_su_kien'      => 'ca_nhan',
            'thoi_gian_bat_dau' => '2026-10-02 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-02 16:00:00',
        ]);

        // Nhóm lặp 3 ngày (Oct 1, 2, 3) từ 08:00 - 09:00
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/su-kien', [
                'tieu_de'           => 'Họp Nhóm Sáng',
                'loai_su_kien'      => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
                'quy_tac_lap'       => 'daily',
                'ngay_ket_thuc_lap' => '2026-10-03',
            ]);

        $recurringEvents = SuKien::where('user_id', $user->id)->whereNotNull('nhom_lap_id')->orderBy('thoi_gian_bat_dau')->get();
        $firstRec = $recurringEvents->first();

        // Cập nhật cả nhóm lặp chuyển sang khung giờ 15:00 - 17:00 (Trùng với Ngày 2 lúc 14:00-16:00)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/su-kien/' . $firstRec->id, [
                'thoi_gian_bat_dau'  => '2026-10-01 15:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 17:00:00',
                'update_mode'        => 'all',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'success'      => false,
                'has_conflict' => true,
                'message'      => 'Phát hiện lịch trình bị trùng.',
            ]);
    }
}
