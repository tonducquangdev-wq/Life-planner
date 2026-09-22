<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    /**
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác.',
            ], 401, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        // Tạo Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Đăng nhập thành công!',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'           => $user->id,
                'ho_ten'       => $user->ho_ten,
                'email'        => $user->email,
                'anh_dai_dien' => $user->avatar_url,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Thu hồi token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/me hoặc GET /api/user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tài khoản thành công!',
            'data'    => [
                'id'                 => $user->id,
                'ho_ten'             => $user->ho_ten,
                'email'              => $user->email,
                'anh_dai_dien'       => $user->avatar_url,
                'initials'           => $user->initials,
                'thong_bao_enabled'  => $user->thong_bao_enabled,
                'thong_bao_lich_hoc' => $user->thong_bao_lich_hoc,
                'thong_bao_deadline' => $user->thong_bao_deadline,
                'thong_bao_tap_luyen' => $user->thong_bao_tap_luyen,
                'ngon_ngu'           => $user->ngon_ngu,
                'giao_dien'          => $user->giao_dien,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/user (Alias)
     */
    public function user(Request $request): JsonResponse
    {
        return $this->me($request);
    }
}
