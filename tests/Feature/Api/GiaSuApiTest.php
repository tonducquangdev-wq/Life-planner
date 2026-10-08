<?php

namespace Tests\Feature\Api;

use App\Models\GiaSu;
use App\Models\MonHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiaSuApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/gia-su : User đăng nhập lấy danh sách gia sư thành công.
     */
    public function test_authenticated_user_can_get_tutors_list(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        GiaSu::create([
            'ho_ten'           => 'Nguyễn Văn Anh',
            'chuyen_mon'       => 'Lập trình Laravel, Vue.js',
            'hoc_phi_theo_gio' => 150000,
            'danh_gia'         => 4.8,
            'so_danh_gia'      => 12,
        ]);

        GiaSu::create([
            'ho_ten'           => 'Trần Thị Bình',
            'chuyen_mon'       => 'Cơ sở dữ liệu, SQL',
            'hoc_phi_theo_gio' => 200000,
            'danh_gia'         => 5.0,
            'so_danh_gia'      => 20,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/gia-su');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách gia sư thành công!',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'ho_ten',
                            'chuyen_mon',
                            'hoc_phi_theo_gio',
                            'hoc_phi_formatted',
                            'avatar_url',
                            'danh_gia',
                        ],
                    ],
                    'total',
                ],
            ]);

        $this->assertEquals(2, $response->json('data.total'));
    }

    /**
     * 2. GET /api/gia-su?q=Laravel : Tìm kiếm gia sư theo từ khóa (tên, chuyên môn).
     */
    public function test_user_can_search_tutors_by_keyword(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        GiaSu::create([
            'ho_ten'           => 'Nguyễn Văn Anh',
            'chuyen_mon'       => 'Chuyên gia Laravel 13',
            'hoc_phi_theo_gio' => 150000,
            'danh_gia'         => 4.8,
        ]);

        GiaSu::create([
            'ho_ten'           => 'Trần Thị Bình',
            'chuyen_mon'       => 'Chuyên gia Tiếng Anh',
            'hoc_phi_theo_gio' => 200000,
            'danh_gia'         => 5.0,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/gia-su?q=Laravel');

        $response->assertStatus(200)
            ->assertJsonFragment(['ho_ten' => 'Nguyễn Văn Anh'])
            ->assertJsonMissing(['ho_ten' => 'Trần Thị Bình']);
    }

    /**
     * 3. GET /api/gia-su?mon_hoc_id=X : Lọc gia sư theo môn học.
     */
    public function test_user_can_filter_tutors_by_mon_hoc_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $monHoc = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT3306',
            'ten_mon'    => 'Lập trình Web',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        GiaSu::create([
            'ho_ten'           => 'Gia Sư Môn Web',
            'mon_hoc_id'       => $monHoc->id,
            'hoc_phi_theo_gio' => 180000,
        ]);

        GiaSu::create([
            'ho_ten'           => 'Gia Sư Khác',
            'mon_hoc_id'       => null,
            'hoc_phi_theo_gio' => 150000,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/gia-su?mon_hoc_id=' . $monHoc->id);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total'));
        $response->assertJsonFragment(['ho_ten' => 'Gia Sư Môn Web']);
    }

    /**
     * 4. GET /api/gia-su?per_page=2 : Kiểm tra phân trang hoạt động chuẩn.
     */
    public function test_tutors_list_is_paginated(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        for ($i = 1; $i <= 5; $i++) {
            GiaSu::create([
                'ho_ten'           => "Gia Sư {$i}",
                'hoc_phi_theo_gio' => 150000,
            ]);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/gia-su?per_page=2&page=1');

        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('data.total'));
        $this->assertEquals(2, count($response->json('data.data')));
        $this->assertEquals(3, $response->json('data.last_page'));
    }

    /**
     * 5. Guest gọi API gia sư bị từ chối 401.
     */
    public function test_guest_cannot_access_gia_su_api(): void
    {
        $this->getJson('/api/gia-su')->assertStatus(401);
    }
}
