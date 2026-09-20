<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'ho_ten' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->ho_ten);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'ho_ten' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_authenticated_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300)->size(500);

        $response = $this
            ->actingAs($user)
            ->patch('/profile/avatar', [
                'avatar' => $file,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile')
            ->assertSessionHas('status', 'avatar-updated');

        $user->refresh();

        $this->assertNotNull($user->anh_dai_dien);
        $this->assertStringStartsWith('avatars/', $user->anh_dai_dien);
        Storage::disk('public')->assertExists($user->anh_dai_dien);
    }

    public function test_avatar_upload_fails_with_invalid_mime_type(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this
            ->actingAs($user)
            ->patch('/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->refresh()->anh_dai_dien);
    }

    public function test_avatar_upload_fails_when_file_exceeds_max_size(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // 3000 KB > 2048 KB limit
        $file = UploadedFile::fake()->image('large.png')->size(3000);

        $response = $this
            ->actingAs($user)
            ->patch('/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->refresh()->anh_dai_dien);
    }

    public function test_old_avatar_is_deleted_when_new_avatar_uploaded(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // Upload first avatar
        $firstFile = UploadedFile::fake()->image('avatar1.jpg', 200, 200);
        $this->actingAs($user)->patch('/profile/avatar', ['avatar' => $firstFile]);

        $user->refresh();
        $firstPath = $user->anh_dai_dien;
        Storage::disk('public')->assertExists($firstPath);

        // Upload second avatar
        $secondFile = UploadedFile::fake()->image('avatar2.png', 200, 200);
        $this->actingAs($user)->patch('/profile/avatar', ['avatar' => $secondFile]);

        $user->refresh();
        $secondPath = $user->anh_dai_dien;

        // Old avatar must be deleted, new avatar exists
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    public function test_guest_cannot_upload_avatar(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->patch('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_user_initials_and_avatar_url_accessors(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'ho_ten' => 'Nguyễn Hoàng Lâm',
            'anh_dai_dien' => null,
        ]);

        $this->assertSame('NL', $user->initials);
        $this->assertNull($user->avatar_url);

        // Case: File does NOT exist on disk -> must return null (fail-safe fallback)
        $user->anh_dai_dien = 'avatars/sample.jpg';
        $this->assertNull($user->avatar_url);

        // Case: File exists on disk -> returns valid URL
        Storage::disk('public')->put('avatars/sample.jpg', 'fake-image-content');
        $this->assertNotNull($user->avatar_url);
        $this->assertStringContainsString('storage/avatars/sample.jpg', $user->avatar_url);

        $user->ho_ten = 'Tôn Đức Quang';
        $this->assertSame('TQ', $user->initials);

        $user->ho_ten = 'Quang';
        $this->assertSame('QU', $user->initials);

        $user->ho_ten = '';
        $this->assertSame('U', $user->initials);
    }

    public function test_profile_renders_successfully_when_avatar_file_is_missing(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'ho_ten' => 'Nguyễn Hoàng Lâm',
            'anh_dai_dien' => 'avatars/deleted_or_missing.jpg',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        // Asserts fallback initials are rendered
        $response->assertSee('NL');
    }
}
