<?php

namespace Tests\Feature;

use App\Models\CongViec;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarCongViecTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_work_event_via_calendar_store_and_auto_sync_cong_viec(): void
    {
        $response = $this->actingAs($this->user)->postJson('/calendar/events', [
            'tieu_de' => 'Họp Sprint Review & Kế hoạch tuần',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-20 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 11:30:00',
            'mo_ta' => 'Phòng họp Zoom A',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Họp Sprint Review & Kế hoạch tuần',
                    'type' => 'cong-viec',
                    'color' => '#6366F1',
                ],
            ]);

        // Kiểm tra bản ghi trong bảng su_kien
        $this->assertDatabaseHas('su_kien', [
            'user_id' => $this->user->id,
            'tieu_de' => 'Họp Sprint Review & Kế hoạch tuần',
            'loai_su_kien' => 'cong_viec',
            'mau_hien_thi' => '#6366F1',
        ]);

        // Kiểm tra tự động tạo bản ghi liên kết trong bảng cong_viec
        $this->assertDatabaseHas('cong_viec', [
            'user_id' => $this->user->id,
            'ten_cong_viec' => 'Họp Sprint Review & Kế hoạch tuần',
            'dong_bo_calendar' => true,
        ]);

        $suKien = SuKien::first();
        $this->assertNotNull($suKien->cong_viec_id);
    }

    public function test_calendar_events_index_returns_work_events_with_hyphenated_type(): void
    {
        SuKien::create([
            'user_id' => $this->user->id,
            'tieu_de' => 'Viết báo cáo tài chính',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-21 14:00:00',
            'thoi_gian_ket_thuc' => '2026-10-21 16:00:00',
            'mau_hien_thi' => '#6366F1',
        ]);

        $response = $this->actingAs($this->user)->getJson('/calendar/events');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('cong-viec', $data[0]['type']);
        $this->assertEquals('#6366F1', $data[0]['color']);
    }

    public function test_can_update_work_event_and_sync_linked_cong_viec(): void
    {
        $congViec = CongViec::create([
            'user_id' => $this->user->id,
            'ten_cong_viec' => 'Task cũ',
            'deadline' => '2026-10-22 10:00:00',
            'dong_bo_calendar' => true,
        ]);

        $suKien = SuKien::create([
            'user_id' => $this->user->id,
            'cong_viec_id' => $congViec->id,
            'tieu_de' => 'Task cũ',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-22 10:00:00',
            'thoi_gian_ket_thuc' => '2026-10-22 11:00:00',
            'mau_hien_thi' => '#6366F1',
        ]);

        $response = $this->actingAs($this->user)->putJson("/calendar/events/{$suKien->id}", [
            'tieu_de' => 'Task mới đã cập nhật',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-22 13:00:00',
            'thoi_gian_ket_thuc' => '2026-10-22 15:00:00',
            'mo_ta' => 'Ghi chú cập nhật',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('su_kien', [
            'id' => $suKien->id,
            'tieu_de' => 'Task mới đã cập nhật',
            'loai_su_kien' => 'cong_viec',
        ]);

        $this->assertDatabaseHas('cong_viec', [
            'id' => $congViec->id,
            'ten_cong_viec' => 'Task mới đã cập nhật',
        ]);
    }

    public function test_deleting_work_event_unsyncs_cong_viec(): void
    {
        $congViec = CongViec::create([
            'user_id' => $this->user->id,
            'ten_cong_viec' => 'Task cần hủy lịch',
            'deadline' => '2026-10-23 09:00:00',
            'dong_bo_calendar' => true,
        ]);

        $suKien = SuKien::create([
            'user_id' => $this->user->id,
            'cong_viec_id' => $congViec->id,
            'tieu_de' => 'Task cần hủy lịch',
            'loai_su_kien' => 'cong_viec',
            'thoi_gian_bat_dau' => '2026-10-23 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-23 10:00:00',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/calendar/events/{$suKien->id}");

        $response->assertStatus(200);

        // SuKien đã bị soft delete
        $this->assertSoftDeleted('su_kien', ['id' => $suKien->id]);

        // CongViec vẫn tồn tại nhưng dong_bo_calendar = false
        $congViec->refresh();
        $this->assertFalse($congViec->dong_bo_calendar);
    }
}
