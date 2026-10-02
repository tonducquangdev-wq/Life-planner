<?php

namespace Tests\Feature;

use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuKienConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_event_time_conflict_on_store(): void
    {
        $user = User::factory()->create();

        // Existing event from 08:00 to 10:30
        SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Học Lập Trình Web',
            'loai_su_kien' => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:30:00',
        ]);

        // Attempting to add an overlapping event from 09:00 to 11:00
        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Họp Nhóm Đồ Án',
            'loai_su_kien' => 'ca-nhan',
            'thoi_gian_bat_dau' => '2026-10-01 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 11:00:00',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'has_conflict' => true,
            ]);

        $this->assertCount(1, $response->json('conflicts'));
        $this->assertEquals('Học Lập Trình Web', $response->json('conflicts.0.tieu_de'));
    }

    public function test_allows_force_saving_conflicting_event(): void
    {
        $user = User::factory()->create();

        // Existing event from 08:00 to 10:30
        SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Học Lập Trình Web',
            'loai_su_kien' => 'hoc_tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:30:00',
        ]);

        // Add overlapping event with van_them = true
        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Họp Nhóm Đồ Án',
            'loai_su_kien' => 'ca-nhan',
            'thoi_gian_bat_dau' => '2026-10-01 09:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 11:00:00',
            'van_them' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertEquals(2, SuKien::count());
    }

    public function test_can_create_event_with_custom_end_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Hội thảo Công nghệ 2 ngày',
            'loai_su_kien' => 'ca-nhan',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-02 17:00:00',
        ]);

        $response->assertStatus(201);
        $event = SuKien::first();
        $this->assertEquals('2026-10-01 08:00:00', $event->thoi_gian_bat_dau->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-10-02 17:00:00', $event->thoi_gian_ket_thuc->format('Y-m-d H:i:s'));
    }
}
