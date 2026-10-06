<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * GET /api/thong-bao
     * Lấy danh sách thông báo của user hiện tại.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->thongBaos()
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $user->thongBaos()
            ->where('da_doc', false)
            ->count();

        return response()->json([
            'success'      => true,
            'message'      => 'Lấy danh sách thông báo thành công!',
            'data'         => $notifications,
            'unread_count' => $unreadCount,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * PATCH /api/thong-bao/read-all
     * Đánh dấu tất cả thông báo của user hiện tại là đã đọc.
     */
    public function readAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $updatedCount = $user->thongBaos()
            ->where('da_doc', false)
            ->update(['da_doc' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc!',
            'data'    => [
                'updated_count' => $updatedCount,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * PATCH /api/thong-bao/{id}/read
     * Đánh dấu một thông báo là đã đọc.
     */
    public function markAsRead(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();

        /** @var ThongBao|null $thongBao */
        $thongBao = $user->thongBaos()->find($id);

        if (! $thongBao) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo hoặc bạn không có quyền truy cập.',
            ], 404, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $thongBao->update(['da_doc' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh dấu thông báo đã đọc thành công!',
            'data'    => $thongBao->fresh(),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
