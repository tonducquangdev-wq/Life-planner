<?php

namespace Tests\Feature\Api;

use App\Models\BaiTapTheChat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/exercises : User lấy danh sách danh mục bài tập thể chất thành công.
     */
    public function test_authenticated_user_can_get_exercise_catalog(): void
    {
        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Bench Press',
            'nhom_co'      => 'Ngực',
            'loai_bai_tap' => 'strength',
            'mo_ta'        => 'Phát triển ngực giữa',
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Squat',
            'nhom_co'      => 'Chân',
            'loai_bai_tap' => 'strength',
            'mo_ta'        => 'Phát triển đùi',
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách bài tập thể chất thành công!',
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonFragment(['ten_bai_tap' => 'Bench Press'])
            ->assertJsonFragment(['ten_bai_tap' => 'Squat']);
    }

    /**
     * 2. Guest truy cập API exercises nhận 401.
     */
    public function test_guest_cannot_access_exercise_catalog(): void
    {
        $this->getJson('/api/exercises')->assertStatus(401);
        $this->getJson('/api/exercises/1')->assertStatus(401);
    }

    /**
     * 3. GET /api/exercises?q=Bench : Tìm kiếm bài tập theo từ khóa.
     */
    public function test_user_can_search_exercises_by_keyword(): void
    {
        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Bench Press',
            'nhom_co'      => 'Ngực',
            'loai_bai_tap' => 'strength',
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Squat',
            'nhom_co'      => 'Chân',
            'loai_bai_tap' => 'strength',
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises?q=Bench');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['ten_bai_tap' => 'Bench Press'])
            ->assertJsonMissing(['ten_bai_tap' => 'Squat']);
    }

    /**
     * 4. GET /api/exercises?nhom_co=Ngực : Lọc bài tập theo nhóm cơ.
     */
    public function test_user_can_filter_exercises_by_muscle_group(): void
    {
        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Bench Press',
            'nhom_co'      => 'Ngực',
            'loai_bai_tap' => 'strength',
        ]);

        BaiTapTheChat::create([
            'ten_bai_tap'  => 'Squat',
            'nhom_co'      => 'Chân',
            'loai_bai_tap' => 'strength',
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises?nhom_co=Ngực');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['ten_bai_tap' => 'Bench Press'])
            ->assertJsonMissing(['ten_bai_tap' => 'Squat']);
    }

    /**
     * 5. GET /api/exercises?per_page=5 : Phân trang danh mục bài tập.
     */
    public function test_user_can_paginate_exercise_catalog(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            BaiTapTheChat::create([
                'ten_bai_tap'  => 'Bài tập ' . $i,
                'nhom_co'      => 'Nhóm ' . $i,
                'loai_bai_tap' => 'strength',
            ]);
        }

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total',
                    'per_page',
                ],
            ]);

        $this->assertEquals(5, count($response->json('data.data')));
        $this->assertEquals(10, $response->json('data.total'));
    }

    /**
     * 6. GET /api/exercises/{id} : Xem chi tiết một bài tập thành công.
     */
    public function test_user_can_view_single_exercise_detail(): void
    {
        $exercise = BaiTapTheChat::create([
            'ten_bai_tap'  => 'Pull Up',
            'nhom_co'      => 'Lưng',
            'loai_bai_tap' => 'strength',
            'mo_ta'        => 'Xô rộng',
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises/' . $exercise->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Chi tiết bài tập thể chất.',
                'data'    => [
                    'id'          => $exercise->id,
                    'ten_bai_tap' => 'Pull Up',
                    'nhom_co'     => 'Lưng',
                ],
            ]);
    }

    /**
     * 7. GET /api/exercises/{id} : Trả về 404 khi không tìm thấy bài tập.
     */
    public function test_viewing_non_existent_exercise_returns_404(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/exercises/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy bài tập thể chất.',
            ]);
    }
}
