<?php

namespace Tests\Feature;

use App\Models\BaiTap;
use App\Models\GiaSu;
use App\Models\MonHoc;
use App\Models\SuKien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_page_displays_subjects_and_deadlines_correctly(): void
    {
        $user = User::factory()->create(['ho_ten' => 'TQuang']);

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'PHP401',
            'ten_mon' => 'Chuyên đề PHP',
            'giang_vien' => 'GV. Huỳnh Luân',
            'phong_hoc' => 'A204',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-10-15',
            'ngay_ket_thuc' => '2026-11-03',
            'mau_sac' => '#3b82f6',
            'trang_thai' => 'dang_hoc',
        ]);

        $baiTap = $monHoc->baiTaps()->create([
            'tieu_de' => 'Báo cáo cuối kì CD-PHP',
            'mo_ta' => 'Nộp bài báo cáo cuối kỳ',
            'han_nop' => '2026-10-19 19:00:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        $response = $this->actingAs($user)->get(route('mon-hoc.index'));

        $response->assertStatus(200);
        $response->assertSee('Quản Lý Học Tập');
        $response->assertSee('Chuyên đề PHP');
        $response->assertSee('GV. Huỳnh Luân');
        $response->assertSee('A204');
        $response->assertSee('Báo cáo cuối kì CD-PHP');
        $response->assertSee('Lịch: Deadline');
        $response->assertSee('Ưu Tiên: Cao');
        $response->assertSee('TQuang');
    }

    public function test_creating_assignment_automatically_syncs_to_calendar_deadline(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'WEB301',
            'ten_mon' => 'Lập trình Web máy chủ',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $response = $this->actingAs($user)->postJson(route('bai-tap.store'), [
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Bài tập lớn Laravel',
            'mo_ta' => 'Xây dựng API',
            'han_nop' => '2026-10-25 18:00:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $baiTap = BaiTap::where('tieu_de', 'Bài tập lớn Laravel')->first();
        $this->assertNotNull($baiTap);
        $this->assertEquals($monHoc->id, $baiTap->mon_hoc_id);

        // Kiểm tra tự động đồng bộ vào bảng su_kien dưới loại 'deadline'
        $suKien = SuKien::where('user_id', $user->id)
            ->where('mon_hoc_id', $monHoc->id)
            ->where('loai_su_kien', 'deadline')
            ->first();

        $this->assertNotNull($suKien);
        $this->assertStringContainsString('Bài tập lớn Laravel', $suKien->tieu_de);
        $this->assertEquals('2026-10-25 18:00:00', $suKien->thoi_gian_bat_dau->format('Y-m-d H:i:s'));
    }

    public function test_toggling_assignment_completion_status(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'FED202',
            'ten_mon' => 'Chuyên đề Front-End',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $baiTap = $monHoc->baiTaps()->create([
            'tieu_de' => 'Thiết kế UI Dashboard',
            'han_nop' => '2026-10-22 20:00:00',
            'muc_do_uu_tien' => 'trung_binh',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        // Toggle lần 1: từ chưa hoàn thành -> đã hoàn thành
        $res1 = $this->actingAs($user)->patchJson(route('bai-tap.toggle-status', $baiTap));
        $res1->assertStatus(200);
        $res1->assertJson(['success' => true, 'is_completed' => true, 'trang_thai' => 'da_hoan_thanh']);

        $baiTap->refresh();
        $this->assertEquals('da_hoan_thanh', $baiTap->trang_thai);

        // Toggle lần 2: từ đã hoàn thành -> chưa hoàn thành
        $res2 = $this->actingAs($user)->patchJson(route('bai-tap.toggle-status', $baiTap));
        $res2->assertStatus(200);
        $res2->assertJson(['success' => true, 'is_completed' => false, 'trang_thai' => 'chua_hoan_thanh']);

        $baiTap->refresh();
        $this->assertEquals('chua_hoan_thanh', $baiTap->trang_thai);
    }

    public function test_deleting_assignment_removes_synced_calendar_event(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'DB201',
            'ten_mon' => 'Hệ quản trị CSDL',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $baiTap = $monHoc->baiTaps()->create([
            'tieu_de' => 'Bài tập thực hành SQL',
            'han_nop' => '2026-10-20 21:00:00',
            'muc_do_uu_tien' => 'thap',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        SuKien::create([
            'user_id' => $user->id,
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Deadline: Bài tập thực hành SQL',
            'loai_su_kien' => 'deadline',
            'thoi_gian_bat_dau' => '2026-10-20 21:00:00',
            'thoi_gian_ket_thuc' => '2026-10-20 22:00:00',
        ]);

        $response = $this->actingAs($user)->deleteJson(route('bai-tap.destroy', $baiTap));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('bai_tap', ['id' => $baiTap->id]);
        $this->assertDatabaseMissing('su_kien', ['tieu_de' => 'Deadline: Bài tập thực hành SQL']);
    }

    public function test_registering_tutor_saves_to_database(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('gia-su.dang-ky'), [
            'ho_ten' => 'ThS. Nguyễn Văn Quang',
            'chuyen_mon' => 'Fullstack Laravel & Vue.js',
            'so_dien_thoai' => '0912345678',
            'email' => 'tutor@lifeplanner.local',
            'hoc_phi_theo_gio' => 200000,
            'mo_ta_kinh_nghiem' => '3 năm trợ giảng Đại học',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $giaSu = GiaSu::where('ho_ten', 'ThS. Nguyễn Văn Quang')->first();
        $this->assertNotNull($giaSu);
        $this->assertEquals(200000, $giaSu->hoc_phi_theo_gio);
        $this->assertEquals('Fullstack Laravel & Vue.js', $giaSu->chuyen_mon);
    }

    public function test_study_page_renders_floating_navigation_menu_bubble(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('mon-hoc.index'));

        $response->assertStatus(200);
        $response->assertSee('floatingMenuContainer');
        $response->assertSee('floatingMenuToggle');
        $response->assertSee('floatingMenuPopup');
        $response->assertSee('📅 Lịch (Home)');
        $response->assertSee('📚 Học tập');
        $response->assertSee('🏋️ Tập luyện');
        $response->assertSee('👤 Hồ sơ');
        $response->assertSee('📲 Cài App');
        $response->assertSee('🌙 Giao diện Tối');
    }

    public function test_user_can_update_subject_and_resync_calendar(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'CS101',
            'ten_mon' => 'Nhập môn Lập trình',
            'giang_vien' => 'GV. Cũ',
            'phong_hoc' => 'B101',
            'so_tin_chi' => 3,
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
            'trang_thai' => 'dang_hoc',
        ]);
        $monHoc->syncToCalendar([1], '07:00', '10:00', true);

        $response = $this->actingAs($user)->putJson(route('mon-hoc.update', $monHoc), [
            'ma_mon' => 'CS102',
            'ten_mon' => 'Lập trình Nâng cao',
            'giang_vien' => 'GV. Mới Cập Nhật',
            'phong_hoc' => 'A205',
            'so_tin_chi' => 4,
            'trang_thai' => 'dang_hoc',
            'mau_sac' => '#10b981',
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
            'dong_bo_calendar' => 1,
            'ngay_trong_tuan' => [2, 4],
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '11:30',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $monHoc->refresh();
        $this->assertEquals('Lập trình Nâng cao', $monHoc->ten_mon);
        $this->assertEquals('CS102', $monHoc->ma_mon);
        $this->assertEquals('GV. Mới Cập Nhật', $monHoc->giang_vien);
        $this->assertEquals('A205', $monHoc->phong_hoc);
        $this->assertEquals(4, $monHoc->so_tin_chi);
        $this->assertEquals('#10b981', $monHoc->mau_sac);

        // Kiểm tra lịch học mới
        $this->assertDatabaseHas('lich_hoc', [
            'mon_hoc_id' => $monHoc->id,
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '11:30',
        ]);

        // Kiểm tra sự kiện trên calendar đã được sync lại với tên mới
        $suKien = SuKien::where('user_id', $user->id)
            ->where('mon_hoc_id', $monHoc->id)
            ->where('loai_su_kien', 'hoc_tap')
            ->first();
        $this->assertNotNull($suKien);
        $this->assertStringContainsString('Lập trình Nâng cao', $suKien->tieu_de);
    }

    public function test_user_can_update_assignment_and_resync_calendar(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'WEB301',
            'ten_mon' => 'Lập trình Web',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $baiTap = $monHoc->baiTaps()->create([
            'tieu_de' => 'Bài tập 1 cũ',
            'mo_ta' => 'Mô tả cũ',
            'han_nop' => '2026-10-25 18:00:00',
            'muc_do_uu_tien' => 'trung_binh',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        SuKien::create([
            'user_id' => $user->id,
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Deadline: Bài tập 1 cũ',
            'mo_ta' => 'Hạn nộp bài tập môn Lập trình Web - Mô tả cũ',
            'loai_su_kien' => 'deadline',
            'thoi_gian_bat_dau' => '2026-10-25 18:00:00',
            'thoi_gian_ket_thuc' => '2026-10-25 19:00:00',
            'mau_hien_thi' => '#ef4444',
            'bat_thong_bao' => true,
        ]);

        $response = $this->actingAs($user)->putJson(route('bai-tap.update', $baiTap), [
            'mon_hoc_id' => $monHoc->id,
            'tieu_de' => 'Bài tập 1 Final Refactored',
            'mo_ta' => 'Mô tả cập nhật mới',
            'han_nop' => '2026-10-28 20:30:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'dang_thuc_hien',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $baiTap->refresh();
        $this->assertEquals('Bài tập 1 Final Refactored', $baiTap->tieu_de);
        $this->assertEquals('Mô tả cập nhật mới', $baiTap->mo_ta);
        $this->assertEquals('2026-10-28 20:30:00', $baiTap->han_nop->format('Y-m-d H:i:s'));
        $this->assertEquals('cao', $baiTap->muc_do_uu_tien);
        $this->assertEquals('dang_thuc_hien', $baiTap->trang_thai);

        // Kiểm tra sự kiện deadline tương ứng đã được cập nhật
        $suKien = SuKien::where('user_id', $user->id)
            ->where('mon_hoc_id', $monHoc->id)
            ->where('loai_su_kien', 'deadline')
            ->first();

        $this->assertNotNull($suKien);
        $this->assertStringContainsString('Bài tập 1 Final Refactored', $suKien->tieu_de);
        $this->assertEquals('2026-10-28 20:30:00', $suKien->thoi_gian_bat_dau->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-10-28 21:30:00', $suKien->thoi_gian_ket_thuc->format('Y-m-d H:i:s'));
    }

    public function test_study_page_contains_edit_modals_and_edit_buttons(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'PHP501',
            'ten_mon' => 'Laravel Framework',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
        ]);

        $monHoc->baiTaps()->create([
            'tieu_de' => 'Xây dựng REST API',
            'han_nop' => '2026-10-30 23:59:00',
            'muc_do_uu_tien' => 'cao',
            'trang_thai' => 'chua_hoan_thanh',
        ]);

        $response = $this->actingAs($user)->get(route('mon-hoc.index'));

        $response->assertStatus(200);
        $response->assertSee('editMonHocModal');
        $response->assertSee('editBaiTapModal');
        $response->assertSee('study-btn-edit');
        $response->assertSee('study-btn-edit-assignment');
        $response->assertSee('openEditMonHocModal');
        $response->assertSee('openEditBaiTapModal');
        $response->assertSee('create_per_day_container');
        $response->assertSee('edit_per_day_container');
    }

    public function test_user_can_create_subject_with_individual_times_for_each_day(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('mon-hoc.store'), [
            'ma_mon' => 'MULTI101',
            'ten_mon' => 'Thiết kế Vi mạch',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'dong_bo_calendar' => 1,
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
            'ngay_trong_tuan' => [1, 4], // Thứ 2 và Thứ 5
            'gio_bat_dau' => '07:00',
            'gio_ket_thuc' => '11:00',
            'gio_bat_dau_theo_thu' => [
                1 => '07:30',
                4 => '13:15',
            ],
            'gio_ket_thuc_theo_thu' => [
                1 => '11:00',
                4 => '16:45',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $monHoc = MonHoc::where('ma_mon', 'MULTI101')->first();
        $this->assertNotNull($monHoc);

        // Kiểm tra lịch học Thứ 2 (day 1)
        $this->assertDatabaseHas('lich_hoc', [
            'mon_hoc_id' => $monHoc->id,
            'ngay_trong_tuan' => 1,
            'gio_bat_dau' => '07:30',
            'gio_ket_thuc' => '11:00',
        ]);

        // Kiểm tra lịch học Thứ 5 (day 4)
        $this->assertDatabaseHas('lich_hoc', [
            'mon_hoc_id' => $monHoc->id,
            'ngay_trong_tuan' => 4,
            'gio_bat_dau' => '13:15',
            'gio_ket_thuc' => '16:45',
        ]);

        // Kiểm tra sự kiện Lịch trình cho Thứ 5 được tạo đúng lúc 13:15
        $t5Events = SuKien::where('user_id', $user->id)
            ->where('mon_hoc_id', $monHoc->id)
            ->where('loai_su_kien', 'hoc_tap')
            ->get();

        $this->assertTrue($t5Events->contains(fn ($e) => $e->thoi_gian_bat_dau->format('H:i') === '13:15'));
        $this->assertTrue($t5Events->contains(fn ($e) => $e->thoi_gian_bat_dau->format('H:i') === '07:30'));
    }

    public function test_user_can_update_subject_with_individual_times_for_each_day(): void
    {
        $user = User::factory()->create();

        $monHoc = MonHoc::create([
            'user_id' => $user->id,
            'ma_mon' => 'AI202',
            'ten_mon' => 'Trí tuệ nhân tạo',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
        ]);
        $monHoc->syncToCalendar([1], '07:00', '10:00', true);

        // Cập nhật sang 2 buổi với giờ riêng từng buổi
        $response = $this->actingAs($user)->putJson(route('mon-hoc.update', $monHoc), [
            'ma_mon' => 'AI202',
            'ten_mon' => 'Trí tuệ nhân tạo Ứng dụng',
            'so_tin_chi' => 3,
            'trang_thai' => 'dang_hoc',
            'dong_bo_calendar' => 1,
            'ngay_bat_dau' => '2026-10-01',
            'ngay_ket_thuc' => '2026-10-31',
            'ngay_trong_tuan' => [2, 5], // Thứ 3 và Thứ 6
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '10:30',
            'gio_bat_dau_theo_thu' => [
                2 => '08:00',
                5 => '14:00',
            ],
            'gio_ket_thuc_theo_thu' => [
                2 => '11:15',
                5 => '17:30',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Kiểm tra lịch học Thứ 3 (day 2)
        $this->assertDatabaseHas('lich_hoc', [
            'mon_hoc_id' => $monHoc->id,
            'ngay_trong_tuan' => 2,
            'gio_bat_dau' => '08:00',
            'gio_ket_thuc' => '11:15',
        ]);

        // Kiểm tra lịch học Thứ 6 (day 5)
        $this->assertDatabaseHas('lich_hoc', [
            'mon_hoc_id' => $monHoc->id,
            'ngay_trong_tuan' => 5,
            'gio_bat_dau' => '14:00',
            'gio_ket_thuc' => '17:30',
        ]);

        // Kiểm tra sự kiện Lịch trình được sync đúng giờ
        $events = SuKien::where('user_id', $user->id)
            ->where('mon_hoc_id', $monHoc->id)
            ->where('loai_su_kien', 'hoc_tap')
            ->get();

        $this->assertTrue($events->contains(fn ($e) => $e->thoi_gian_bat_dau->format('H:i') === '08:00'));
        $this->assertTrue($events->contains(fn ($e) => $e->thoi_gian_bat_dau->format('H:i') === '14:00'));
    }
}
