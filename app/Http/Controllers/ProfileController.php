<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Vui lòng chọn một tệp hình ảnh.',
            'avatar.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'avatar.mimes' => 'Ảnh đại diện phải là file JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        $user = $request->user();
        $oldAvatar = $user->anh_dai_dien;

        // 1. Lưu ảnh mới vào disk public (storage/app/public/avatars/{hash}.{ext})
        $newPath = $request->file('avatar')->store('avatars', 'public');

        // 2. Cập nhật database với đường dẫn tương đối
        $user->anh_dai_dien = $newPath;
        $user->save();

        // 3. Xóa avatar cũ khỏi storage nếu an toàn và tồn tại
        if ($oldAvatar && str_starts_with($oldAvatar, 'avatars/') && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->thong_bao_enabled = $request->has('thong_bao_enabled');
        $user->thong_bao_lich_hoc = $request->has('thong_bao_lich_hoc');
        $user->thong_bao_deadline = $request->has('thong_bao_deadline');
        $user->thong_bao_tap_luyen = $request->has('thong_bao_tap_luyen');
        $user->am_thanh_thong_bao = $request->has('am_thanh_thong_bao');

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Quick toggle main notification setting via AJAX.
     */
    public function quickToggleNotification(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $enabled = $request->has('enabled') ? $request->boolean('enabled') : ! $user->thong_bao_enabled;
        $user->thong_bao_enabled = $enabled;
        $user->save();

        return response()->json([
            'success' => true,
            'enabled' => $user->thong_bao_enabled,
            'message' => $user->thong_bao_enabled ? 'Đã bật thông báo!' : 'Đã tắt thông báo!',
        ]);
    }

    /**
     * Update interface language & theme preferences.
     */
    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'ngon_ngu' => ['required', 'in:vi,en'],
            'giao_dien' => ['required', 'in:light,dark,system'],
        ]);

        $user = $request->user();
        $user->ngon_ngu = $request->input('ngon_ngu', 'vi');
        $user->giao_dien = $request->input('giao_dien', 'light');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'appearance-updated');
    }

    /**
     * Quick toggle dark mode via AJAX.
     */
    public function quickToggleTheme(Request $request)
    {
        $user = $request->user();
        $theme = $request->input('giao_dien', 'light');

        if ($user) {
            $user->giao_dien = $theme;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'giao_dien' => $theme,
            'message' => 'Đã cập nhật chế độ giao diện!',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
