<?php

namespace Tests\Feature\Api;

use App\Models\KeHoachTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutPlanApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/workout-plans : User lấy danh sách kế hoạch tập luyện của chính mình.
     */
    public function test_authenticated_user_can_get_their_workout_plans(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        KeHoachTapLuyen::create([
            'user_id'      => $userA->id,
            'ten_ke_hoach' => 'Tăng Cơ Toàn Thân A1',
            'mo_ta'        => 'Mô tả A1',
            'is_active'    => true,
        ]);

        KeHoachTapLuyen::create([
            'user_id'      => $userA->id,
            'ten_ke_hoach' => 'Giảm Mỡ Bụng A2',
            'mo_ta'        => 'Mô tả A2',
            'is_active'    => false,
        ]);

        KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế Hoạch Bí Mật Của B',
            'mo_ta'        => 'Mô tả B',
            'is_active'    => true,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/workout-plans');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách kế hoạch tập luyện thành công!',
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonFragment(['ten_ke_hoach' => 'Tăng Cơ Toàn Thân A1'])
            ->assertJsonFragment(['ten_ke_hoach' => 'Giảm Mỡ Bụng A2'])
            ->assertJsonMissing(['ten_ke_hoach' => 'Kế Hoạch Bí Mật Của B']);
    }

    /**
     * 2. Guest truy cập API workout-plans nhận 401.
     */
    public function test_guest_cannot_access_workout_plans_api(): void
    {
        $this->getJson('/api/workout-plans')->assertStatus(401);
        $this->postJson('/api/workout-plans', [])->assertStatus(401);
        $this->getJson('/api/workout-plans/1')->assertStatus(401);
        $this->putJson('/api/workout-plans/1', [])->assertStatus(401);
        $this->deleteJson('/api/workout-plans/1')->assertStatus(401);
    }

    /**
     * 3. POST /api/workout-plans : Tạo kế hoạch tập luyện mới thành công.
     */
    public function test_user_can_create_workout_plan_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $payload = [
            'ten_ke_hoach' => 'Lịch Tập 4 Buổi/Tuần',
            'mo_ta'        => 'Tăng sức bền và cơ bắp',
            'is_active'    => true,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/workout-plans', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'user_id'      => $user->id,
                    'ten_ke_hoach' => 'Lịch Tập 4 Buổi/Tuần',
                    'is_active'    => true,
                ],
            ]);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Lịch Tập 4 Buổi/Tuần',
        ]);
    }

    /**
     * 4. POST /api/workout-plans : Validation lỗi khi thiếu tên kế hoạch.
     */
    public function test_create_workout_plan_fails_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/workout-plans', [
                'ten_ke_hoach' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ten_ke_hoach']);
    }

    /**
     * 5. GET /api/workout-plans/{id} : Xem chi tiết kế hoạch của mình thành công.
     */
    public function test_user_can_view_single_workout_plan_detail(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế hoạch Chi Tiết',
            'mo_ta'        => 'Xem chi tiết',
            'is_active'    => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/workout-plans/' . $plan->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'           => $plan->id,
                    'ten_ke_hoach' => 'Kế hoạch Chi Tiết',
                ],
            ]);
    }

    /**
     * 6. GET /api/workout-plans/{id} : Anti-IDOR - Không xem được kế hoạch của người khác (404).
     */
    public function test_user_cannot_view_other_users_workout_plan(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế hoạch B',
            'is_active'    => true,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/workout-plans/' . $planB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * 7. PUT /api/workout-plans/{id} : User cập nhật kế hoạch của chính mình thành công.
     */
    public function test_user_can_update_their_own_workout_plan(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế hoạch Cũ',
            'mo_ta'        => 'Mô tả cũ',
            'is_active'    => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/workout-plans/' . $plan->id, [
                'ten_ke_hoach' => 'Kế hoạch Mới Đã Cập Nhật',
                'mo_ta'        => 'Mô tả mới',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật kế hoạch tập luyện thành công!',
                'data'    => [
                    'id'           => $plan->id,
                    'ten_ke_hoach' => 'Kế hoạch Mới Đã Cập Nhật',
                ],
            ]);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'id'           => $plan->id,
            'ten_ke_hoach' => 'Kế hoạch Mới Đã Cập Nhật',
        ]);
    }

    /**
     * 8. PUT /api/workout-plans/{id} : Anti-IDOR - Không cập nhật được kế hoạch của người khác (404).
     */
    public function test_user_cannot_update_other_users_workout_plan(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế hoạch B',
            'is_active'    => true,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/workout-plans/' . $planB->id, [
                'ten_ke_hoach' => 'Hack kế hoạch của B',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'id'           => $planB->id,
            'ten_ke_hoach' => 'Kế hoạch B',
        ]);
    }

    /**
     * 9. DELETE /api/workout-plans/{id} : User xóa kế hoạch của chính mình thành công.
     */
    public function test_user_can_delete_their_own_workout_plan(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế hoạch Sắp Xóa',
            'is_active'    => false,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/workout-plans/' . $plan->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa kế hoạch tập luyện thành công!',
            ]);

        $this->assertDatabaseMissing('ke_hoach_tap_luyen', [
            'id' => $plan->id,
        ]);
    }

    /**
     * 10. DELETE /api/workout-plans/{id} : Anti-IDOR - Không xóa được kế hoạch của người khác (404).
     */
    public function test_user_cannot_delete_other_users_workout_plan(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế hoạch B',
            'is_active'    => true,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/workout-plans/' . $planB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'id' => $planB->id,
        ]);
    }
}
