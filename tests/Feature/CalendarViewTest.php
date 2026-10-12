<?php

namespace Tests\Feature;

use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_page_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/calendar');

        $response->assertStatus(200);
        $response->assertSee('calendarGrid');
        $response->assertSee('todayScheduleList');
        $response->assertSee('upcomingDeadlineList');
        $response->assertSee('createEventModal');
        $response->assertSee('btnPrevMonth');
        $response->assertSee('btnNextMonth');
        $response->assertSee('calendarViewModeGroup');
        $response->assertSee('calendarFilterGroup');
    }

    public function test_calendar_events_api_returns_json_list(): void
    {
        $user = User::factory()->create();

        SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Kiểm thử Neumorphism Lịch',
            'loai_su_kien' => 'hoc_tap',
            'thoi_gian_bat_dau' => Carbon::today()->setHour(9)->setMinute(0),
            'thoi_gian_ket_thuc' => Carbon::today()->setHour(11)->setMinute(0),
            'mo_ta' => 'Phòng Lab 3',
        ]);

        $response = $this->actingAs($user)->getJson('/calendar/events');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'title' => 'Kiểm thử Neumorphism Lịch',
        ]);
    }

    public function test_can_create_new_event_via_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Buổi tập Gym Chiều',
            'loai_su_kien' => 'tap_luyen',
            'thoi_gian_bat_dau' => Carbon::tomorrow()->setHour(17)->setMinute(30)->toDateTimeString(),
            'thoi_gian_ket_thuc' => Carbon::tomorrow()->setHour(19)->setMinute(0)->toDateTimeString(),
            'mo_ta' => 'Fitness Center',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('su_kien', [
            'user_id' => $user->id,
            'tieu_de' => 'Buổi tập Gym Chiều',
        ]);
    }

    public function test_can_update_event_via_api(): void
    {
        $user = User::factory()->create();

        $event = SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Họp nhóm cũ',
            'loai_su_kien' => 'ca_nhan',
            'thoi_gian_bat_dau' => Carbon::now()->addDays(2),
            'thoi_gian_ket_thuc' => Carbon::now()->addDays(2)->addHours(2),
        ]);

        $response = $this->actingAs($user)->putJson("/calendar/events/{$event->id}", [
            'tieu_de' => 'Họp nhóm mới đã cập nhật',
            'loai_su_kien' => 'ca_nhan',
            'thoi_gian_bat_dau' => Carbon::now()->addDays(2)->toDateTimeString(),
            'thoi_gian_ket_thuc' => Carbon::now()->addDays(2)->addHours(2)->toDateTimeString(),
            'mo_ta' => 'Google Meet',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('su_kien', [
            'id' => $event->id,
            'tieu_de' => 'Họp nhóm mới đã cập nhật',
        ]);
    }

    public function test_can_delete_event_via_api(): void
    {
        $user = User::factory()->create();

        $event = SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Sự kiện cần xóa',
            'loai_su_kien' => 'ca_nhan',
            'thoi_gian_bat_dau' => Carbon::now()->addDays(3),
            'thoi_gian_ket_thuc' => Carbon::now()->addDays(3)->addHours(1),
        ]);

        $response = $this->actingAs($user)->deleteJson("/calendar/events/{$event->id}?mode=single");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('su_kien', [
            'id' => $event->id,
        ]);
    }
}
