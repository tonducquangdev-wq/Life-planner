<?php

namespace Tests\Feature;

use App\Models\LichHoc;
use App\Models\MonHoc;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonHocCalendarSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_mon_hoc_automatically_syncs_to_calendar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mon-hoc', [
            'ten_mon' => 'Lập trình Web & Laravel',
            'ma_mon' => 'INT3306',
            'giang_vien' => 'TS. Nguyễn Văn A',
            'phong_hoc' => 'Phòng B2.04',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-10-15',
            'ngay_ket_thuc' => '2026-11-15',
            'mau_sac' => '#6366f1',
            'trang_thai' => 'dang_hoc',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('mon-hoc.index'));

        $monHoc = MonHoc::where('ten_mon', 'Lập trình Web & Laravel')->first();
        $this->assertNotNull($monHoc);

        // Kiểm tra đã tự động tạo LichHoc
        $lichHocs = LichHoc::where('mon_hoc_id', $monHoc->id)->get();
        $this->assertNotEmpty($lichHocs);

        // Kiểm tra đã tự động tạo sự kiện SuKien trên Calendar
        $suKiens = SuKien::where('mon_hoc_id', $monHoc->id)->get();
        $this->assertNotEmpty($suKiens);

        $firstEvent = $suKiens->first();
        $this->assertEquals('hoc_tap', $firstEvent->loai_su_kien);
        $this->assertEquals('#6366f1', $firstEvent->mau_hien_thi);
        $this->assertEquals($user->id, $firstEvent->user_id);
        $this->assertStringContainsString('Lập trình Web & Laravel', $firstEvent->tieu_de);
        $this->assertStringContainsString('B2.04', $firstEvent->mo_ta);
    }

    public function test_creating_mon_hoc_with_custom_days_and_hours(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/mon-hoc', [
            'ten_mon' => 'Cơ sở dữ liệu phân tán',
            'ma_mon' => 'DB401',
            'giang_vien' => 'ThS. Trần Thị B',
            'phong_hoc' => 'Phòng Lab 3',
            'so_tin_chi' => 4,
            'ngay_bat_dau' => '2026-10-19', // Monday
            'ngay_ket_thuc' => '2026-11-09',
            'ngay_trong_tuan' => [1, 3], // Thứ 2 và Thứ 4
            'gio_bat_dau' => '07:30',
            'gio_ket_thuc' => '10:00',
            'dong_bo_calendar' => 1,
            'mau_sac' => '#10b981',
            'trang_thai' => 'dang_hoc',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $monHoc = MonHoc::where('ten_mon', 'Cơ sở dữ liệu phân tán')->first();
        $this->assertNotNull($monHoc);

        // Phải có 2 lịch học hàng tuần (Thứ 2 và Thứ 4)
        $this->assertEquals(2, LichHoc::where('mon_hoc_id', $monHoc->id)->count());

        // Phải có sự kiện Calendar được sinh ra cho các ngày
        $events = SuKien::where('mon_hoc_id', $monHoc->id)->get();
        $this->assertGreaterThan(0, $events->count());
        $this->assertEquals('#10b981', $events->first()->mau_hien_thi);
    }

    public function test_deleting_mon_hoc_cleans_up_calendar_events(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ten_mon' => 'Môn chuẩn bị xóa',
            'ma_mon' => 'DEL101',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-10-15',
            'ngay_ket_thuc' => '2026-11-15',
            'trang_thai' => 'dang_hoc',
        ]);

        $monHoc->syncToCalendar([1, 4], '08:00', '10:30', true);
        $this->assertGreaterThan(0, SuKien::where('mon_hoc_id', $monHoc->id)->count());
        $this->assertGreaterThan(0, LichHoc::where('mon_hoc_id', $monHoc->id)->count());

        $res = $this->actingAs($user)->delete("/mon-hoc/{$monHoc->id}");
        $res->assertRedirect(route('mon-hoc.index'));

        // Kiểm tra sau khi xóa môn học thì SuKien và LichHoc đã được xóa
        $this->assertEquals(0, SuKien::where('mon_hoc_id', $monHoc->id)->count());
        $this->assertEquals(0, LichHoc::where('mon_hoc_id', $monHoc->id)->count());
    }

    public function test_api_creating_mon_hoc_also_syncs_to_calendar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/mon-hoc', [
            'ten_mon' => 'Trí tuệ nhân tạo nâng cao',
            'ma_mon' => 'AI501',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-10-15',
            'ngay_ket_thuc' => '2026-11-15',
            'ngay_trong_tuan' => [2, 5],
            'gio_bat_dau' => '13:00',
            'gio_ket_thuc' => '15:30',
            'mau_sac' => '#f59e0b',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'events_created',
            'data' => [
                'id',
                'ten_mon',
                'lich_hocs',
                'su_kiens',
            ],
        ]);

        $monHocId = $response->json('data.id');
        $this->assertGreaterThan(0, SuKien::where('mon_hoc_id', $monHocId)->count());
    }
}
