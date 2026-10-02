<?php

namespace Tests\Feature;

use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuKienRepeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_weekly_repeating_events_with_end_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Lớp Lập trình Laravel',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
            'quy_tac_lap' => 'weekly',
            'ngay_ket_thuc_lap' => '2026-10-22', // Oct 1, 8, 15, 22 -> 4 instances
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertEquals(4, SuKien::count());

        $events = SuKien::where('user_id', $user->id)->get();
        $firstEvent = $events->first();

        $this->assertEquals('weekly', $firstEvent->quy_tac_lap);
        $this->assertEquals('2026-10-22', $firstEvent->ngay_ket_thuc_lap->format('Y-m-d'));
        $this->assertNotEmpty($firstEvent->nhom_lap_id);

        $this->assertEquals(4, SuKien::where('nhom_lap_id', $firstEvent->nhom_lap_id)->count());
    }

    public function test_can_create_daily_repeating_events_bounded_by_end_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Tập Gym sáng',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
            'quy_tac_lap' => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-05',
        ]);

        $response->assertStatus(201);
        // Oct 1, 2, 3, 4, 5 -> 5 instances
        $this->assertEquals(5, SuKien::count());

        // Max start date should be Oct 5
        $maxDate = SuKien::max('thoi_gian_bat_dau');
        $this->assertStringStartsWith('2026-10-05', $maxDate);
    }

    public function test_validation_fails_if_ngay_ket_thuc_lap_missing_for_recurring_event(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Học nhóm',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'quy_tac_lap' => 'daily',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ngay_ket_thuc_lap']);
    }

    public function test_validation_fails_if_ngay_ket_thuc_lap_before_start_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Học nhóm',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-05 08:00:00',
            'quy_tac_lap' => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-04',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ngay_ket_thuc_lap']);
    }

    public function test_can_update_ngay_ket_thuc_lap_and_prune_exceeding_instances(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Tập Gym sáng',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
            'quy_tac_lap' => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-05', // 5 instances
        ]);

        $firstEvent = SuKien::first();
        $this->assertEquals(5, SuKien::count());

        // Update all in group to end on 2026-10-03
        $updateResponse = $this->actingAs($user)->putJson("/calendar/events/{$firstEvent->id}", [
            'tieu_de' => 'Tập Gym sáng (Cập nhật)',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'quy_tac_lap' => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-03',
            'update_mode' => 'all',
        ]);

        $updateResponse->assertStatus(200);
        $this->assertEquals(3, SuKien::count());
    }

    public function test_can_delete_all_occurrences_of_repeating_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Chạy bộ',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'quy_tac_lap' => 'weekly',
            'ngay_ket_thuc_lap' => '2026-10-22',
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
            'ngay_ket_thuc_lap' => '2026-10-22', // 4 instances
        ]);

        $firstEvent = SuKien::first();

        // Delete single occurrence
        $deleteResponse = $this->actingAs($user)->deleteJson("/calendar/events/{$firstEvent->id}?mode=single");
        $deleteResponse->assertStatus(200);

        $this->assertEquals(3, SuKien::count());
    }
}
