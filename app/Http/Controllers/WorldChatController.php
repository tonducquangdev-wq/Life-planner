<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorldChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldChatController extends Controller
{
    /**
     * Lấy danh sách tin nhắn kênh thế giới (hỗ trợ polling sau last_id).
     */
    public function index(Request $request): JsonResponse
    {
        $currentUserId = Auth::id();
        $afterId = $request->query('after_id');

        // Khởi tạo tin nhắn chào mừng mẫu nếu hệ thống chưa có tin nhắn nào
        if (! $afterId && WorldChatMessage::count() === 0) {
            $starterUser = User::first() ?? Auth::user();
            if ($starterUser) {
                WorldChatMessage::create([
                    'user_id' => $starterUser->id,
                    'noi_dung' => 'Chào mừng tất cả anh em đến với Kênh Chat Thế Giới của Life Planner! Cùng nhau tập luyện và chia sẻ tiến độ nhé! 💪🔥',
                ]);
            }
        }

        if ($afterId) {
            $messages = WorldChatMessage::with('user')
                ->where('id', '>', (int) $afterId)
                ->orderBy('id', 'asc')
                ->limit(50)
                ->get();
        } else {
            $messages = WorldChatMessage::with('user')
                ->latest('id')
                ->limit(50)
                ->get()
                ->reverse()
                ->values();
        }

        $formatted = $messages->map(fn (WorldChatMessage $m) => $m->formatForUser($currentUserId))->values();
        $onlineCount = max(User::count() + 5, 8);

        return response()->json([
            'success' => true,
            'messages' => $formatted,
            'last_id' => $messages->last()?->id ?? (int) $afterId,
            'online_count' => $onlineCount,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gửi tin nhắn mới lên kênh thế giới.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'noi_dung' => ['required', 'string', 'min:1', 'max:1000'],
        ], [
            'noi_dung.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'noi_dung.max' => 'Nội dung tin nhắn không được vượt quá 1000 ký tự.',
        ]);

        $message = WorldChatMessage::create([
            'user_id' => Auth::id(),
            'noi_dung' => trim($validated['noi_dung']),
        ]);

        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi tin nhắn!',
            'data' => $message->formatForUser(Auth::id()),
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }
}
