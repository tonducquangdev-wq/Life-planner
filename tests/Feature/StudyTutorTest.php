<?php

namespace Tests\Feature;

use App\Models\GiaSu;
use App\Models\MonHoc;
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
}
