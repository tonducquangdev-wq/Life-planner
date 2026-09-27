<?php

namespace Tests\Feature;

use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuKienRepeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_weekly_repeating_events(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Lớp Lập trình Laravel',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
            'quy_tac_lap' => 'weekly',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        // Weekly should create 12 active instances
        $this->assertEquals(12, SuKien::count());

        $events = SuKien::where('user_id', $user->id)->get();
        $firstEvent = $events->first();

        $this->assertEquals('weekly', $firstEvent->quy_tac_lap);
        $this->assertNotEmpty($firstEvent->nhom_lap_id);

        // Check that all 12 events share the same nhom_lap_id
        $this->assertEquals(12, SuKien::where('nhom_lap_id', $firstEvent->nhom_lap_id)->count());
    }

    public function test_can_create_daily_repeating_events(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Tập Gym sáng',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
            'quy_tac_lap' => 'daily',
        ]);

        $response->assertStatus(201);
        // Daily creates 30 active instances
        $this->assertEquals(30, SuKien::count());
    }

    public function test_can_delete_all_occurrences_of_repeating_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Chạy bộ',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'quy_tac_lap' => 'weekly',
        ]);

        $firstEvent = SuKien::first();
        $this->assertNotNull($firstEvent);

        // Delete all occurrences
        $deleteResponse = $this->actingAs($user)->deleteJson("/calendar/events/{$firstEvent->id}?mode=all");
        $deleteResponse->assertStatus(200);

        $this->assertEquals(0, SuKien::count());
    }

    public function test_can_delete_single_occurrence_of_repeating_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Chạy bộ',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'quy_tac_lap' => 'weekly',
        ]);

        $firstEvent = SuKien::first();

        // Delete single occurrence
        $deleteResponse = $this->actingAs($user)->deleteJson("/calendar/events/{$firstEvent->id}?mode=single");
        $deleteResponse->assertStatus(200);

        $this->assertEquals(11, SuKien::count());
    }
}
