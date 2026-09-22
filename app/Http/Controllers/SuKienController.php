<?php

namespace App\Http\Controllers;

use App\Models\SuKien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuKienController extends Controller
{
    /**
     * Danh sách sự kiện của user hiện tại
     */
    public function index(): JsonResponse
    {
        $suKiens = SuKien::where('user_id', Auth::id())
            ->orderBy('thoi_gian_bat_dau', 'asc')
            ->get();

        $data = $suKiens->map(function ($evt) {
            return [
                'id' => $evt->id,
                'title' => $evt->tieu_de,
                'start' => $evt->thoi_gian_bat_dau ? $evt->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $evt->thoi_gian_ket_thuc ? $evt->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $evt->loai_su_kien),
                'color' => $evt->mau_hien_thi ?? '#3B82F6',
                'location' => $evt->mo_ta,
                'bat_thong_bao' => (bool) $evt->bat_thong_bao,
                'so_ngay_nhac' => (int) ($evt->so_ngay_nhac ?? 1),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Tạo mới sự kiện cho user hiện tại
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'loai_su_kien' => 'required|string',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after_or_equal:thoi_gian_bat_dau',
            'mo_ta' => 'nullable|string',
            'mau_hien_thi' => 'nullable|string',
            'bat_thong_bao' => 'nullable|boolean',
            'so_ngay_nhac' => 'nullable|integer|in:1,2',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);

        $suKien = SuKien::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm sự kiện thành công.',
            'data' => [
                'id' => $suKien->id,
                'title' => $suKien->tieu_de,
                'start' => $suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $suKien->loai_su_kien),
                'color' => $suKien->mau_hien_thi ?? '#3B82F6',
                'location' => $suKien->mo_ta,
                'bat_thong_bao' => (bool) $suKien->bat_thong_bao,
                'so_ngay_nhac' => (int) ($suKien->so_ngay_nhac ?? 1),
            ],
        ], 201);
    }

    /**
     * Cập nhật sự kiện của user hiện tại (Anti-IDOR)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'loai_su_kien' => 'required|string',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after_or_equal:thoi_gian_bat_dau',
            'mo_ta' => 'nullable|string',
            'mau_hien_thi' => 'nullable|string',
            'bat_thong_bao' => 'nullable|boolean',
            'so_ngay_nhac' => 'nullable|integer|in:1,2',
        ]);

        $validated['loai_su_kien'] = str_replace('-', '_', $validated['loai_su_kien']);
        $validated['bat_thong_bao'] = $request->boolean('bat_thong_bao');
        $validated['so_ngay_nhac'] = $request->input('so_ngay_nhac', 1);

        $suKien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sự kiện thành công.',
            'data' => [
                'id' => $suKien->id,
                'title' => $suKien->tieu_de,
                'start' => $suKien->thoi_gian_bat_dau ? $suKien->thoi_gian_bat_dau->toIso8601String() : null,
                'end' => $suKien->thoi_gian_ket_thuc ? $suKien->thoi_gian_ket_thuc->toIso8601String() : null,
                'type' => str_replace('_', '-', $suKien->loai_su_kien),
                'color' => $suKien->mau_hien_thi ?? '#3B82F6',
                'location' => $suKien->mo_ta,
                'bat_thong_bao' => (bool) $suKien->bat_thong_bao,
                'so_ngay_nhac' => (int) ($suKien->so_ngay_nhac ?? 1),
            ],
        ]);
    }

    /**
     * Xóa sự kiện (Soft Delete) của user hiện tại (Anti-IDOR)
     */
    public function destroy(int $id): JsonResponse
    {
        $suKien = SuKien::where('user_id', Auth::id())->findOrFail($id);
        $suKien->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sự kiện thành công.',
        ]);
    }
}
