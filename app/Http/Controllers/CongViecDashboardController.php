<?php

namespace App\Http\Controllers;

use App\Models\CongViec;
use App\Models\DuAn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CongViecDashboardController extends Controller
{
    /**
     * Dashboard tổng quan chỉ số Công việc & Dự án
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Chỉ số tổng quan
        $tongSoCongViec = CongViec::where('user_id', $userId)->count();
        $congViecDangLam = CongViec::where('user_id', $userId)->where('trang_thai', 'dang_lam')->count();
        $congViecCanLam = CongViec::where('user_id', $userId)->where('trang_thai', 'can_lam')->count();
        $congViecChoDuyet = CongViec::where('user_id', $userId)->where('trang_thai', 'cho_duyet')->count();
        $congViecHoanThanh = CongViec::where('user_id', $userId)->where('trang_thai', 'hoan_thanh')->count();

        // Công việc quá hạn (Trạng thái khác hoan_thanh + deadline < now())
        $congViecQuaHan = CongViec::where('user_id', $userId)
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        // 2. Danh sách dự án nổi bật (kèm thống kê tiến độ trung bình)
        $duAns = DuAn::where('user_id', $userId)
            ->withAvg('congViecs as tien_do_trung_binh', 'tien_do')
            ->withCount([
                'congViecs as tong_cong_viec_count',
                'congViecs as cong_viec_hoan_thanh_count' => function ($q) {
                    $q->where('trang_thai', 'hoan_thanh');
                },
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 3. Công việc sắp hết hạn hoặc quá hạn
        $congViecGap = CongViec::where('user_id', $userId)
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('deadline')
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        $stats = [
            'tong_cong_viec' => $tongSoCongViec,
            'can_lam' => $congViecCanLam,
            'dang_lam' => $congViecDangLam,
            'cho_duyet' => $congViecChoDuyet,
            'hoan_thanh' => $congViecHoanThanh,
            'qua_han' => $congViecQuaHan,
            'ty_le_hoan_thanh' => $tongSoCongViec > 0 ? (int) round(($congViecHoanThanh / $tongSoCongViec) * 100) : 0,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'du_an_noi_bat' => $duAns,
                    'cong_viec_gap' => $congViecGap,
                ],
            ]);
        }

        return view('cong_viec.dashboard', compact('stats', 'duAns', 'congViecGap'));
    }
}
