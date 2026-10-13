<?php

namespace Tests\Feature;

use App\Models\GiaSu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyTutorTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_tutors_list(): void
    {
        $user = User::factory()->create();

        GiaSu::create([
            'ho_ten' => 'Gia Su Test',
            'email' => 'tutor@test.local',
            'so_dien_thoai' => '0901234567',
            'chuyen_mon' => 'Laravel 13 & PHP',
            'hoc_phi_theo_gio' => 150000,
            'danh_gia' => 4.9,
            'so_danh_gia' => 10,
            'mo_ta_kinh_nghiem' => 'Kinh nghiem lap trinh web',
        ]);

        $response = $this->actingAs($user)->get(route('study.tutors'));

        $response->assertStatus(200);
        $response->assertSee('Gia Su Test');
        $response->assertSee('Quay lại Lịch');
        $response->assertSee('Tìm Gia sư');
    }

    public function test_guest_is_redirected_from_tutors_list(): void
    {
        $response = $this->get(route('study.tutors'));

        $response->assertRedirect('/login');
    }

    public function test_user_can_filter_tutors_by_keyword(): void
    {
        $user = User::factory()->create();

        GiaSu::create([
            'ho_ten' => 'Gia Su Python',
            'email' => 'python@test.local',
            'so_dien_thoai' => '0901111111',
            'chuyen_mon' => 'Python Django Machine Learning',
            'hoc_phi_theo_gio' => 170000,
            'danh_gia' => 4.8,
            'so_danh_gia' => 15,
            'mo_ta_kinh_nghiem' => 'Day Python co ban den nang cao',
        ]);

        GiaSu::create([
            'ho_ten' => 'Gia Su React',
            'email' => 'react@test.local',
            'so_dien_thoai' => '0902222222',
            'chuyen_mon' => 'React Frontend Web',
            'hoc_phi_theo_gio' => 200000,
            'danh_gia' => 5.0,
            'so_danh_gia' => 20,
            'mo_ta_kinh_nghiem' => 'Day React chuyen nghiep',
        ]);

        $response = $this->actingAs($user)->get(route('study.tutors', ['q' => 'Python']));

        $response->assertStatus(200);
        $response->assertSee('Gia Su Python');
        $response->assertDontSee('Gia Su React');
    }

    public function test_user_can_filter_tutors_by_price(): void
    {
        $user = User::factory()->create();

        GiaSu::create([
            'ho_ten' => 'Gia Su Gia Re',
            'email' => 'giare@test.local',
            'so_dien_thoai' => '0903333333',
            'chuyen_mon' => 'Toan tin dai cuong',
            'hoc_phi_theo_gio' => 120000,
            'danh_gia' => 4.7,
            'so_danh_gia' => 8,
            'mo_ta_kinh_nghiem' => 'Hoc phi sinh vien',
        ]);

        GiaSu::create([
            'ho_ten' => 'Gia Su Chuyen Gia',
            'email' => 'chuyengia@test.local',
            'so_dien_thoai' => '0904444444',
            'chuyen_mon' => 'Kien truc he thong lon',
            'hoc_phi_theo_gio' => 250000,
            'danh_gia' => 5.0,
            'so_danh_gia' => 30,
            'mo_ta_kinh_nghiem' => 'Chuyen gia cap cao',
        ]);

        // Duoi 180k
        $responseUnder = $this->actingAs($user)->get(route('study.tutors', ['muc_gia' => 'duoi_180']));
        $responseUnder->assertStatus(200);
        $responseUnder->assertSee('Gia Su Gia Re');
        $responseUnder->assertDontSee('Gia Su Chuyen Gia');

        // Tren 180k
        $responseOver = $this->actingAs($user)->get(route('study.tutors', ['muc_gia' => 'tren_180']));
        $responseOver->assertStatus(200);
        $responseOver->assertSee('Gia Su Chuyen Gia');
        $responseOver->assertDontSee('Gia Su Gia Re');
    }

    public function test_empty_state_is_displayed_when_no_tutors_match(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('study.tutors', ['q' => 'TuKhoaKhongTonTai999']));

        $response->assertStatus(200);
        $response->assertSee('Không tìm thấy gia sư phù hợp');
        $response->assertSee('Xóa bộ lọc tìm kiếm');
    }

    public function test_contact_modal_and_data_attributes_are_rendered(): void
    {
        $user = User::factory()->create();

        GiaSu::create([
            'ho_ten' => 'Do Quang Huy',
            'email' => 'huy@test.local',
            'so_dien_thoai' => '0987654321',
            'chuyen_mon' => 'An Toan Mang',
            'hoc_phi_theo_gio' => 190000,
            'danh_gia' => 4.9,
            'so_danh_gia' => 25,
            'mo_ta_kinh_nghiem' => 'Chuyen gia bao mat',
        ]);

        $response = $this->actingAs($user)->get(route('study.tutors'));

        $response->assertStatus(200);
        $response->assertSee('contactTutorModal');
        $response->assertSee('btn-contact-tutor');
        $response->assertSee('data-phone="0987654321"', false);
        $response->assertSee('btnCopyPhone');
        $response->assertSee('btnZaloLink');
    }
}
