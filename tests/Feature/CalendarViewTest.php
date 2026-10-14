<?php

namespace Tests\Feature;

use App\Models\BaiTap;
use App\Models\CongViec;
use App\Models\MonHoc;
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

    public function test_calendar_page_displays_real_deadlines_from_database(): void
    {
        $user = User::factory()->create();

        // 1. Deadline trong bảng su_kien
        SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Nộp đồ án Laravel 13',
            'loai_su_kien' => 'deadline',
            'thoi_gian_bat_dau' => Carbon::tomorrow()->setHour(23)->setMinute(59),
        ]);

        // 2. Bài tập có hạn nộp trong bảng bai_tap
        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ten_mon' => 'Hệ điều hành Linux',
            'ma_mon' => 'OS101',
            'so_tin_chi' => 3,
        ]);
        BaiTap::create([
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Báo cáo Kernel Linux',
            'han_nop' => Carbon::now()->addDays(2),
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        // 3. Công việc có deadline
        CongViec::create([
            'user_id' => $user->id,
            'ten_cong_viec' => 'Hoàn thiện giao diện PWA',
            'deadline' => Carbon::now()->addDays(3),
            'trang_thai' => 'dang_lam',
        ]);

        $response = $this->actingAs($user)->get('/calendar');

        $response->assertStatus(200);
        $response->assertSee('Nộp đồ án Laravel 13');
        $response->assertSee('Báo cáo Kernel Linux');
        $response->assertSee('Hoàn thiện giao diện PWA');
        $response->assertDontSee('Thi giữa kỳ Kiến trúc Phần mềm'); // Không còn dữ liệu mẫu cũ
    }

    public function test_calendar_deadlines_api_returns_real_deadlines(): void
    {
        $user = User::factory()->create();

        SuKien::create([
            'user_id' => $user->id,
            'tieu_de' => 'Deadline kiểm thử API',
            'loai_su_kien' => 'deadline',
            'thoi_gian_bat_dau' => Carbon::tomorrow()->setHour(23)->setMinute(59),
        ]);

        $response = $this->actingAs($user)->getJson('/calendar/deadlines');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'tieu_de' => 'Deadline kiểm thử API',
        ]);
    }
}
