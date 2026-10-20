<?php

namespace App\Http\Controllers;

use App\Models\BaiTap;
use App\Models\GiaSu;
use App\Models\MonHoc;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MonHocController extends Controller
{
    /**
     * Hiển thị danh sách môn học của người dùng đăng nhập
     */
    public function index()
    {
        $userId = Auth::id();

        $monHocs = MonHoc::where('user_id', $userId)
            ->withCount('baiTaps')
            ->with(['baiTaps', 'lichHocs'])
            ->latest()
            ->get();

        // Tự động khởi tạo dữ liệu chuẩn theo hình ảnh mẫu nếu người dùng chưa có môn học nào
        if ($monHocs->isEmpty()) {
            $sampleData = [
                [
                    'ten_mon' => 'Lập trình Web máy chủ',
                    'ma_mon' => 'WEB301',
                    'giang_vien' => 'GV. Trương Châu Long',
                    'phong_hoc' => 'A204',
                    'so_tin_chi' => 3,
                    'ngay_bat_dau' => '2026-10-15',
                    'ngay_ket_thuc' => '2026-11-06',
                    'mau_sac' => '#3b82f6',
                    'trang_thai' => 'dang_hoc',
                    'dow' => [5], // T6
                    'start_time' => '07:00',
                    'end_time' => '10:01',
                ],
                [
                    'ten_mon' => 'Chuyên đề Front-End',
                    'ma_mon' => 'FED202',
                    'giang_vien' => 'GV. Trần Phương Duy',
                    'phong_hoc' => 'A204',
                    'so_tin_chi' => 3,
                    'ngay_bat_dau' => '2026-10-15',
                    'ngay_ket_thuc' => '2026-11-04',
                    'mau_sac' => '#3b82f6',
                    'trang_thai' => 'dang_hoc',
                    'dow' => [3], // T4
                    'start_time' => '07:00',
                    'end_time' => '11:00',
                ],
                [
                    'ten_mon' => 'Hệ quản trị CSDL',
                    'ma_mon' => 'DB201',
                    'giang_vien' => 'GV. Trương Châu Long',
                    'phong_hoc' => 'A204',
                    'so_tin_chi' => 3,
                    'ngay_bat_dau' => '2026-10-15',
                    'ngay_ket_thuc' => '2027-01-04',
                    'mau_sac' => '#3b82f6',
                    'trang_thai' => 'dang_hoc',
                    'dow' => [2], // T3
                    'start_time' => '07:00',
                    'end_time' => '11:00',
                ],
                [
                    'ten_mon' => 'Chuyên đề PHP',
                    'ma_mon' => 'PHP401',
                    'giang_vien' => 'GV. Huỳnh Luân',
                    'phong_hoc' => 'A204',
                    'so_tin_chi' => 3,
                    'ngay_bat_dau' => '2026-10-15',
                    'ngay_ket_thuc' => '2026-11-03',
                    'mau_sac' => '#3b82f6',
                    'trang_thai' => 'dang_hoc',
                    'dow' => [1], // T2
                    'start_time' => '07:00',
                    'end_time' => '11:00',
                ],
                [
                    'ten_mon' => 'CD Back-End',
                    'ma_mon' => 'BED301',
                    'giang_vien' => 'GV. Nguyễn Minh Hải',
                    'phong_hoc' => 'A204',
                    'so_tin_chi' => 3,
                    'ngay_bat_dau' => '2026-10-15',
                    'ngay_ket_thuc' => '2026-10-29',
                    'mau_sac' => '#3b82f6',
                    'trang_thai' => 'dang_hoc',
                    'dow' => [4], // T5
                    'start_time' => '07:00',
                    'end_time' => '11:00',
                ],
            ];

            foreach (array_reverse($sampleData) as $item) {
                $dow = $item['dow'];
                $st = $item['start_time'];
                $et = $item['end_time'];
                unset($item['dow'], $item['start_time'], $item['end_time']);

                $item['user_id'] = $userId;
                $mh = MonHoc::create($item);
                $mh->syncToCalendar($dow, $st, $et, true);

                if ($mh->ten_mon === 'Chuyên đề PHP') {
                    $mh->baiTaps()->create([
                        'tieu_de' => 'Báo cáo cuối kì CD-PHP',
                        'mo_ta' => 'Nộp báo cáo bài tập lớn cuối kỳ môn Chuyên đề PHP',
                        'han_nop' => '2026-10-19 19:00:00',
                        'muc_do_uu_tien' => 'cao',
                        'trang_thai' => 'chua_hoan_thanh',
                    ]);

                    SuKien::create([
                        'user_id' => $userId,
                        'mon_hoc_id' => $mh->id,
                        'tieu_de' => 'Deadline: Báo cáo cuối kì CD-PHP',
                        'mo_ta' => 'Hạn nộp bài tập môn Chuyên đề PHP',
                        'loai_su_kien' => 'deadline',
                        'thoi_gian_bat_dau' => '2026-10-19 19:00:00',
                        'thoi_gian_ket_thuc' => '2026-10-19 20:00:00',
                        'mau_hien_thi' => '#ef4444',
                        'bat_thong_bao' => true,
                        'so_ngay_nhac' => 1,
                        'quy_tac_lap' => 'once',
                    ]);
                }
            }

            $monHocs = MonHoc::where('user_id', $userId)
                ->withCount('baiTaps')
                ->with(['baiTaps', 'lichHocs'])
                ->latest()
                ->get();
        }

        $baiTaps = BaiTap::whereHas('monHoc', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with('monHoc')
            ->orderByRaw("CASE WHEN trang_thai = 'da_hoan_thanh' THEN 1 ELSE 0 END ASC")
            ->orderBy('han_nop', 'asc')
            ->get();

        return view('mon_hoc.index', compact('monHocs', 'baiTaps'));
    }

    /**
     * Hiển thị form tạo mới môn học
     */
    public function create()
    {
        return view('mon_hoc.create');
    }

    /**
     * Lưu môn học mới vào CSDL
     */
    /**
     * Chuẩn hóa định dạng ngày từ d/m/Y hoặc Y-m-d về chuẩn Y-m-d.
     * Trả về null nếu giá trị rỗng.
     */
    protected function normalizeDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // Trường hợp đã là Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Trường hợp dd/mm/yyyy hoặc dd-mm-yyyy
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $matches)) {
            return sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Lưu môn học mới vào CSDL
     */
    public function store(Request $request)
    {
        // Chuẩn hóa ngày trước khi validate để tránh xung đột định dạng d/m/Y vs Y-m-d
        $rawStart = $request->input('ngay_bat_dau');
        $rawEnd = $request->input('ngay_ket_thuc');

        $normalizedStart = $this->normalizeDate($rawStart);
        $normalizedEnd = $this->normalizeDate($rawEnd);

        // Tự động tạo mã môn nếu người dùng để trống
        $maMon = trim((string) $request->input('ma_mon'));
        if ($maMon === '') {
            $rawLetters = preg_replace('/[^A-Za-z0-9]/', '', (string) $request->input('ten_mon', ''));
            $slug = strtoupper(substr($rawLetters, 0, 4));
            $maMon = ($slug ?: 'MH').rand(100, 999);
        }

        $request->merge([
            'ma_mon' => $maMon,
            'ngay_bat_dau' => $normalizedStart,
            'ngay_ket_thuc' => $normalizedEnd,
            'so_tin_chi' => $request->input('so_tin_chi') ? (int) $request->input('so_tin_chi') : 3,
            'tien_do' => $request->input('tien_do') !== null ? (int) $request->input('tien_do') : 0,
            'trang_thai' => $request->input('trang_thai') ?: 'dang_hoc',
            'mau_sac' => $request->input('mau_sac') ?: '#6366f1',
        ]);

        $rules = [
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'nullable|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        // Chỉ kiểm tra after_or_equal khi CẢ HAI ngày đều được cung cấp
        if ($normalizedStart && $normalizedEnd) {
            $rules['ngay_ket_thuc'] = 'nullable|date|after_or_equal:ngay_bat_dau';
        } else {
            $rules['ngay_ket_thuc'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'ten_mon.required' => 'Vui lòng nhập tên môn học.',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ], [
            'ten_mon' => 'tên môn học',
            'ma_mon' => 'mã môn học',
            'ngay_bat_dau' => 'ngày bắt đầu',
            'ngay_ket_thuc' => 'ngày kết thúc',
        ]);

        // Gán user_id bằng Auth::id() và tạo môn học
        $validated['user_id'] = Auth::id();
        $validated['mau_sac'] = $request->input('mau_sac', '#6366f1');
        $validated['tien_do'] = (int) $request->input('tien_do', 0);
        $validated['so_tin_chi'] = (int) $request->input('so_tin_chi', 3);
        $validated['ngay_bat_dau'] = $normalizedStart;
        $validated['ngay_ket_thuc'] = $normalizedEnd;

        $monHoc = MonHoc::create($validated);

        // Tự động đồng bộ môn học vào bảng lịch học (lich_hoc) và trang Lịch trình (su_kien)
        $dongBoCalendar = $request->has('dong_bo_calendar') ? $request->boolean('dong_bo_calendar') : true;
        $daysOfWeek = $request->input('ngay_trong_tuan', []);
        $gioBatDau = $request->input('gio_bat_dau', '08:00');
        $gioKetThuc = $request->input('gio_ket_thuc', '10:30');
        $gioBatDauTheoThu = $request->input('gio_bat_dau_theo_thu');
        $gioKetThucTheoThu = $request->input('gio_ket_thuc_theo_thu');

        $eventsCount = $monHoc->syncToCalendar($daysOfWeek, $gioBatDau, $gioKetThuc, $dongBoCalendar, $gioBatDauTheoThu, $gioKetThucTheoThu);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Môn học đã được thêm thành công'.($dongBoCalendar ? " và tự động đồng bộ {$eventsCount} buổi vào Trang Lịch!" : '!'),
                'events_created' => $eventsCount,
                'data' => [
                    'id' => $monHoc->id,
                    'ma_mon' => $monHoc->ma_mon,
                    'ten_mon' => $monHoc->ten_mon,
                    'giang_vien' => $monHoc->giang_vien,
                    'phong_hoc' => $monHoc->phong_hoc,
                    'so_tin_chi' => $monHoc->so_tin_chi,
                    'tien_do' => $monHoc->tien_do,
                    'ngay_bat_dau' => $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : null,
                    'ngay_ket_thuc' => $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('d/m/Y') : null,
                    'mau_sac' => $monHoc->mau_sac,
                    'trang_thai' => $monHoc->trang_thai,
                    'show_url' => route('mon-hoc.show', $monHoc),
                    'edit_url' => route('mon-hoc.edit', $monHoc),
                    'destroy_url' => route('mon-hoc.destroy', $monHoc),
                ],
            ]);
        }

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Thêm môn học thành công'.($dongBoCalendar ? ' và tự động thêm vào Lịch trình!' : '!'));
    }

    /**
     * Xem chi tiết môn học
     */
    public function show(MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        $monHoc->load(['baiTaps', 'lichHocs', 'suKiens']);

        return view('mon_hoc.show', compact('monHoc'));
    }

    /**
     * Hiển thị form chỉnh sửa môn học
     */
    public function edit(MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        return view('mon_hoc.edit', compact('monHoc'));
    }

    /**
     * Cập nhật thông tin môn học
     */
    public function update(Request $request, MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        $hasStart = $request->has('ngay_bat_dau');
        $hasEnd = $request->has('ngay_ket_thuc');

        $normalizedStart = $hasStart ? $this->normalizeDate($request->input('ngay_bat_dau')) : null;
        $normalizedEnd = $hasEnd ? $this->normalizeDate($request->input('ngay_ket_thuc')) : null;

        $mergeData = [];
        if ($hasStart) {
            $mergeData['ngay_bat_dau'] = $normalizedStart;
        }
        if ($hasEnd) {
            $mergeData['ngay_ket_thuc'] = $normalizedEnd;
        }
        $maMon = trim((string) $request->input('ma_mon'));
        if ($maMon === '') {
            $maMon = $monHoc->ma_mon ?: (strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->input('ten_mon', 'MH')), 0, 4)).rand(100, 999));
        }
        $mergeData['ma_mon'] = $maMon;

        if (! $request->has('so_tin_chi')) {
            $mergeData['so_tin_chi'] = $monHoc->so_tin_chi ?? 3;
        }
        if (! $request->has('tien_do')) {
            $mergeData['tien_do'] = $monHoc->tien_do ?? 0;
        }
        if (! $request->has('diem_so')) {
            $mergeData['diem_so'] = $monHoc->diem_so;
        }
        if (! empty($mergeData)) {
            $request->merge($mergeData);
        }

        $rules = [
            'ma_mon' => 'required|string|max:50',
            'ten_mon' => 'required|string|max:255',
            'giang_vien' => 'nullable|string|max:255',
            'phong_hoc' => 'nullable|string|max:255',
            'so_tin_chi' => 'nullable|integer|min:1|max:20',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'ngay_bat_dau' => 'nullable|date',
            'mau_sac' => 'nullable|string|max:20',
            'trang_thai' => 'required|in:dang_hoc,da_hoan_thanh,tam_dung',
        ];

        // Xác định ngày bắt đầu hiệu lực (effective start date)
        $effectiveStartDate = null;
        if ($hasStart) {
            $effectiveStartDate = $normalizedStart;
        } elseif ($monHoc->ngay_bat_dau) {
            $effectiveStartDate = $monHoc->ngay_bat_dau->format('Y-m-d');
        }

        $effectiveEndDate = null;
        if ($hasEnd) {
            $effectiveEndDate = $normalizedEnd;
        } elseif ($monHoc->ngay_ket_thuc) {
            $effectiveEndDate = $monHoc->ngay_ket_thuc->format('Y-m-d');
        }

        if ($effectiveStartDate && $effectiveEndDate) {
            $rules['ngay_ket_thuc'] = 'nullable|date|after_or_equal:'.$effectiveStartDate;
        } else {
            $rules['ngay_ket_thuc'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'ngay_ket_thuc.after_or_equal' => 'The ngày kết thúc field must be a date after or equal to ngày bắt đầu.',
        ], [
            'ngay_bat_dau' => 'ngày bắt đầu',
            'ngay_ket_thuc' => 'ngày kết thúc',
        ]);

        if ($hasStart) {
            $validated['ngay_bat_dau'] = $normalizedStart;
        }
        if ($hasEnd) {
            $validated['ngay_ket_thuc'] = $normalizedEnd;
        }

        $monHoc->update($validated);

        $dongBoCalendar = $request->has('dong_bo_calendar') ? $request->boolean('dong_bo_calendar') : true;
        $daysOfWeek = $request->input('ngay_trong_tuan');
        $gioBatDau = $request->input('gio_bat_dau');
        $gioKetThuc = $request->input('gio_ket_thuc');
        $gioBatDauTheoThu = $request->input('gio_bat_dau_theo_thu');
        $gioKetThucTheoThu = $request->input('gio_ket_thuc_theo_thu');

        $monHoc->syncToCalendar($daysOfWeek, $gioBatDau, $gioKetThuc, $dongBoCalendar, $gioBatDauTheoThu, $gioKetThucTheoThu);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật môn học thành công'.($dongBoCalendar ? ' và đã đồng bộ vào Lịch trình!' : '!'),
                'mon_hoc' => $monHoc->load('lichHocs'),
            ]);
        }

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Cập nhật môn học thành công'.($dongBoCalendar ? ' và đã đồng bộ vào Lịch trình!' : '!'));
    }

    /**
     * Xóa mềm môn học (Soft Delete)
     */
    public function destroy(Request $request, MonHoc $monHoc)
    {
        // Kiểm tra quyền sở hữu môn học
        abort_if($monHoc->user_id !== Auth::id(), 403);

        $monHoc->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa môn học thành công!',
            ]);
        }

        return redirect()->route('mon-hoc.index')
            ->with('status', 'Đã xóa môn học thành công!');
    }

    /**
     * Đăng ký trở thành gia sư
     */
    public function dangKyGiaSu(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'chuyen_mon' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'hoc_phi_theo_gio' => 'nullable|numeric|min:0',
            'mo_ta_kinh_nghiem' => 'nullable|string',
            'mon_hoc_id' => 'nullable|exists:mon_hoc,id',
        ]);

        $validated['hoc_phi_theo_gio'] = (int) ($request->input('hoc_phi_theo_gio') ?: 150000);
        $validated['danh_gia'] = 5.0;
        $validated['so_danh_gia'] = 1;
        $validated['trang_thai'] = 'san_sang';

        $giaSu = GiaSu::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký hồ sơ Gia Sư thành công! Thông tin của bạn đã được lưu vào hệ thống.',
                'data' => $giaSu,
            ]);
        }

        return redirect()->route('mon-hoc.index')->with('status', 'Đăng ký hồ sơ Gia Sư thành công!');
    }

    /**
     * Báo cáo học tập & Thống kê điểm GPA
     */
    public function baoCaoHocTap()
    {
        $userId = Auth::id();
        $monHocs = MonHoc::where('user_id', $userId)->with('baiTaps')->get();

        // Seed sample subjects if user currently has none in DB
        if ($monHocs->isEmpty()) {
            $sampleSubjects = [
                ['ma_mon' => 'INT3306', 'ten_mon' => 'Lập trình Web & Laravel 13', 'giang_vien' => 'TS. Nguyễn Văn A', 'phong_hoc' => 'Phòng B2.04', 'so_tin_chi' => 3, 'tien_do' => 85, 'diem_so' => 9.0, 'mau_sac' => '#6366f1', 'trang_thai' => 'dang_hoc'],
                ['ma_mon' => 'INT2204', 'ten_mon' => 'Cơ sở dữ liệu nâng cao', 'giang_vien' => 'ThS. Trần Thị B', 'phong_hoc' => 'Phòng A1.02', 'so_tin_chi' => 4, 'tien_do' => 100, 'diem_so' => 8.5, 'mau_sac' => '#3b82f6', 'trang_thai' => 'da_hoan_thanh'],
                ['ma_mon' => 'INT3110', 'ten_mon' => 'Kiến trúc Phần mềm', 'giang_vien' => 'PGS.TS. Lê Văn C', 'phong_hoc' => 'Phòng C3.01', 'so_tin_chi' => 3, 'tien_do' => 60, 'diem_so' => 8.0, 'mau_sac' => '#10b981', 'trang_thai' => 'dang_hoc'],
                ['ma_mon' => 'ENG201', 'ten_mon' => 'Tiếng Anh Chuyên ngành IT', 'giang_vien' => 'Ms. Emily Smith', 'phong_hoc' => 'Phòng D1.05', 'so_tin_chi' => 2, 'tien_do' => 100, 'diem_so' => 9.5, 'mau_sac' => '#f59e0b', 'trang_thai' => 'da_hoan_thanh'],
                ['ma_mon' => 'INT3401', 'ten_mon' => 'An toàn & Bảo mật Thông tin', 'giang_vien' => 'TS. Phạm Hoàng D', 'phong_hoc' => 'Phòng B2.01', 'so_tin_chi' => 3, 'tien_do' => 40, 'diem_so' => 7.8, 'mau_sac' => '#ec4899', 'trang_thai' => 'dang_hoc'],
            ];

            foreach ($sampleSubjects as $subData) {
                $subData['user_id'] = $userId;
                $m = MonHoc::create($subData);
                $m->baiTaps()->createMany([
                    ['tieu_de' => 'Bài tập lớn '.$m->ten_mon, 'mo_ta' => 'Xây dựng module hệ thống', 'han_nop' => now()->addDays(5), 'muc_do_uu_tien' => 'cao', 'trang_thai' => 'dang_thuc_hien'],
                    ['tieu_de' => 'Bài tập cá nhân 1', 'mo_ta' => 'Trả lời câu hỏi lý thuyết', 'han_nop' => now()->subDays(2), 'muc_do_uu_tien' => 'trung_binh', 'trang_thai' => 'da_hoan_thanh'],
                ]);
            }

            $monHocs = MonHoc::where('user_id', $userId)->with('baiTaps')->get();
        }

        $tongSoMon = $monHocs->count();
        $soMonDangHoc = $monHocs->where('trang_thai', 'dang_hoc')->count();
        $soMonHoanThanh = $monHocs->where('trang_thai', 'da_hoan_thanh')->count();
        $tongTinChi = $monHocs->sum('so_tin_chi');
        $tinChiHoanThanh = $monHocs->where('trang_thai', 'da_hoan_thanh')->sum('so_tin_chi');

        // GPA 10.0 & 4.0 scale calculation
        $validGradeSubjects = $monHocs->whereNotNull('diem_so')->where('diem_so', '>', 0);
        $totalWeightedPoints10 = 0;
        $totalCreditsWithGrade = 0;
        $totalWeightedPoints4 = 0;

        foreach ($validGradeSubjects as $m) {
            $score10 = (float) $m->diem_so;
            $credits = (int) $m->so_tin_chi;

            $totalWeightedPoints10 += ($score10 * $credits);
            $totalCreditsWithGrade += $credits;

            $score4 = match (true) {
                $score10 >= 8.5 => 4.0,
                $score10 >= 7.0 => 3.0,
                $score10 >= 5.5 => 2.0,
                $score10 >= 4.0 => 1.0,
                default => 0.0,
            };
            $totalWeightedPoints4 += ($score4 * $credits);
        }

        $gpa10 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints10 / $totalCreditsWithGrade, 2) : 0;
        $gpa4 = $totalCreditsWithGrade > 0 ? round($totalWeightedPoints4 / $totalCreditsWithGrade, 2) : 0;

        // Xếp loại học lực
        $xepLoai = match (true) {
            $gpa4 >= 3.6 => ['label' => 'Xuất sắc', 'color' => 'success', 'icon' => 'bi-trophy-fill'],
            $gpa4 >= 3.2 => ['label' => 'Giỏi', 'color' => 'primary', 'icon' => 'bi-award-fill'],
            $gpa4 >= 2.5 => ['label' => 'Khá', 'color' => 'info', 'icon' => 'bi-star-fill'],
            $gpa4 >= 2.0 => ['label' => 'Trung bình', 'color' => 'warning', 'icon' => 'bi-hand-thumbs-up-fill'],
            default => ['label' => 'Yếu / Kém', 'color' => 'danger', 'icon' => 'bi-exclamation-triangle-fill'],
        };

        // Assignment analytics
        $allAssignments = $monHocs->pluck('baiTaps')->flatten();
        $tongBaiTap = $allAssignments->count();
        $baiTapHoanThanh = $allAssignments->where('trang_thai', 'da_hoan_thanh')->count();
        $baiTapChuaNop = $allAssignments->where('trang_thai', '!=', 'da_hoan_thanh')->count();
        $tyLeHoanThanhBaiTap = $tongBaiTap > 0 ? round(($baiTapHoanThanh / $tongBaiTap) * 100) : 0;

        return view('mon_hoc.bao_cao', compact(
            'monHocs',
            'tongSoMon',
            'soMonDangHoc',
            'soMonHoanThanh',
            'tongTinChi',
            'tinChiHoanThanh',
            'gpa10',
            'gpa4',
            'xepLoai',
            'tongBaiTap',
            'baiTapHoanThanh',
            'baiTapChuaNop',
            'tyLeHoanThanhBaiTap'
        ));
    }

    /**
     * Tìm kiếm và xem thông tin gia sư phục vụ sinh viên
     */
    public function danhSachGiaSu(Request $request)
    {
        $keyword = trim($request->input('q', ''));
        $monHocId = $request->input('mon_hoc_id');
        $mucGia = $request->input('muc_gia');

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

        $giaSus = $query->orderByDesc('danh_gia')->get();
        $monHocs = MonHoc::where('user_id', Auth::id())->get();

        return view('mon_hoc.gia_su', compact('giaSus', 'monHocs', 'keyword', 'monHocId', 'mucGia'));
    }
}
