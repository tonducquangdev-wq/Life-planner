<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $payload = [
            'ho_ten'                => 'Nguyễn Văn A',
            'email'                 => 'mobile_user@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success'    => true,
                'message'    => 'Đăng ký tài khoản thành công!',
                'token_type' => 'Bearer',
                'user'       => [
                    'ho_ten'       => 'Nguyễn Văn A',
                    'email'        => 'mobile_user@example.com',
                    'anh_dai_dien' => null,
                    'avatar_url'   => null,
                    'initials'     => 'NA',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'mobile_user@example.com',
        ]);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertArrayNotHasKey('token', $response->json());
    }

    public function test_register_validation_errors(): void
    {
        // Email đã tồn tại
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/register', [
            'ho_ten'                => 'Test User',
            'email'                 => 'duplicate@example.com',
            'password'              => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_login_via_api(): void
    {
        $user = User::factory()->create([
            'ho_ten'   => 'Trần Văn B',
            'email'    => 'login_user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'login_user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'    => true,
                'message'    => 'Đăng nhập thành công!',
                'token_type' => 'Bearer',
                'user'       => [
                    'id'           => $user->id,
                    'ho_ten'       => 'Trần Văn B',
                    'email'        => 'login_user@example.com',
                    'anh_dai_dien' => null,
                    'avatar_url'   => null,
                    'initials'     => 'TB',
                ],
            ]);

        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_user_can_get_me_info_via_api(): void
    {
        $user = User::factory()->create([
            'ho_ten' => 'Lê Thị C',
            'email'  => 'me_user@example.com',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lấy thông tin tài khoản thành công!',
                'data'    => [
                    'id'       => $user->id,
                    'ho_ten'   => 'Lê Thị C',
                    'email'    => 'me_user@example.com',
                    'initials' => 'LC',
                ],
            ]);
    }

    public function test_user_can_logout_via_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng xuất thành công!',
            ]);
    }
}
