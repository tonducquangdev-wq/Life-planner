<?php

namespace Tests\Feature\Api;

use App\Models\ThongBao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/thong-bao: User lấy danh sách thông báo của chính mình thành công.
     */
    public function test_authenticated_user_can_get_their_notifications(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Tạo 2 thông báo cho user A
        $thongBaoA1 = ThongBao::create([
            'user_id'        => $userA->id,
            'tieu_de'        => 'Thông báo A1',
            'noi_dung'       => 'Nội dung thông báo A1',
            'loai_thong_bao' => 'deadline',
            'da_doc'         => false,
        ]);

        $thongBaoA2 = ThongBao::create([
            'user_id'        => $userA->id,
            'tieu_de'        => 'Thông báo A2',
            'noi_dung'       => 'Nội dung thông báo A2',
            'loai_thong_bao' => 'lich_hoc',
            'da_doc'         => true,
        ]);

        // Tạo 1 thông báo cho user B
        ThongBao::create([
            'user_id'        => $userB->id,
            'tieu_de'        => 'Thông báo B1',
            'noi_dung'       => 'Nội dung thông báo B1',
            'loai_thong_bao' => 'he_thong',
            'da_doc'         => false,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/thong-bao');

        $response->assertStatus(200)
            ->assertJson([
                'success'      => true,
                'message'      => 'Lấy danh sách thông báo thành công!',
                'unread_count' => 1,
            ])
            ->assertJsonCount(2, 'data');

        // Verify không lẫn thông báo của User B
        $response->assertJsonFragment(['tieu_de' => 'Thông báo A1'])
            ->assertJsonFragment(['tieu_de' => 'Thông báo A2'])
            ->assertJsonMissing(['tieu_de' => 'Thông báo B1']);
    }

    /**
     * 2. GET /api/thong-bao: Guest gọi API bị từ chối 401.
     */
    public function test_guest_cannot_get_notifications(): void
    {
        $response = $this->getJson('/api/thong-bao');

        $response->assertStatus(401);
    }

    /**
     * 3. PATCH /api/thong-bao/{id}/read: User đánh dấu 1 thông báo là đã đọc thành công.
     */
    public function test_user_can_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $thongBao = ThongBao::create([
            'user_id'        => $user->id,
            'tieu_de'        => 'Thông báo chưa đọc',
            'noi_dung'       => 'Nội dung',
            'loai_thong_bao' => 'tap_luyen',
            'da_doc'         => false,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/thong-bao/' . $thongBao->id . '/read');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đánh dấu thông báo đã đọc thành công!',
                'data'    => [
                    'id'     => $thongBao->id,
                    'da_doc' => true,
                ],
            ]);

        $this->assertDatabaseHas('thong_bao', [
            'id'     => $thongBao->id,
            'da_doc' => 1,
        ]);
    }

    /**
     * 4. PATCH /api/thong-bao/{id}/read: Không được đánh dấu đọc thông báo của User khác (trả về 404).
     */
    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $thongBaoB = ThongBao::create([
            'user_id'        => $userB->id,
            'tieu_de'        => 'Thông báo của B',
            'noi_dung'       => 'B bí mật',
            'loai_thong_bao' => 'he_thong',
            'da_doc'         => false,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->patchJson('/api/thong-bao/' . $thongBaoB->id . '/read');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        // Trạng thái thông báo của User B giữ nguyên chưa đọc
        $this->assertDatabaseHas('thong_bao', [
            'id'     => $thongBaoB->id,
            'da_doc' => 0,
        ]);
    }

    /**
     * 5. PATCH /api/thong-bao/read-all: Đánh dấu tất cả thông báo của user hiện tại đã đọc.
     */
    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // 3 thông báo chưa đọc của User A
        ThongBao::create([
            'user_id'        => $userA->id,
            'tieu_de'        => 'A1',
            'noi_dung'       => 'Nội dung A1',
            'loai_thong_bao' => 'deadline',
            'da_doc'         => false,
        ]);
        ThongBao::create([
            'user_id'        => $userA->id,
            'tieu_de'        => 'A2',
            'noi_dung'       => 'Nội dung A2',
            'loai_thong_bao' => 'lich_hoc',
            'da_doc'         => false,
        ]);
        ThongBao::create([
            'user_id'        => $userA->id,
            'tieu_de'        => 'A3',
            'noi_dung'       => 'Nội dung A3',
            'loai_thong_bao' => 'tap_luyen',
            'da_doc'         => false,
        ]);

        // 1 thông báo chưa đọc của User B
        $thongBaoB = ThongBao::create([
            'user_id'        => $userB->id,
            'tieu_de'        => 'B1',
            'noi_dung'       => 'Nội dung B1',
            'loai_thong_bao' => 'he_thong',
            'da_doc'         => false,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->patchJson('/api/thong-bao/read-all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã đánh dấu tất cả thông báo là đã đọc!',
                'data'    => [
                    'updated_count' => 3,
                ],
            ]);

        // Kiểm tra User A cả 3 thông báo đều đã đọc
        $this->assertEquals(0, ThongBao::where('user_id', $userA->id)->where('da_doc', false)->count());

        // Kiểm tra User B thông báo vẫn giữ nguyên chưa đọc
        $this->assertDatabaseHas('thong_bao', [
            'id'     => $thongBaoB->id,
            'da_doc' => 0,
        ]);
    }

    /**
     * 6. Guest không được phép đánh dấu đọc thông báo.
     */
    public function test_guest_cannot_mark_notifications_as_read(): void
    {
        $responseRead = $this->patchJson('/api/thong-bao/1/read');
        $responseRead->assertStatus(401);

        $responseReadAll = $this->patchJson('/api/thong-bao/read-all');
        $responseReadAll->assertStatus(401);
    }
}
