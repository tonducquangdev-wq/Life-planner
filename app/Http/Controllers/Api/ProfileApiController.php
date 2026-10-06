<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileApiController extends Controller
{
    /**
     * Format user response payload for profile.
     */
    private function formatUser(User $user): array
    {
        return [
            'id'                  => $user->id,
            'ho_ten'              => $user->ho_ten,
            'email'               => $user->email,
            'anh_dai_dien'        => $user->anh_dai_dien,
            'avatar_url'          => $user->avatar_url,
            'initials'            => $user->initials,
            'ngon_ngu'            => $user->ngon_ngu ?? 'vi',
            'giao_dien'           => $user->giao_dien ?? 'system',
            'thong_bao_enabled'   => (bool) $user->thong_bao_enabled,
            'thong_bao_lich_hoc'  => (bool) $user->thong_bao_lich_hoc,
            'thong_bao_deadline'  => (bool) $user->thong_bao_deadline,
            'thong_bao_tap_luyen' => (bool) $user->thong_bao_tap_luyen,
            'am_thanh_thong_bao'  => (bool) $user->am_thanh_thong_bao,
        ];
    }

    /**
     * PUT /api/me
     * Cập nhật thông tin trang cá nhân của user đang đăng nhập.
     */
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'ho_ten'              => 'sometimes|required|string|max:255',
            'email'               => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'ngon_ngu'            => 'sometimes|in:vi,en',
            'giao_dien'           => 'sometimes|in:light,dark,system',
            'thong_bao_enabled'   => 'sometimes|boolean',
            'thong_bao_lich_hoc'  => 'sometimes|boolean',
            'thong_bao_deadline'  => 'sometimes|boolean',
            'thong_bao_tap_luyen' => 'sometimes|boolean',
            'am_thanh_thong_bao'  => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin trang cá nhân thành công!',
            'data'    => $this->formatUser($user->fresh()),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/me/avatar
     * Upload ảnh đại diện mới cho user.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn một tệp hình ảnh.',
            'avatar.image'    => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'avatar.mimes'    => 'Ảnh đại diện phải là file JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max'      => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        /** @var User $user */
        $user = $request->user();
        $oldAvatar = $user->anh_dai_dien;

        // 1. Lưu ảnh mới vào disk public (storage/app/public/avatars/{hash}.{ext})
        $newPath = $request->file('avatar')->store('avatars', 'public');

        // 2. Cập nhật database với đường dẫn tương đối
        $user->anh_dai_dien = $newPath;
        $user->save();

        // 3. Xóa avatar cũ khỏi storage nếu tồn tại
        if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'data'    => [
                'anh_dai_dien' => $user->anh_dai_dien,
                'avatar_url'   => $user->avatar_url,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/me/avatar
     * Xóa ảnh đại diện hiện tại của user.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $oldAvatar = $user->anh_dai_dien;

        if ($oldAvatar) {
            if (Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
            $user->anh_dai_dien = null;
            $user->save();
        }

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ảnh đại diện!',
            'data'    => [
                'anh_dai_dien' => null,
                'avatar_url'   => null,
                'initials'     => $user->initials,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/me/password
     * Đổi mật khẩu cho user đang đăng nhập.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required'         => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required'                 => 'Vui lòng nhập mật khẩu mới.',
            'password.min'                      => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed'                => 'Mật khẩu xác nhận không khớp.',
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
