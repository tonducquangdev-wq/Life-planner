<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDuAnRequest;
use App\Models\DuAn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DuAnController extends Controller
{
    /**
     * Danh sách dự án
     */
    public function index(Request $request)
    {
        $query = DuAn::where('user_id', Auth::id())
            ->withAvg('congViecs as tien_do_trung_binh', 'tien_do')
            ->withCount([
                'congViecs as tong_cong_viec_count',
                'congViecs as cong_viec_hoan_thanh_count' => function ($q) {
                    $q->where('trang_thai', 'hoan_thanh');
                },
            ]);

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->input('trang_thai'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('ten_du_an', 'like', "%{$search}%");
        }

        $duAns = $query->orderBy('created_at', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $duAns,
            ]);
        }

        return view('du_an.index', compact('duAns'));
    }

    /**
     * Tạo mới dự án
     */
    public function store(StoreDuAnRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $duAn = DuAn::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo dự án thành công.',
                'data' => $duAn,
            ], 201);
        }

        return redirect()->route('du-an.index')->with('success', 'Tạo dự án thành công.');
    }

    /**
     * Chi tiết dự án + danh sách công việc thuộc dự án
     */
    public function show(Request $request, int $id)
    {
        $duAn = DuAn::where('user_id', Auth::id())
            ->withAvg('congViecs as tien_do_trung_binh', 'tien_do')
            ->withCount([
                'congViecs as tong_cong_viec_count',
                'congViecs as cong_viec_hoan_thanh_count' => function ($q) {
                    $q->where('trang_thai', 'hoan_thanh');
                },
            ])
            ->findOrFail($id);

        $congViecs = $duAn->congViecs()
            ->where('user_id', Auth::id())
            ->with('suKien')
            ->orderBy('deadline', 'asc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'du_an' => $duAn,
                    'cong_viec' => $congViecs,
                ],
            ]);
        }

        return view('du_an.show', compact('duAn', 'congViecs'));
    }

    /**
     * Cập nhật dự án
     */
    public function update(StoreDuAnRequest $request, int $id)
    {
        $duAn = DuAn::where('user_id', Auth::id())->findOrFail($id);
        $duAn->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật dự án thành công.',
                'data' => $duAn,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật dự án thành công.');
    }

    /**
     * Xóa dự án (Soft Delete)
     */
    public function destroy(Request $request, int $id)
    {
        $duAn = DuAn::where('user_id', Auth::id())->findOrFail($id);
        $duAn->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa dự án thành công.',
            ]);
        }

        return redirect()->route('du-an.index')->with('success', 'Xóa dự án thành công.');
    }
}
