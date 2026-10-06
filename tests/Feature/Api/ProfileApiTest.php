<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. User đã đăng nhập cập nhật profile thành công.
     */
    public function test_authenticated_user_can_update_profile_successfully(): void
    {
        $user = User::factory()->create([
            'ho_ten'              => 'Tên Cũ',
            'email'               => 'old_email@example.com',
            'ngon_ngu'            => 'vi',
            'giao_dien'           => 'light',
            'thong_bao_enabled'   => true,
            'thong_bao_lich_hoc'  => true,
            'thong_bao_deadline'  => true,
            'thong_bao_tap_luyen' => true,
            'am_thanh_thong_bao'  => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'ho_ten'              => 'Tên Mới Triển Khai',
            'email'               => 'new_email@example.com',
            'ngon_ngu'            => 'en',
            'giao_dien'           => 'dark',
            'thong_bao_enabled'   => false,
            'thong_bao_lich_hoc'  => false,
            'thong_bao_deadline'  => false,
            'thong_bao_tap_luyen' => false,
            'am_thanh_thong_bao'  => false,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật thông tin trang cá nhân thành công!',
                'data'    => [
                    'id'                  => $user->id,
                    'ho_ten'              => 'Tên Mới Triển Khai',
                    'email'               => 'new_email@example.com',
                    'ngon_ngu'            => 'en',
                    'giao_dien'           => 'dark',
                    'thong_bao_enabled'   => false,
                    'thong_bao_lich_hoc'  => false,
                    'thong_bao_deadline'  => false,
                    'thong_bao_tap_luyen' => false,
                    'am_thanh_thong_bao'  => false,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id'                  => $user->id,
            'ho_ten'              => 'Tên Mới Triển Khai',
            'email'               => 'new_email@example.com',
            'ngon_ngu'            => 'en',
            'giao_dien'           => 'dark',
            'thong_bao_enabled'   => 0,
            'thong_bao_lich_hoc'  => 0,
            'thong_bao_deadline'  => 0,
            'thong_bao_tap_luyen' => 0,
            'am_thanh_thong_bao'  => 0,
        ]);
    }

    /**
     * 2. User cập nhật email trùng với user khác phải nhận 422.
     */
    public function test_updating_email_to_existing_user_email_fails_validation(): void
    {
        $userA = User::factory()->create(['email' => 'usera@example.com']);
        $userB = User::factory()->create(['email' => 'userb@example.com']);

        $tokenA = $userA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/me', [
                'email' => 'userb@example.com',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 3. Guest gọi PUT /api/me phải nhận 401.
     */
    public function test_unauthenticated_guest_cannot_update_profile(): void
    {
        $response = $this->putJson('/api/me', [
            'ho_ten' => 'Hack Name',
        ]);

        $response->assertStatus(401);
    }

    /**
     * 4. Không được cập nhật các field ngoài whitelist.
     */
    public function test_non_whitelisted_fields_are_ignored(): void
    {
        $originalPassword = 'password123';
        $user = User::factory()->create([
            'ho_ten'   => 'Tên Gốc',
            'password' => Hash::make($originalPassword),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me', [
                'ho_ten'   => 'Tên Hợp Lệ',
                'password' => 'hacked_password_123',
                'id'       => 99999,
                'user_id'  => 88888,
                'is_admin' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id'     => $user->id,
                    'ho_ten' => 'Tên Hợp Lệ',
                ],
            ]);

        $user->refresh();

        // Kiểm tra password không bị thay đổi
        $this->assertTrue(Hash::check($originalPassword, $user->password));
        $this->assertFalse(Hash::check('hacked_password_123', $user->password));

        // ID của user giữ nguyên
        $this->assertNotEquals(99999, $user->id);
    }

    /**
     * 5. User upload avatar thành công.
     */
    public function test_authenticated_user_can_upload_avatar_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/me/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật ảnh đại diện thành công!',
            ]);

        $user->refresh();

        $this->assertNotNull($user->anh_dai_dien);
        $this->assertStringStartsWith('avatars/', $user->anh_dai_dien);
        Storage::disk('public')->assertExists($user->anh_dai_dien);

        $response->assertJson([
            'data' => [
                'anh_dai_dien' => $user->anh_dai_dien,
                'avatar_url'   => $user->avatar_url,
            ],
        ]);
    }

    /**
     * 6. Guest không upload được (401).
     */
    public function test_unauthenticated_guest_cannot_upload_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/me/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(401);
    }

    /**
     * 7. Upload file không phải ảnh bị lỗi validation (422).
     */
    public function test_uploading_non_image_file_fails_validation(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/me/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    /**
     * 8. Upload avatar mới sẽ thay thế avatar cũ (xóa file cũ khỏi storage).
     */
    public function test_uploading_new_avatar_replaces_and_deletes_old_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Lưu avatar cũ
        $oldFile = UploadedFile::fake()->image('old_avatar.jpg');
        $oldPath = $oldFile->store('avatars', 'public');

        $user->anh_dai_dien = $oldPath;
        $user->save();

        Storage::disk('public')->assertExists($oldPath);

        // Upload avatar mới
        $newFile = UploadedFile::fake()->image('new_avatar.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/me/avatar', [
                'avatar' => $newFile,
            ]);

        $response->assertStatus(200);

        $user->refresh();

        // Verify file cũ đã bị xóa khỏi disk public và file mới tồn tại
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->anh_dai_dien);
        $this->assertNotEquals($oldPath, $user->anh_dai_dien);
    }

    /**
     * 9. User xóa avatar thành công.
     */
    public function test_authenticated_user_can_delete_avatar_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['ho_ten' => 'Nguyễn Văn A']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg');
        $filePath = $file->store('avatars', 'public');

        $user->anh_dai_dien = $filePath;
        $user->save();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/me/avatar');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã xóa ảnh đại diện!',
                'data'    => [
                    'anh_dai_dien' => null,
                    'avatar_url'   => null,
                    'initials'     => 'NA',
                ],
            ]);
    }

    /**
     * 10. Guest không được phép xóa avatar (401).
     */
    public function test_unauthenticated_guest_cannot_delete_avatar(): void
    {
        $response = $this->deleteJson('/api/me/avatar');

        $response->assertStatus(401);
    }

    /**
     * 11. File avatar vật lý bị xóa khỏi disk public & DB cập nhật anh_dai_dien = null.
     */
    public function test_deleting_avatar_removes_physical_file_and_updates_database(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->image('my_avatar.jpg');
        $filePath = $file->store('avatars', 'public');
        $user->anh_dai_dien = $filePath;
        $user->save();

        Storage::disk('public')->assertExists($filePath);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/me/avatar');

        $response->assertStatus(200);

        $user->refresh();

        $this->assertNull($user->anh_dai_dien);
        Storage::disk('public')->assertMissing($filePath);
    }

    /**
     * 12. User chưa có avatar vẫn gọi API thành công (graceful response).
     */
    public function test_user_without_avatar_can_call_delete_avatar_gracefully(): void
    {
        $user = User::factory()->create(['anh_dai_dien' => null]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/me/avatar');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã xóa ảnh đại diện!',
                'data'    => [
                    'anh_dai_dien' => null,
                    'avatar_url'   => null,
                ],
            ]);
    }

    /**
     * 13. User đổi mật khẩu thành công với current_password hợp lệ.
     */
    public function test_authenticated_user_can_update_password_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me/password', [
                'current_password'      => 'oldpassword123',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!',
            ]);

        $user->refresh();

        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertFalse(Hash::check('oldpassword123', $user->password));
    }

    /**
     * 14. Token hiện tại tiếp tục hoạt động (không bị thu hồi / logout) sau khi đổi mật khẩu.
     */
    public function test_existing_sanctum_token_remains_valid_after_password_change(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Đổi mật khẩu
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me/password', [
                'current_password'      => 'oldpassword123',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200);

        // Gọi lại API GET /api/me bằng chính token đó
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id' => $user->id,
                ],
            ]);
    }

    /**
     * 15. Đổi mật khẩu với current_password sai nhận lỗi 422.
     */
    public function test_update_password_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me/password', [
                'current_password'      => 'wrongpassword',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /**
     * 16. Đổi mật khẩu với mật khẩu mới < 8 ký tự nhận lỗi 422.
     */
    public function test_update_password_fails_if_new_password_is_too_short(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me/password', [
                'current_password'      => 'oldpassword123',
                'password'              => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 17. Đổi mật khẩu không trùng khớp password_confirmation nhận lỗi 422.
     */
    public function test_update_password_fails_if_password_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/me/password', [
                'current_password'      => 'oldpassword123',
                'password'              => 'newpassword123',
                'password_confirmation' => 'differentpassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 18. Guest gọi PUT /api/me/password nhận lỗi 401.
     */
    public function test_unauthenticated_guest_cannot_update_password(): void
    {
        $response = $this->putJson('/api/me/password', [
            'current_password'      => 'oldpassword123',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(401);
    }
}
