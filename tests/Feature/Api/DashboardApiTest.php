<?php

namespace Tests\Feature\Api;

use App\Models\BaiTap;
use App\Models\MonHoc;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Guest gọi API nhận 401 Unauthorized.
     */
    public function test_guest_cannot_access_dashboard_api(): void
    {
        $this->getJson('/api/dashboard')->assertStatus(401);
    }

    /**
     * 2. User đăng nhập lấy dữ liệu Action-First Dashboard thành công.
     */
    public function test_user_can_get_action_first_dashboard_data(): void
    {
        $user = User::factory()->create(['ho_ten' => 'Quang']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $now = Carbon::now();

        // Sự kiện tiếp theo hôm nay
        SuKien::create([
            'user_id'           => $user->id,
            'tieu_de'           => 'Học Cơ Sở Dữ Liệu',
            'loai_su_kien'      => 'hoc_tap',
            'thoi_gian_bat_dau' => $now->copy()->addMinutes(15)->toDateTimeString(),
            'thoi_gian_ket_thuc' => $now->copy()->addMinutes(105)->toDateTimeString(),
        ]);

        // Môn học & Deadline nguy hiểm (sắp nộp trong 2 ngày)
        $monHoc = MonHoc::create([
            'user_id'    => $user->id,
            'ten_mon'    => 'Cơ Sở Dữ Liệu',
            'ma_mon'     => 'CSDL101',
            'so_tin_chi' => 3,
        ]);

        BaiTap::create([
            'mon_hoc_id' => $monHoc->id,
            'tieu_de'    => 'Báo cáo CSDL',
            'han_nop'    => $now->copy()->addDays(2)->toDateTimeString(),
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy dữ liệu Action-First Dashboard thành công!',
                'data'    => [
                    'next_action' => [
                        'status_type' => 'next_up',
                        'tieu_de'     => 'Học Cơ Sở Dữ Liệu',
                    ],
                    'urgent_deadline' => [
                        'tieu_de' => 'Báo cáo CSDL',
                    ],
                ],
            ]);
    }
}
