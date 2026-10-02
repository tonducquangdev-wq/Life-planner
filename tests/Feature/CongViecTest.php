<?php

namespace Tests\Feature;

use App\Models\CongViec;
use App\Models\DuAn;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CongViecTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_project(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/du-an', [
            'ten_du_an' => 'Đồ án Laravel 13 Life Planner',
            'mo_ta' => 'Xây dựng module Công việc & Dự án',
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
            'trang_thai' => 'dang_thuc_hien',
            'mau_nhan' => '#4F46E5',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertEquals(1, DuAn::count());
        $duAn = DuAn::first();
        $this->assertEquals('Đồ án Laravel 13 Life Planner', $duAn->ten_du_an);
        $this->assertEquals('dang_thuc_hien', $duAn->trang_thai);
    }

    public function test_can_create_task_with_calendar_sync(): void
    {
        $user = User::factory()->create();
        $duAn = DuAn::create([
            'user_id' => $user->id,
            'ten_du_an' => 'Dự án A',
            'trang_thai' => 'dang_thuc_hien',
        ]);

        $response = $this->actingAs($user)->postJson('/cong-viec', [
            'ten_cong_viec' => 'Nộp báo cáo đồ án',
            'du_an_id' => $duAn->id,
            'uu_tien' => 'khan_cap',
            'trang_thai' => 'can_lam',
            'deadline' => '2026-10-10 17:00:00',
            'dong_bo_calendar' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertEquals(1, CongViec::count());
        $task = CongViec::first();
        $this->assertTrue($task->dong_bo_calendar);

        // Check linked SuKien on Calendar
        $this->assertEquals(1, SuKien::count());
        $suKien = SuKien::first();
        $this->assertEquals('[Công việc] Nộp báo cáo đồ án', $suKien->tieu_de);
        $this->assertEquals('cong_viec', $suKien->loai_su_kien);
        $this->assertEquals($task->id, $suKien->cong_viec_id);
    }

    public function test_can_create_task_without_calendar_sync(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/cong-viec', [
            'ten_cong_viec' => 'Công việc tự do',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'can_lam',
            'deadline' => '2026-10-10 17:00:00',
            'dong_bo_calendar' => false,
        ]);

        $response->assertStatus(201);
        $this->assertEquals(1, CongViec::count());
        // Should NOT create SuKien when dong_bo_calendar is false
        $this->assertEquals(0, SuKien::count());
    }

    public function test_overdue_task_attributes(): void
    {
        $user = User::factory()->create();

        $pastTask = CongViec::create([
            'user_id' => $user->id,
            'ten_cong_viec' => 'Task quá hạn',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'dang_lam',
            'deadline' => '2026-09-01 08:00:00', // Past date
        ]);

        $futureTask = CongViec::create([
            'user_id' => $user->id,
            'ten_cong_viec' => 'Task tương lai',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'can_lam',
            'deadline' => '2026-10-20 08:00:00', // Future date
        ]);

        $this->assertTrue($pastTask->is_qua_han);
        $this->assertFalse($futureTask->is_qua_han);
        $this->assertNotNull($pastTask->qua_han_text);
        $this->assertEquals(1, CongViec::quaHan()->count());
    }

    public function test_project_progress_average_calculation(): void
    {
        $user = User::factory()->create();
        $duAn = DuAn::create([
            'user_id' => $user->id,
            'ten_du_an' => 'Dự án tiến độ',
        ]);

        CongViec::create([
            'user_id' => $user->id,
            'du_an_id' => $duAn->id,
            'ten_cong_viec' => 'Task 1',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'hoan_thanh',
            'tien_do' => 100,
        ]);

        CongViec::create([
            'user_id' => $user->id,
            'du_an_id' => $duAn->id,
            'ten_cong_viec' => 'Task 2',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'dang_lam',
            'tien_do' => 50,
        ]);

        // Average progress = (100 + 50) / 2 = 75%
        $duAnWithStats = DuAn::where('id', $duAn->id)
            ->withAvg('congViecs as tien_do_trung_binh', 'tien_do')
            ->first();

        $this->assertEquals(75, $duAnWithStats->tien_do_percent);
    }

    public function test_kanban_status_update(): void
    {
        $user = User::factory()->create();
        $task = CongViec::create([
            'user_id' => $user->id,
            'ten_cong_viec' => 'Task Kanban',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'can_lam',
            'tien_do' => 0,
        ]);

        $response = $this->actingAs($user)->patchJson("/cong-viec/{$task->id}/trang-thai", [
            'trang_thai' => 'hoan_thanh',
        ]);

        $response->assertStatus(200);
        $task->refresh();
        $this->assertEquals('hoan_thanh', $task->trang_thai);
        $this->assertEquals(100, $task->tien_do);
    }

    public function test_unchecking_calendar_sync_deletes_linked_calendar_event(): void
    {
        $user = User::factory()->create();

        // Create task with calendar sync enabled
        $task = CongViec::create([
            'user_id' => $user->id,
            'ten_cong_viec' => 'Task đồng bộ Lịch',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'can_lam',
            'deadline' => '2026-10-15 10:00:00',
            'dong_bo_calendar' => true,
        ]);

        SuKien::create([
            'user_id' => $user->id,
            'cong_viec_id' => $task->id,
            'tieu_de' => '[Công việc] Task đồng bộ Lịch',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-15 10:00:00',
            'thoi_gian_ket_thuc' => '2026-10-15 11:00:00',
        ]);

        $this->assertEquals(1, SuKien::count());

        // Update task to uncheck calendar sync
        $response = $this->actingAs($user)->putJson("/cong-viec/{$task->id}", [
            'ten_cong_viec' => 'Task đồng bộ Lịch',
            'uu_tien' => 'trung_binh',
            'trang_thai' => 'can_lam',
            'deadline' => '2026-10-15 10:00:00',
            'dong_bo_calendar' => false,
        ]);

        $response->assertStatus(200);

        // Linked SuKien should be deleted
        $this->assertEquals(0, SuKien::count());
    }
}
