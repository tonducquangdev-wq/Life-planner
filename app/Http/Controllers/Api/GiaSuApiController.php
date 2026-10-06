<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiaSu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GiaSuApiController extends Controller
{
    /**
     * GET /api/gia-su : Lấy danh sách gia sư cho Mobile App, hỗ trợ tìm kiếm, lọc môn học, lọc mức giá & phân trang.
     */
    public function index(Request $request): JsonResponse
    {
        $keyword = trim($request->input('q', $request->input('search', '')));
        $monHocId = $request->input('mon_hoc_id');
        $mucGia = $request->input('muc_gia');
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $query = GiaSu::with('monHoc');

        if (! empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%{$keyword}%")
                  ->orWhere('chuyen_mon', 'like', "%{$keyword}%")
                  ->orWhere('mo_ta_kinh_nghiem', 'like', "%{$keyword}%");
            });
        }

        if (! empty($monHocId)) {
            $query->where('mon_hoc_id', $monHocId);
        }

        if ($mucGia === 'duoi_180') {
            $query->where('hoc_phi_theo_gio', '<=', 180000);
        } elseif ($mucGia === 'tren_180') {
            $query->where('hoc_phi_theo_gio', '>', 180000);
        }

        $giaSus = $query->orderByDesc('danh_gia')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách gia sư thành công!',
            'data'    => $giaSus,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
