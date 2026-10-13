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

    public function test_updating_recurring_event_to_once_automatically_deletes_all_other_occurrences(): void
    {
        $user = User::factory()->create();

        // Tạo chuỗi sự kiện lặp hàng tuần: 4 buổi
        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Học nhóm đồ án',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
            'quy_tac_lap' => 'weekly',
            'ngay_ket_thuc_lap' => '2026-10-22',
        ]);

        $this->assertEquals(4, SuKien::count());
        $firstEvent = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->first();
        $this->assertNotNull($firstEvent->nhom_lap_id);

        // Chuyển sự kiện về 'once' (chỉ lặp 1 lần)
        $response = $this->actingAs($user)->putJson("/calendar/events/{$firstEvent->id}", [
            'tieu_de' => 'Học nhóm đồ án (Chỉ 1 buổi duy nhất)',
            'loai_su_kien' => 'hoc-tap',
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 10:00:00',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Tất cả các buổi lặp khác phía sau phải tự động bị xóa đi
        $this->assertEquals(1, SuKien::count());

        $remainingEvent = SuKien::find($firstEvent->id);
        $this->assertNotNull($remainingEvent);
        $this->assertEquals('once', $remainingEvent->quy_tac_lap);
        $this->assertNull($remainingEvent->nhom_lap_id);
        $this->assertNull($remainingEvent->ngay_ket_thuc_lap);
        $this->assertEquals('Học nhóm đồ án (Chỉ 1 buổi duy nhất)', $remainingEvent->tieu_de);
    }

    public function test_updating_middle_occurrence_to_once_preserves_past_and_deletes_future_occurrences(): void
    {
        $user = User::factory()->create();

        // Tạo 5 buổi lặp hàng ngày (1/10 đến 5/10)
        $this->actingAs($user)->postJson('/calendar/events', [
            'tieu_de' => 'Chạy bộ công viên',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-01 06:00:00',
            'thoi_gian_ket_thuc' => '2026-10-01 07:00:00',
            'quy_tac_lap' => 'daily',
            'ngay_ket_thuc_lap' => '2026-10-05',
        ]);

        $this->assertEquals(5, SuKien::count());
        $events = SuKien::where('user_id', $user->id)->orderBy('thoi_gian_bat_dau')->get();
        $middleEvent = $events->get(2); // Ngày 3/10

        // Người dùng sửa buổi ngày 3/10 thành 1 lần duy nhất
        $response = $this->actingAs($user)->putJson("/calendar/events/{$middleEvent->id}", [
            'tieu_de' => 'Chạy bộ ngày 3/10 duy nhất',
            'loai_su_kien' => 'tap-luyen',
            'thoi_gian_bat_dau' => '2026-10-03 06:00:00',
            'thoi_gian_ket_thuc' => '2026-10-03 07:00:00',
            'quy_tac_lap' => 'once',
        ]);

        $response->assertStatus(200);

        // Các ngày đã qua (1/10, 2/10) và ngày hiện tại (3/10) được GIỮ LẠI (tổng cộng 3 buổi)
        // Các ngày sau đó (4/10, 5/10) bị XÓA
        $this->assertEquals(3, SuKien::count());

        $this->assertTrue(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-01')->exists());
        $this->assertTrue(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-02')->exists());
        $this->assertFalse(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-04')->exists());
        $this->assertFalse(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-05')->exists());

        $editedEvent = SuKien::find($middleEvent->id);
        $this->assertEquals('once', $editedEvent->quy_tac_lap);
        $this->assertNull($editedEvent->nhom_lap_id);
    }

    public function test_api_updating_middle_occurrence_to_once_preserves_past_occurrences(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Tạo chuỗi 4 buổi hàng tuần: Oct 1, 8, 15, 22
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/su-kien', [
                'tieu_de' => 'Lớp Tiếng Anh',
                'loai_su_kien' => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 18:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 19:30:00',
                'quy_tac_lap' => 'weekly',
                'ngay_ket_thuc_lap' => '2026-10-22',
            ]);

        $this->assertEquals(4, SuKien::count());
        $events = SuKien::orderBy('thoi_gian_bat_dau')->get();
        $thirdEvent = $events->get(2); // Ngày 15/10

        // Sửa buổi 15/10 về 1 lần
        $updateRes = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/su-kien/{$thirdEvent->id}", [
                'tieu_de' => 'Lớp Tiếng Anh (Chỉ học buổi 15/10)',
                'quy_tac_lap' => 'once',
            ]);

        $updateRes->assertStatus(200);

        // Buổi 1/10 và 8/10 trong quá khứ được giữ lại, buổi 15/10 được giữ lại, buổi 22/10 bị xóa => còn 3 buổi
        $this->assertEquals(3, SuKien::count());
        $this->assertTrue(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-01')->exists());
        $this->assertTrue(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-08')->exists());
        $this->assertTrue(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-15')->exists());
        $this->assertFalse(SuKien::whereDate('thoi_gian_bat_dau', '2026-10-22')->exists());

        $editedEvent = SuKien::find($thirdEvent->id);
        $this->assertEquals('once', $editedEvent->quy_tac_lap);
        $this->assertNull($editedEvent->nhom_lap_id);
    }

    public function test_api_updating_first_recurring_event_to_once_deletes_future_occurrences(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Tạo chuỗi sự kiện lặp qua API: 4 buổi
        $createRes = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/su-kien', [
                'tieu_de' => 'Lớp Tiếng Anh',
                'loai_su_kien' => 'hoc_tap',
                'thoi_gian_bat_dau' => '2026-10-01 18:00:00',
                'thoi_gian_ket_thuc' => '2026-10-01 19:30:00',
                'quy_tac_lap' => 'weekly',
                'ngay_ket_thuc_lap' => '2026-10-22', // 4 instances
            ]);

        $createRes->assertStatus(201);
        $this->assertEquals(4, SuKien::count());

        $firstEvent = SuKien::orderBy('thoi_gian_bat_dau')->first();

        // Cập nhật buổi đầu tiên qua API với quy_tac_lap = 'once'
        $updateRes = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/su-kien/{$firstEvent->id}", [
                'tieu_de' => 'Lớp Tiếng Anh (Chỉ học 1 buổi)',
                'quy_tac_lap' => 'once',
            ]);

        $updateRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // Do sửa buổi đầu tiên, tất cả 3 buổi tương lai phía sau bị xóa => chỉ còn 1 buổi
        $this->assertEquals(1, SuKien::count());
        $remainingEvent = SuKien::first();
        $this->assertEquals($firstEvent->id, $remainingEvent->id);
        $this->assertEquals('once', $remainingEvent->quy_tac_lap);
        $this->assertNull($remainingEvent->nhom_lap_id);
        $this->assertNull($remainingEvent->ngay_ket_thuc_lap);
    }
}
