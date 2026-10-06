<?php

namespace Tests\Feature\Api;

use App\Models\MonHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonHocApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/mon-hoc : User lấy danh sách môn học của chính mình thành công.
     */
    public function test_authenticated_user_can_get_their_subjects(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        MonHoc::create([
            'user_id'    => $userA->id,
            'ma_mon'     => 'INT3306',
            'ten_mon'    => 'Lập trình Web',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        MonHoc::create([
            'user_id'    => $userA->id,
            'ma_mon'     => 'INT2204',
            'ten_mon'    => 'Cơ sở dữ liệu',
            'so_tin_chi' => 4,
            'trang_thai' => 'da_hoan_thanh',
        ]);

        MonHoc::create([
            'user_id'    => $userB->id,
            'ma_mon'     => 'ENG101',
            'ten_mon'    => 'Tiếng Anh B riêng của B',
            'so_tin_chi' => 2,
            'trang_thai' => 'dang_hoc',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/mon-hoc');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy danh sách môn học thành công!',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'ten_mon_hoc',
                        'so_tin_chi',
                        'diem_hien_tai',
                        'hoc_ky',
                        'nam_hoc',
                    ],
                ],
            ]);

        $response->assertJsonFragment(['ma_mon' => 'INT3306', 'ten_mon_hoc' => 'Lập trình Web'])
            ->assertJsonFragment(['ma_mon' => 'INT2204', 'ten_mon_hoc' => 'Cơ sở dữ liệu'])
            ->assertJsonMissing(['ma_mon' => 'ENG101']);
    }

    /**
     * 2. Guest gọi API môn học bị từ chối 401.
     */
    public function test_guest_cannot_access_mon_hoc_api(): void
    {
        $this->getJson('/api/mon-hoc')->assertStatus(401);
        $this->postJson('/api/mon-hoc', [])->assertStatus(401);
        $this->getJson('/api/mon-hoc/1')->assertStatus(401);
        $this->putJson('/api/mon-hoc/1', [])->assertStatus(401);
        $this->deleteJson('/api/mon-hoc/1')->assertStatus(401);
    }

    /**
     * 3. POST /api/mon-hoc : User tạo môn học mới thành công.
     */
    public function test_user_can_create_subject_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $payload = [
            'ma_mon'       => 'INT3401',
            'ten_mon'      => 'An toàn thông tin',
            'giang_vien'   => 'TS. Phạm Hoàng D',
            'phong_hoc'    => 'Phòng B2.01',
            'so_tin_chi'   => 3,
            'tien_do'      => 40,
            'diem_so'      => 8.5,
            'ngay_bat_dau' => '2026-09-01',
            'ngay_ket_thuc' => '2026-12-31',
            'mau_sac'      => '#ec4899',
            'trang_thai'   => 'dang_hoc',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/mon-hoc', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm môn học thành công!',
                'data'    => [
                    'user_id'    => $user->id,
                    'ma_mon'     => 'INT3401',
                    'ten_mon'    => 'An toàn thông tin',
                    'so_tin_chi' => 3,
                ],
            ]);

        $this->assertDatabaseHas('mon_hoc', [
            'user_id' => $user->id,
            'ma_mon'  => 'INT3401',
        ]);
    }

    /**
     * 4. POST /api/mon-hoc : Tự động gán user_id, bỏ qua user_id client tự gửi.
     */
    public function test_user_id_from_request_body_is_ignored(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $payload = [
            'user_id'    => $otherUser->id, // Client cố tình giả mạo user_id
            'ma_mon'     => 'INT9999',
            'ten_mon'    => 'Môn Học Test Chặn Spoofing',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/mon-hoc', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm môn học thành công!',
                'data'    => [
                    'user_id' => $user->id, // Phải luôn bằng ID user đang đăng nhập
                ],
            ]);

        $this->assertDatabaseHas('mon_hoc', [
            'user_id' => $user->id,
            'ma_mon'  => 'INT9999',
        ]);
        $this->assertDatabaseMissing('mon_hoc', [
            'user_id' => $otherUser->id,
            'ma_mon'  => 'INT9999',
        ]);
    }

    /**
     * 5. POST /api/mon-hoc : Lỗi validation khi thiếu trường bắt buộc.
     */
    public function test_create_subject_fails_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/mon-hoc', [
                'ma_mon'     => '',
                'ten_mon'    => '',
                'so_tin_chi' => 0, // Nhỏ hơn min:1
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ma_mon', 'ten_mon', 'so_tin_chi']);
    }

    /**
     * 5. GET /api/mon-hoc/{id} : Xem chi tiết môn học thành công.
     */
    public function test_user_can_view_single_subject_detail(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $monHoc = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT3110',
            'ten_mon'    => 'Kiến trúc phần mềm',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/mon-hoc/' . $monHoc->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'     => $monHoc->id,
                    'ma_mon' => 'INT3110',
                ],
            ]);
    }

    /**
     * 6. GET /api/mon-hoc/{id} : Anti-IDOR - Không thể xem môn học của người khác (404).
     */
    public function test_user_cannot_view_other_users_subject(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $monHocB = MonHoc::create([
            'user_id'    => $userB->id,
            'ma_mon'     => 'ENG201',
            'ten_mon'    => 'Môn riêng của B',
            'so_tin_chi' => 2,
            'trang_thai' => 'dang_hoc',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/mon-hoc/' . $monHocB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * 7. PUT /api/mon-hoc/{id} : User cập nhật môn học của chính mình thành công.
     */
    public function test_user_can_update_their_own_subject(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $monHoc = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT3306',
            'ten_mon'    => 'Môn Cũ',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/mon-hoc/' . $monHoc->id, [
                'ten_mon'  => 'Môn Đã Cập Nhật',
                'tien_do'  => 100,
                'diem_so'  => 9.5,
                'trang_thai' => 'da_hoan_thanh',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật môn học thành công!',
                'data'    => [
                    'id'         => $monHoc->id,
                    'ten_mon'    => 'Môn Đã Cập Nhật',
                    'trang_thai' => 'da_hoan_thanh',
                ],
            ]);

        $this->assertDatabaseHas('mon_hoc', [
            'id'         => $monHoc->id,
            'ten_mon'    => 'Môn Đã Cập Nhật',
            'trang_thai' => 'da_hoan_thanh',
        ]);
    }

    /**
     * 8. PUT /api/mon-hoc/{id} : Anti-IDOR - Không thể cập nhật môn học của người khác (404).
     */
    public function test_user_cannot_update_other_users_subject(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $monHocB = MonHoc::create([
            'user_id'    => $userB->id,
            'ma_mon'     => 'ENG201',
            'ten_mon'    => 'Tiếng Anh B',
            'so_tin_chi' => 2,
            'trang_thai' => 'dang_hoc',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/mon-hoc/' . $monHocB->id, [
                'ten_mon' => 'Hack môn của B',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('mon_hoc', [
            'id'      => $monHocB->id,
            'ten_mon' => 'Tiếng Anh B',
        ]);
    }

    /**
     * 9. DELETE /api/mon-hoc/{id} : User xóa môn học của chính mình thành công (Soft Delete).
     */
    public function test_user_can_delete_their_own_subject(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $monHoc = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT3306',
            'ten_mon'    => 'Môn Sắp Xóa',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/mon-hoc/' . $monHoc->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa môn học thành công!',
            ]);

        $this->assertSoftDeleted('mon_hoc', [
            'id' => $monHoc->id,
        ]);
    }

    /**
     * 10. DELETE /api/mon-hoc/{id} : Anti-IDOR - Không thể xóa môn học của người khác (404).
     */
    public function test_user_cannot_delete_other_users_subject(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $monHocB = MonHoc::create([
            'user_id'    => $userB->id,
            'ma_mon'     => 'ENG201',
            'ten_mon'    => 'Môn của B',
            'so_tin_chi' => 2,
            'trang_thai' => 'dang_hoc',
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/mon-hoc/' . $monHocB->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('mon_hoc', [
            'id'         => $monHocB->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * 12. GET /api/bao-cao-hoc-tap : User lấy báo cáo học tập thành công với đầy đủ các chỉ số GPA, tín chỉ, học lực.
     */
    public function test_authenticated_user_can_get_study_report(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $m1 = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT3306',
            'ten_mon'    => 'Lập trình Web',
            'so_tin_chi' => 3,
            'diem_so'    => 9.0,
            'trang_thai' => 'da_hoan_thanh',
        ]);

        $m2 = MonHoc::create([
            'user_id'    => $user->id,
            'ma_mon'     => 'INT2204',
            'ten_mon'    => 'Cơ sở dữ liệu',
            'so_tin_chi' => 4,
            'diem_so'    => 8.5,
            'trang_thai' => 'da_hoan_thanh',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/bao-cao-hoc-tap');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy báo cáo học tập thành công!',
                'data'    => [
                    'gpa'                => 4.0,
                    'gpa_10'             => 8.71,
                    'gpa_4'              => 4.0,
                    'tong_tin_chi'       => 7,
                    'tin_chi_hoan_thanh' => 7,
                    'hoc_luc'            => 'Xuất sắc',
                    'tong_mon_hoc'       => 2,
                    'so_mon_hoan_thanh'  => 2,
                ],
            ]);
    }

    /**
     * 13. GET /api/bao-cao-hoc-tap : Guest truy cập báo cáo học tập bị từ chối 401.
     */
    public function test_guest_cannot_access_study_report(): void
    {
        $this->getJson('/api/bao-cao-hoc-tap')->assertStatus(401);
    }
}
