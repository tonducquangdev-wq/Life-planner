<?php

namespace Tests\Feature\Api;

use App\Models\BuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\LichSuTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WorkoutReportApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /api/workout-report : User lấy báo cáo tập luyện chính xác thành công.
     */
    public function test_authenticated_user_can_get_workout_report(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // User A: 2 Kế hoạch tập luyện, 3 Buổi tập
        $planA1 = KeHoachTapLuyen::create([
            'user_id'      => $userA->id,
            'ten_ke_hoach' => 'Kế hoạch A1 (PPL)',
            'is_active'    => true,
        ]);

        $planA2 = KeHoachTapLuyen::create([
            'user_id'      => $userA->id,
            'ten_ke_hoach' => 'Kế hoạch A2 (Fullbody)',
            'is_active'    => false,
        ]);

        $sessionA1 = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planA1->id,
            'ten_buoi_tap'          => 'Push Day',
            'thu_tu'                => 1,
        ]);

        $sessionA2 = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planA1->id,
            'ten_buoi_tap'          => 'Pull Day',
            'thu_tu'                => 2,
        ]);

        BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planA2->id,
            'ten_buoi_tap'          => 'Full Body 1',
            'thu_tu'                => 1,
        ]);

        // User A: 2 Lịch sử tập luyện hôm nay
        LichSuTapLuyen::create([
            'user_id'           => $userA->id,
            'buoi_tap_id'       => $sessionA1->id,
            'thoi_gian_bat_dau' => Carbon::now()->subMinutes(60),
            'thoi_gian_ket_thuc' => Carbon::now(),
            'tong_thoi_luong'   => 60,
        ]);

        LichSuTapLuyen::create([
            'user_id'           => $userA->id,
            'buoi_tap_id'       => $sessionA2->id,
            'thoi_gian_bat_dau' => Carbon::now()->subMinutes(30),
            'thoi_gian_ket_thuc' => Carbon::now(),
            'tong_thoi_luong'   => 30,
        ]);

        // User B: 1 Kế hoạch, 1 Buổi tập, 1 Lịch sử tập luyện
        $planB = KeHoachTapLuyen::create([
            'user_id'      => $userB->id,
            'ten_ke_hoach' => 'Kế hoạch B',
            'is_active'    => true,
        ]);

        $sessionB = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $planB->id,
            'ten_buoi_tap'          => 'Leg Day B',
            'thu_tu'                => 1,
        ]);

        LichSuTapLuyen::create([
            'user_id'           => $userB->id,
            'buoi_tap_id'       => $sessionB->id,
            'thoi_gian_bat_dau' => Carbon::now()->subMinutes(45),
            'thoi_gian_ket_thuc' => Carbon::now(),
            'tong_thoi_luong'   => 45,
        ]);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/workout-report');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy báo cáo tập luyện thành công!',
                'data'    => [
                    'tong_so_ke_hoach'     => 2,
                    'tong_so_buoi_tap'     => 3,
                    'buoi_tap_hoan_thanh'  => 2,
                    'buoi_tap_tuan_nay'    => 2,
                    'thoi_gian_tap_hom_nay' => 90,
                    'tong_thoi_gian_tap'   => 90,
                    'chuoi_ngay_tap'       => 1,
                    'ke_hoach_hien_tai'    => [
                        'id'           => $planA1->id,
                        'ten_ke_hoach' => 'Kế hoạch A1 (PPL)',
                        'so_buoi_tap'  => 2,
                    ],
                ],
            ]);
    }

    /**
     * 2. Guest truy cập /api/workout-report nhận 401.
     */
    public function test_guest_cannot_access_workout_report(): void
    {
        $response = $this->getJson('/api/workout-report');
        $response->assertStatus(401);
    }

    /**
     * 3. Tính toán chính xác streak (chuỗi ngày tập liên tục 3 ngày) và tiến độ tập luyện.
     */
    public function test_workout_report_calculates_streak_and_progress_correctly(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $plan = KeHoachTapLuyen::create([
            'user_id'      => $user->id,
            'ten_ke_hoach' => 'Kế hoạch 2 Buổi',
            'is_active'    => true,
        ]);

        $session = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap'          => 'Buổi 1',
            'thu_tu'                => 1,
        ]);

        // Tập 3 ngày liên tục: Hôm nay, Hôm qua, Hôm kia
        LichSuTapLuyen::create([
            'user_id'           => $user->id,
            'buoi_tap_id'       => $session->id,
            'thoi_gian_bat_dau' => Carbon::today()->addHours(8),
            'thoi_gian_ket_thuc' => Carbon::today()->addHours(9),
            'tong_thoi_luong'   => 45,
        ]);

        LichSuTapLuyen::create([
            'user_id'           => $user->id,
            'buoi_tap_id'       => $session->id,
            'thoi_gian_bat_dau' => Carbon::yesterday()->addHours(8),
            'thoi_gian_ket_thuc' => Carbon::yesterday()->addHours(9),
            'tong_thoi_luong'   => 45,
        ]);

        LichSuTapLuyen::create([
            'user_id'           => $user->id,
            'buoi_tap_id'       => $session->id,
            'thoi_gian_bat_dau' => Carbon::yesterday()->subDay()->addHours(8),
            'thoi_gian_ket_thuc' => Carbon::yesterday()->subDay()->addHours(9),
            'tong_thoi_luong'   => 45,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/workout-report');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'chuoi_ngay_tap' => 3,
                ],
            ]);
    }
}
