<?php

namespace Tests\Feature;

use App\Models\BuoiTap;
use App\Models\ChiTietBuoiTap;
use App\Models\KeHoachTapLuyen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutSharingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_workout_checklist(): void
    {
        $user = User::factory()->create();
        $plan = KeHoachTapLuyen::create([
            'user_id' => $user->id,
            'ten_ke_hoach' => 'Plan Checklist Test',
            'is_active' => true,
        ]);

        $buoiTap = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Buoi 1 Test',
            'thu_tu' => 1,
            'ngay_trong_tuan' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('workout.checklist', ['buoi_tap_id' => $buoiTap->id]));
        $response->assertRedirect(route('tap-luyen.index', ['buoi_tap_id' => $buoiTap->id]));

        $responseMain = $this->actingAs($user)->get(route('tap-luyen.index', ['buoi_tap_id' => $buoiTap->id]));
        $responseMain->assertStatus(200);
        $responseMain->assertSee('Checklist bài tập');
        $responseMain->assertSee('Quay lại Lịch');
    }

    public function test_user_can_generate_share_code(): void
    {
        $user = User::factory()->create();
        $plan = KeHoachTapLuyen::create([
            'user_id' => $user->id,
            'ten_ke_hoach' => 'Plan Share Test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('workout.share.generate', ['id' => $plan->id]));

        $plan->refresh();
        $this->assertTrue($plan->is_shared);
        $this->assertNotEmpty($plan->share_code);
        $this->assertStringStartsWith('PPL-', $plan->share_code);
    }

    public function test_user_can_view_community_and_shared_plan(): void
    {
        $user1 = User::factory()->create(['ho_ten' => 'Gymer Pro']);
        $user2 = User::factory()->create(['ho_ten' => 'Newbie']);

        $plan = KeHoachTapLuyen::create([
            'user_id' => $user1->id,
            'ten_ke_hoach' => 'PPL Pro Community',
            'share_code' => 'PPL-COMM1',
            'is_shared' => true,
        ]);

        // Check community list
        $response = $this->actingAs($user2)->get(route('workout.community'));
        $response->assertStatus(200);
        $response->assertSee('PPL-COMM1');
        $response->assertSee('PPL Pro Community');

        // Check view shared plan details
        $viewResponse = $this->actingAs($user2)->get(route('workout.share.view', ['code' => 'PPL-COMM1']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('PPL Pro Community');
        $viewResponse->assertSee('PPL-COMM1');
    }

    public function test_user_can_copy_shared_plan_without_modifying_original(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();

        $plan = KeHoachTapLuyen::create([
            'user_id' => $owner->id,
            'ten_ke_hoach' => 'Original Plan',
            'share_code' => 'PPL-ORIG',
            'is_shared' => true,
        ]);

        $bt = BuoiTap::create([
            'ke_hoach_tap_luyen_id' => $plan->id,
            'ten_buoi_tap' => 'Push Day',
            'thu_tu' => 1,
            'ngay_trong_tuan' => 1,
        ]);

        $copyResponse = $this->actingAs($recipient)->post(route('workout.copy', ['code' => 'PPL-ORIG']));

        $copyResponse->assertRedirect(route('tap-luyen.index'));

        // Recipient has new cloned plan
        $this->assertDatabaseHas('ke_hoach_tap_luyen', [
            'user_id' => $recipient->id,
            'ten_ke_hoach' => 'Original Plan (Sao chép)',
        ]);

        // Original plan remains owned by $owner
        $plan->refresh();
        $this->assertEquals($owner->id, $plan->user_id);
        $this->assertEquals('Original Plan', $plan->ten_ke_hoach);
    }
}
