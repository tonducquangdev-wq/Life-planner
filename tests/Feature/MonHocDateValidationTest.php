<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MonHoc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonHocDateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_mon_hoc_store_validation_cases(): void
    {
        $user = User::factory()->create();

        // CASE 1: Ngày bắt đầu: 2026-09-27, Ngày kết thúc: 2026-09-28 -> HỢP LỆ
        $res1 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH01',
            'ten_mon' => 'Toán cao cấp 1',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '2026-09-28',
        ]);
        $res1->assertSessionHasNoErrors();
        $res1->assertRedirect(route('mon-hoc.index'));

        // CASE 2: Ngày bắt đầu: 2026-09-27, Ngày kết thúc: 2026-09-27 -> HỢP LỆ (Bằng nhau)
        $res2 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH02',
            'ten_mon' => 'Toán cao cấp 2',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res2->assertSessionHasNoErrors();
        $res2->assertRedirect(route('mon-hoc.index'));

        // CASE 3: Ngày bắt đầu: 2026-09-28, Ngày kết thúc: 2026-09-27 -> KHÔNG HỢP LỆ
        $res3 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH03',
            'ten_mon' => 'Toán cao cấp 3',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-28',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res3->assertSessionHasErrors(['ngay_ket_thuc']);

        // CASE 4: Ngày bắt đầu: 2026-09-27, Ngày kết thúc: trống -> HỢP LỆ
        $res4 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH04',
            'ten_mon' => 'Toán cao cấp 4',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '',
        ]);
        $res4->assertSessionHasNoErrors();
        $res4->assertRedirect(route('mon-hoc.index'));

        // CASE 5: Ngày bắt đầu: trống, Ngày kết thúc: 2026-09-27 -> HỢP LỆ
        $res5 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH05',
            'ten_mon' => 'Toán cao cấp 5',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res5->assertSessionHasNoErrors();
        $res5->assertRedirect(route('mon-hoc.index'));

        // Format d/m/Y: 27/09/2026 -> 28/09/2026 -> HỢP LỆ
        $res6 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH06',
            'ten_mon' => 'Toán cao cấp 6',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '27/09/2026',
            'ngay_ket_thuc' => '28/09/2026',
        ]);
        $res6->assertSessionHasNoErrors();

        // Format d/m/Y (May 10 to Oct 05): 10/05/2026 -> 05/10/2026 -> HỢP LỆ (Không bị nhầm tháng thành Oct > May)
        $res7 = $this->actingAs($user)->post('/mon-hoc', [
            'ma_mon' => 'MH07',
            'ten_mon' => 'Toán cao cấp 7',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '10/05/2026',
            'ngay_ket_thuc' => '05/10/2026',
        ]);
        $res7->assertSessionHasNoErrors();
    }

    public function test_all_mon_hoc_update_validation_cases(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'MHEdit',
            'ten_mon' => 'Môn Học Edit',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '2026-09-28',
        ]);

        // 1. Update hợp lệ (End = Start)
        $res1 = $this->actingAs($user)->put("/mon-hoc/{$monHoc->id}", [
            'ma_mon' => 'MHEdit',
            'ten_mon' => 'Môn Học Edit',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res1->assertSessionHasNoErrors();

        // 2. Update không hợp lệ (End < Start)
        $res2 = $this->actingAs($user)->put("/mon-hoc/{$monHoc->id}", [
            'ma_mon' => 'MHEdit',
            'ten_mon' => 'Môn Học Edit',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-09-28',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res2->assertSessionHasErrors(['ngay_ket_thuc']);

        // 3. Update với ngày bắt đầu trống, ngày kết thúc có
        $res3 = $this->actingAs($user)->put("/mon-hoc/{$monHoc->id}", [
            'ma_mon' => 'MHEdit',
            'ten_mon' => 'Môn Học Edit',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res3->assertSessionHasNoErrors();
    }

    public function test_mon_hoc_api_date_scenarios(): void
    {
        $user = User::factory()->create();

        // API POST: Start 2026-09-27, End 2026-09-28 -> 201
        $res1 = $this->actingAs($user, 'sanctum')->postJson('/api/mon-hoc', [
            'ma_mon' => 'API01',
            'ten_mon' => 'Môn Học API',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-09-27',
            'ngay_ket_thuc' => '2026-09-28',
        ]);
        $res1->assertStatus(201);
        $id = $res1->json('data.id');

        // API POST: Start empty, End 2026-09-27 -> 201
        $res2 = $this->actingAs($user, 'sanctum')->postJson('/api/mon-hoc', [
            'ma_mon' => 'API02',
            'ten_mon' => 'Môn Học API 2',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res2->assertStatus(201);

        // API POST: Start 2026-09-28, End 2026-09-27 -> 422
        $res3 = $this->actingAs($user, 'sanctum')->postJson('/api/mon-hoc', [
            'ma_mon' => 'API03',
            'ten_mon' => 'Môn Học API 3',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-09-28',
            'ngay_ket_thuc' => '2026-09-27',
        ]);
        $res3->assertStatus(422);

        // API PUT: Partial update chỉ gửi ngay_ket_thuc hợp lệ (sau ngay_bat_dau trong DB)
        $res4 = $this->actingAs($user, 'sanctum')->putJson("/api/mon-hoc/{$id}", [
            'ngay_ket_thuc' => '2026-10-15',
        ]);
        $res4->assertStatus(200);

        // API PUT: Partial update chỉ gửi ngay_ket_thuc trước ngay_bat_dau trong DB -> 422
        $res5 = $this->actingAs($user, 'sanctum')->putJson("/api/mon-hoc/{$id}", [
            'ngay_ket_thuc' => '2026-09-20',
        ]);
        $res5->assertStatus(422);
    }
}
