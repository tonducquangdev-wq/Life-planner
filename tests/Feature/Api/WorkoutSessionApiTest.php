<?php

namespace Tests\Feature\Api;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutSessionApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/workout-sessions : User lấy danh sách buổi tập của chính mình.
     */
    public function test_authenticated_user_can_get_their_workout_sessions(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planA = KeHoachTapLuyen::create([
            'user_id'      => $userA->id,
            'ten_ke_hoach' => 'Kế Hoạch A',
            'is_active'    => true,
        ]);

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch B',
            'is_active'    => true,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planA->id,
            'ten_buoi_tap'          => 'Buổi 1: Ngực & Tay Sau',
            'thu_tu'                => 1,
            'ngay_trong_tuan'       => 1,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planA->id,
            'ten_buoi_tap'          => 'Buổi 2: Lưng & Tay Trước',
            'thu_tu'                => 2,
            'ngay_trong_tuan'       => 3,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planB->id,
            'ten_buoi_tap'          => 'Buổi Riêng Của B',
            'thu_tu'                => 1,
            'ngay_trong_tuan'       => 2,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/workout-sessions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách buổi tập thành công!',
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonFragment(['ten_buoi_tap' => 'Buổi 1: Ngực & Tay Sau'])
            ->assertJsonFragment(['ten_buoi_tap' => 'Buổi 2: Lưng & Tay Trước'])
            ->assertJsonMissing(['ten_buoi_tap' => 'Buổi Riêng Của B']);
    }

    /**
     * 2. Guest truy cập API workout-sessions nhận 401.
     */
    public function test_guest_cannot_access_workout_sessions_api(): void
    {
        $this->getJson('/api/workout-sessions')->assertStatus(401);
        $this->postJson('/api/workout-sessions', [])->assertStatus(401);
        $this->getJson('/api/workout-sessions/1')->assertStatus(401);
        $this->putJson('/api/workout-sessions/1', [])->assertStatus(401);
        $this->deleteJson('/api/workout-sessions/1')->assertStatus(401);
    }

    /**
     * 3. POST /api/workout-sessions : Tạo buổi tập mới thành công.
     */
    public function test_user_can_create_workout_session_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế Hoạch Tập Luyện',
            'is_active'    => true,
        ]);

        $payload = [
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi 3: Chân & Vai',
            'mo_ta'                 => 'Tập nặng squat',
            'thu_tu'                => 1,
            'ngay_trong_tuan'       => 5,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/workout-sessions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo buổi tập thành công!',
                'data'    => [
                    'ke_hoach_tap_luyen_id' => $plan->id,
                    'ten_buoi_tap'          => 'Buổi 3: Chân & Vai',
                    'ngay_trong_tuan'       => 5,
                ],
            ]);

        $this->assertDatabaseHas('buoi_tap', [
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi 3: Chân & Vai',
        ]);
    }

    /**
     * 4. POST /api/workout-sessions : Thất bại khi tạo buổi tập với plan không thuộc sở hữu của mình.
     */
    public function test_create_workout_session_fails_if_plan_belongs_to_another_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch B',
            'is_active'    => true,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->postJson('/api/workout-sessions', [
                'ke_hoach_tap_luyen_id' => $planB->id,
                'ten_buoi_tap'          => 'Buổi Hack',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * 5. POST /api/workout-sessions : Validation lỗi khi thiếu tên buổi tập hoặc ke_hoach_tap_luyen_id.
     */
    public function test_create_workout_session_fails_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/workout-sessions', [
                'ten_buoi_tap' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ke_hoach_tap_luyen_id', 'ten_buoi_tap']);
    }

    /**
     * 6. GET /api/workout-sessions/{id} : Xem chi tiết buổi tập của mình thành công.
     */
    public function test_user_can_view_single_workout_session_detail(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế Hoạch Xem',
            'is_active'    => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi Chi Tiết',
            'thu_tu'                => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/workout-sessions/' . $session->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'           => $session->id,
                    'ten_buoi_tap' => 'Buổi Chi Tiết',
                ],
            ]);
    }

    /**
     * 7. GET /api/workout-sessions/{id} : Anti-IDOR - Không xem được buổi tập của người khác (404).
     */
    public function test_user_cannot_view_other_users_workout_session(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch B',
            'is_active'    => true,
        ]);

        $sessionB = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planB->id,
            'ten_buoi_tap'          => 'Buổi Của B',
            'thu_tu'                => 1,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/workout-sessions/' . $sessionB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * 8. PUT /api/workout-sessions/{id} : User cập nhật buổi tập của chính mình thành công.
     */
    public function test_user_can_update_their_own_workout_session(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế Hoạch Cập Nhật',
            'is_active'    => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi Cũ',
            'thu_tu'                => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/workout-sessions/' . $session->id, [
                'ten_buoi_tap'    => 'Buổi Đã Đổi Tên',
                'ngay_trong_tuan' => 4,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật buổi tập thành công!',
                'data'    => [
                    'id'           => $session->id,
                    'ten_buoi_tap' => 'Buổi Đã Đổi Tên',
                ],
            ]);

        $this->assertDatabaseHas('buoi_tap', [
            'id'           => $session->id,
            'ten_buoi_tap' => 'Buổi Đã Đổi Tên',
        ]);
    }

    /**
     * 9. PUT /api/workout-sessions/{id} : Anti-IDOR - Không cập nhật được buổi tập của người khác (404).
     */
    public function test_user_cannot_update_other_users_workout_session(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch B',
            'is_active'    => true,
        ]);

        $sessionB = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planB->id,
            'ten_buoi_tap'          => 'Buổi B',
            'thu_tu'                => 1,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/workout-sessions/' . $sessionB->id, [
                'ten_buoi_tap' => 'Hack buổi tập của B',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('buoi_tap', [
            'id'           => $sessionB->id,
            'ten_buoi_tap' => 'Buổi B',
        ]);
    }

    /**
     * 10. DELETE /api/workout-sessions/{id} : User xóa buổi tập của chính mình thành công.
     */
    public function test_user_can_delete_their_own_workout_session(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế Hoạch Xóa',
            'is_active'    => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi Sắp Xóa',
            'thu_tu'                => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/workout-sessions/' . $session->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa buổi tập thành công!',
            ]);

        $this->assertDatabaseMissing('buoi_tap', [
            'id' => $session->id,
        ]);
    }

    /**
     * 11. DELETE /api/workout-sessions/{id} : Anti-IDOR - Không xóa được buổi tập của người khác (404).
     */
    public function test_user_cannot_delete_other_users_workout_session(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch B',
            'is_active'    => true,
        ]);

        $sessionB = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planB->id,
            'ten_buoi_tap'          => 'Buổi Của B',
            'thu_tu'                => 1,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/workout-sessions/' . $sessionB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('buoi_tap', [
            'id' => $sessionB->id,
        ]);
    }
}
