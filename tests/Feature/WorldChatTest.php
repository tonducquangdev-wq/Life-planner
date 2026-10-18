<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorldChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorldChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Khách chưa đăng nhập không thể truy cập Kênh Chat Thế Giới.
     */
    public function test_guest_cannot_access_or_post_to_world_chat(): void
    {
        $getResponse = $this->getJson(route('world-chat.index'));
        $getResponse->assertUnauthorized();

        $postResponse = $this->postJson(route('world-chat.store'), [
            'noi_dung' => 'Xin chào!',
        ]);
        $postResponse->assertUnauthorized();
    }

    /**
     * Người dùng đã đăng nhập có thể tải danh sách tin nhắn.
     */
    public function test_authenticated_user_can_fetch_world_chat_messages(): void
    {
        $user = User::factory()->create(['ho_ten' => 'Người Dùng A']);

        $response = $this->actingAs($user)->getJson(route('world-chat.index'));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'messages',
            'last_id',
            'online_count',
        ]);
        $response->assertJson(['success' => true]);
    }

    /**
     * Người dùng có thể gửi tin nhắn lên kênh thế giới và được lưu vào CSDL.
     */
    public function test_authenticated_user_can_send_world_chat_message(): void
    {
        $user = User::factory()->create(['ho_ten' => 'Tôn Đức Quang']);

        $payload = [
            'noi_dung' => 'Hôm nay tập ngực căng quá anh em ơi! 🔥',
        ];

        $response = $this->actingAs($user)->postJson(route('world-chat.store'), $payload);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'author' => 'Tôn Đức Quang',
                'text' => 'Hôm nay tập ngực căng quá anh em ơi! 🔥',
                'is_me' => true,
            ],
        ]);

        $this->assertDatabaseHas('world_chat_messages', [
            'user_id' => $user->id,
            'noi_dung' => 'Hôm nay tập ngực căng quá anh em ơi! 🔥',
        ]);
    }

    /**
     * Tin nhắn được phân biệt đúng người gửi (is_me = true cho người gửi, false cho người khác).
     */
    public function test_world_chat_messages_distinguish_sender_correctly(): void
    {
        $user1 = User::factory()->create(['ho_ten' => 'Người Gửi 1']);
        $user2 = User::factory()->create(['ho_ten' => 'Người Gửi 2']);

        $msg1 = WorldChatMessage::create([
            'user_id' => $user1->id,
            'noi_dung' => 'Tin của người 1',
        ]);

        // User 2 xem tin nhắn
        $response = $this->actingAs($user2)->getJson(route('world-chat.index'));
        $response->assertOk();

        $messages = collect($response->json('messages'));
        $targetMsg = $messages->firstWhere('id', $msg1->id);

        $this->assertNotNull($targetMsg);
        $this->assertFalse($targetMsg['is_me']);
        $this->assertEquals('Người Gửi 1', $targetMsg['author']);

        // User 1 xem lại tin nhắn của chính mình
        $responseSelf = $this->actingAs($user1)->getJson(route('world-chat.index'));
        $messagesSelf = collect($responseSelf->json('messages'));
        $targetMsgSelf = $messagesSelf->firstWhere('id', $msg1->id);

        $this->assertTrue($targetMsgSelf['is_me']);
    }

    /**
     * Polling với after_id chỉ trả về các tin nhắn mới hơn.
     */
    public function test_polling_with_after_id_only_returns_newer_messages(): void
    {
        $user = User::factory()->create();

        $msg1 = WorldChatMessage::create(['user_id' => $user->id, 'noi_dung' => 'Tin cũ']);
        $msg2 = WorldChatMessage::create(['user_id' => $user->id, 'noi_dung' => 'Tin mới']);

        $response = $this->actingAs($user)->getJson(route('world-chat.index', ['after_id' => $msg1->id]));
        $response->assertOk();

        $messages = $response->json('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals($msg2->id, $messages[0]['id']);
        $this->assertEquals('Tin mới', $messages[0]['text']);
    }

    /**
     * Validation từ chối tin nhắn rỗng hoặc quá 1000 ký tự.
     */
    public function test_validation_rejects_empty_or_too_long_message(): void
    {
        $user = User::factory()->create();

        // Rỗng
        $responseEmpty = $this->actingAs($user)->postJson(route('world-chat.store'), [
            'noi_dung' => '   ',
        ]);
        $responseEmpty->assertStatus(422);

        // Quá 1000 ký tự
        $responseLong = $this->actingAs($user)->postJson(route('world-chat.store'), [
            'noi_dung' => str_repeat('a', 1001),
        ]);
        $responseLong->assertStatus(422);
    }
}
