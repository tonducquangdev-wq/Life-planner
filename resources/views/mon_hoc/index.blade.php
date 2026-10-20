<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Học Tập - Life Planner</title>

    <!-- Kịch bản Khởi tạo Giao diện Sáng/Tối chống giật trang (Anti-Flicker) -->
    <script>
        (function() {
            var userTheme = "{{ Auth::check() ? (Auth::user()->giao_dien ?? 'system') : 'system' }}";
            var savedTheme = localStorage.getItem('theme');
            var themeToApply = 'light';
            if (savedTheme && (savedTheme === 'dark' || savedTheme === 'light')) {
                themeToApply = savedTheme;
            } else if (userTheme && (userTheme === 'dark' || userTheme === 'light')) {
                themeToApply = userTheme;
            } else {
                themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
            if (themeToApply === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        })();
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Dashboard CSS for floating menu and global components -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : '1.0' }}">
    <!-- Custom Study Management CSS -->
    <link rel="preload" href="{{ asset('css/mon-hoc.css') }}?v={{ file_exists(public_path('css/mon-hoc.css')) ? filemtime(public_path('css/mon-hoc.css')) : '1.2' }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/mon-hoc.css') }}?v={{ file_exists(public_path('css/mon-hoc.css')) ? filemtime(public_path('css/mon-hoc.css')) : '1.2' }}">
    @include('layouts.pwa-head')
</head>

<body>
    <div class="study-page-wrapper">

        <!-- =========================================================================
             1. TOPBAR NAVBAR (LIFE PLANNER LOGO & USER ACTIONS)
             ========================================================================= -->
        <header class="study-topbar">
            <div class="study-navbar-inner">
                <!-- Brand Logo (LP LIFE PLANNER) -->
                <a href="{{ route('calendar.index') }}" class="study-brand-logo" title="Quay lại Lịch">
                    <div class="study-logo-square">
                        <span>LP</span>
                    </div>
                    <span class="study-brand-title">LIFE PLANNER</span>
                </a>

                <!-- Top Right Actions -->
                <div class="study-topbar-actions">
                    <!-- Gia Sư Verified Pill -->
                    <button type="button" class="study-badge-verified" data-bs-toggle="modal" data-bs-target="#modalDangKyGiaSu" title="Hồ sơ giảng viên & gia sư xác thực">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span class="d-none d-sm-inline">Gia Sư Verified</span>
                    </button>

                    <!-- Tin Nhắn Button -->
                    <button type="button" class="study-btn-chat" data-bs-toggle="modal" data-bs-target="#modalWorldChatQuick" title="Hộp thư & Trò chuyện cộng đồng">
                        <i class="bi bi-chat-dots-fill text-muted"></i>
                        <span class="d-none d-sm-inline">Tin Nhắn</span>
                    </button>

                    <!-- Dark Mode Toggle Button -->
                    <button type="button" class="study-theme-toggle" id="btnThemeToggle" title="Chuyển chế độ Sáng / Tối" onclick="toggleThemeMode()">
                        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                    </button>

                    <!-- User Profile Avatar Pill -->
                    <div class="dropdown">
                        <button class="study-user-pill dropdown-toggle border-0" type="button" id="userDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="study-avatar-circle">
                                <span>{{ Auth::check() ? strtoupper(substr(Auth::user()->ho_ten ?: (Auth::user()->name ?: 'T'), 0, 1)) : 'T' }}</span>
                            </div>
                            <span class="d-none d-sm-inline">{{ Auth::check() ? (Auth::user()->ho_ten ?: (Auth::user()->name ?: 'TQuang')) : 'TQuang' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2" aria-labelledby="userDropdownBtn">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small text-truncate">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                                <small class="text-muted text-truncate d-block">{{ Auth::user()->email ?? '' }}</small>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('calendar.index') }}">
                                    <i class="bi bi-calendar3 text-primary"></i><span>Trang Lịch chính</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('study.tutors') }}">
                                    <i class="bi bi-people-fill text-info"></i><span>Danh sách Gia Sư</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bao-cao-hoc-tap.index') }}">
                                    <i class="bi bi-graph-up-arrow text-success"></i><span>Báo cáo điểm GPA</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                        <i class="bi bi-box-arrow-right"></i><span>Đăng xuất</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- =========================================================================
             2. MAIN CONTENT BODY
             ========================================================================= -->
        <main class="study-container">

            <!-- Flash Alert Notification if any -->
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2.5 py-3 px-4" role="alert" style="background: var(--study-verified-bg); color: var(--study-verified-text);">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- PAGE HEADER: TITLE & 4 ACTION BUTTONS -->
            <div class="study-header-section">
                <!-- Left Title & Subtitle -->
                <div class="study-header-title-box">
                    <span class="study-header-icon">🎓</span>
                    <div>
                        <h1 class="study-page-title">Quản Lý Học Tập</h1>
                        <p class="study-page-subtitle">
                            Theo dõi lịch học, phòng học, giảng viên và hạn nộp bài tập tự động đồng bộ Lịch
                        </p>
                    </div>
                </div>

                <!-- Right Action Buttons: Desktop hiển thị hàng ngang 4 nút, Mobile thu gọn vào 1 nút bấm xổ ra -->
                <div class="study-actions-wrapper">
                    <!-- Nút bấm duy nhất trên Mobile (Chạm vào để xổ ra 4 nút) -->
                    <button type="button" 
                            class="study-action-btn study-btn-primary study-mobile-actions-toggle d-inline-flex d-lg-none" 
                            id="btnToggleStudyActions" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#studyActionsCollapse" 
                            aria-expanded="false" 
                            aria-controls="studyActionsCollapse"
                            title="Mở danh sách tùy chọn học tập">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-grid-fill text-warning"></i>
                            <span>Thao Tác Nhanh</span>
                            <span class="badge bg-white text-primary rounded-pill px-2 py-0.5 ms-1" style="font-size: 0.72rem;">4 Chức Năng</span>
                        </span>
                        <i class="bi bi-chevron-down study-chevron-icon ms-auto"></i>
                    </button>

                    <!-- Khối chứa 4 nút hành động: Desktop luôn hiển thị, Mobile xổ ra theo dạng collapse -->
                    <div class="study-actions-collapse collapse d-lg-flex" id="studyActionsCollapse">
                        <div class="study-actions-row">
                            <!-- Button 1: Đăng Ký Gia Sư -->
                            <button type="button" class="study-action-btn study-btn-outline" data-bs-toggle="modal" data-bs-target="#modalDangKyGiaSu">
                                <span>🎓</span>
                                <span>Đăng Ký Gia Sư</span>
                            </button>

                            <!-- Button 2: Tìm Gia Sư -->
                            <a href="{{ route('study.tutors') }}" class="study-action-btn study-btn-outline">
                                <span>👨‍🏫</span>
                                <span>Tìm Gia Sư</span>
                            </a>

                            <!-- Button 3: Thêm Bài Tập -->
                            <button type="button" class="study-action-btn study-btn-purple" data-bs-toggle="modal" data-bs-target="#modalThemBaiTap">
                                <i class="bi bi-plus-lg"></i>
                                <span>Thêm Bài Tập</span>
                            </button>

                            <!-- Button 4: Thêm Môn Học -->
                            <button type="button" class="study-action-btn study-btn-primary" data-bs-toggle="modal" data-bs-target="#createMonHocModal">
                                <i class="bi bi-plus-lg"></i>
                                <span>Thêm Môn Học</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =====================================================================
                 3. CARD 1: DANH SÁCH MÔN HỌC & LỊCH TRÌNH
                 ===================================================================== -->
            <section class="study-card nm-course-card">
                <div class="study-card-header">
                    <div class="study-card-title-group">
                        <span class="study-card-icon">📑</span>
                        <h2 class="study-card-title">Danh sách Môn Học & Lịch Trình</h2>
                    </div>
                    <div class="study-card-count">
                        Tổng Số: <strong id="totalSubjectCountText">{{ $monHocs->count() }} Môn Học</strong>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="study-table-responsive">
                    <table class="study-table" id="tableMonHoc">
                        <thead>
                            <tr>
                                <th>TÊN MÔN HỌC</th>
                                <th>GIẢNG VIÊN</th>
                                <th>PHÒNG HỌC</th>
                                <th>GIỜ & NGÀY HỌC</th>
                                <th>THỜI GIAN MÔN</th>
                                <th>TRẠNG THÁI</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyMonHoc">
                            @forelse($monHocs as $monHoc)
                            @php
                                $firstLich = $monHoc->lichHocs->first();
                                $dayMap = [1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 7 => 'CN'];
                                
                                if ($monHoc->lichHocs->isNotEmpty()) {
                                    $distinctTimes = $monHoc->lichHocs->map(fn($lh) => substr($lh->gio_bat_dau, 0, 5) . '–' . substr($lh->gio_ket_thuc, 0, 5))->unique();
                                    if ($distinctTimes->count() === 1) {
                                        $gioHoc = substr($firstLich->gio_bat_dau, 0, 5) . ' – ' . substr($firstLich->gio_ket_thuc, 0, 5);
                                        $ngayHoc = $monHoc->lichHocs->pluck('ngay_trong_tuan')->map(fn($d) => $dayMap[$d] ?? 'T'.$d)->unique()->join(', ');
                                    } else {
                                        $gioHoc = $monHoc->lichHocs->map(fn($lh) => ($dayMap[$lh->ngay_trong_tuan] ?? 'T'.$lh->ngay_trong_tuan) . ': ' . substr($lh->gio_bat_dau, 0, 5) . '–' . substr($lh->gio_ket_thuc, 0, 5))->join(' • ');
                                        $ngayHoc = $monHoc->lichHocs->count() . ' buổi/tuần';
                                    }
                                } else {
                                    $gioHoc = '07:00 – 11:00';
                                    $ngayHoc = $monHoc->ngay_bat_dau ? ($dayMap[$monHoc->ngay_bat_dau->dayOfWeekIso] ?? 'T2') : 'T2';
                                }

                                $gvDisplay = $monHoc->giang_vien ? (Str::startsWith($monHoc->giang_vien, ['GV.', 'TS.', 'ThS.', 'PGS.', 'GS.']) ? $monHoc->giang_vien : 'GV. ' . $monHoc->giang_vien) : 'GV. Chưa cập nhật';
                                $startStr = $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('d/m/Y') : '15/10/2026';
                                $endStr = $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('d/m/Y') : '06/11/2026';
                                
                                $statusLabel = match($monHoc->trang_thai) {
                                    'da_hoan_thanh' => 'Đã hoàn thành',
                                    'tam_dung' => 'Tạm dừng',
                                    default => 'Đang học',
                                };

                                $monHocPayload = [
                                    'id' => $monHoc->id,
                                    'ten_mon' => $monHoc->ten_mon,
                                    'ma_mon' => $monHoc->ma_mon ?? '',
                                    'so_tin_chi' => $monHoc->so_tin_chi ?? 3,
                                    'giang_vien' => $monHoc->giang_vien ?? '',
                                    'phong_hoc' => $monHoc->phong_hoc ?? '',
                                    'ngay_bat_dau' => $monHoc->ngay_bat_dau ? $monHoc->ngay_bat_dau->format('Y-m-d') : '',
                                    'ngay_ket_thuc' => $monHoc->ngay_ket_thuc ? $monHoc->ngay_ket_thuc->format('Y-m-d') : '',
                                    'trang_thai' => $monHoc->trang_thai ?: 'dang_hoc',
                                    'mau_sac' => $monHoc->mau_sac ?: '#3b82f6',
                                    'ngay_trong_tuan' => $monHoc->lichHocs->pluck('ngay_trong_tuan')->values()->all(),
                                    'gio_bat_dau' => $firstLich ? substr($firstLich->gio_bat_dau, 0, 5) : '07:00',
                                    'gio_ket_thuc' => $firstLich ? substr($firstLich->gio_ket_thuc, 0, 5) : '10:00',
                                    'dong_bo_calendar' => $monHoc->dong_bo_calendar ?? 1,
                                    'lich_hocs' => $monHoc->lichHocs->map(fn($lh) => [
                                        'ngay_trong_tuan' => (int) $lh->ngay_trong_tuan,
                                        'gio_bat_dau' => substr($lh->gio_bat_dau, 0, 5),
                                        'gio_ket_thuc' => substr($lh->gio_ket_thuc, 0, 5),
                                    ])->values()->all(),
                                ];
                            @endphp
                            <tr id="rowMonHoc{{ $monHoc->id }}" data-id="{{ $monHoc->id }}" class="nm-course-card">
                                <!-- Tên Môn Học -->
                                <td data-label="Tên môn học">
                                    <div class="study-course-cell">
                                        <span class="study-course-dot" style="background-color: {{ $monHoc->mau_sac ?: '#3b82f6' }};"></span>
                                        <div class="study-course-info">
                                            <a href="{{ route('mon-hoc.show', $monHoc) }}" class="study-course-name">
                                                {{ $monHoc->ten_mon }}
                                            </a>
                                            <span class="study-course-credits">{{ $monHoc->so_tin_chi ?? 3 }} tín chỉ</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Giảng Viên -->
                                <td data-label="Giảng viên">
                                    <div class="study-teacher-cell">
                                        <i class="bi bi-person-vcard"></i>
                                        <span>{{ $gvDisplay }}</span>
                                    </div>
                                </td>

                                <!-- Phòng Học -->
                                <td data-label="Phòng học">
                                    <span class="badge-room-pill">
                                        <i class="bi bi-geo-alt-fill text-primary"></i>
                                        <span>{{ $monHoc->phong_hoc ?: 'A204' }}</span>
                                    </span>
                                </td>

                                <!-- Giờ & Ngày Học -->
                                <td data-label="Lịch học">
                                    <div class="study-schedule-cell">
                                        <span class="badge-time-pill">
                                            <i class="bi bi-clock-fill text-primary"></i>
                                            <span>{{ $gioHoc }}</span>
                                        </span>
                                        <div class="study-dow-tag">
                                            <i class="bi bi-calendar3"></i>
                                            <span>{{ $ngayHoc }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Thời Gian Môn -->
                                <td data-label="Thời gian">
                                    <span class="badge-date-range">
                                        {{ $startStr }} &rarr; {{ $endStr }}
                                    </span>
                                </td>

                                <!-- Trạng Thái -->
                                <td data-label="Trạng thái">
                                    <span class="badge-status-pill status-{{ $monHoc->trang_thai ?: 'dang_hoc' }}">
                                        <span class="status-dot"></span>
                                        <span>{{ $statusLabel }}</span>
                                    </span>
                                </td>

                                <!-- Thao Tác -->
                                <td data-label="Thao tác">
                                    <div class="study-table-actions d-flex align-items-center gap-2">
                                        <button type="button" class="study-btn-edit" onclick='openEditMonHocModal(@json($monHocPayload))' title="Sửa môn học">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Sửa</span>
                                        </button>
                                        <button type="button" class="study-btn-delete" onclick="confirmDeleteMonHoc({{ $monHoc->id }}, '{{ addslashes($monHoc->ten_mon) }}')" title="Xóa môn học">
                                            <i class="bi bi-trash3"></i>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRowMonHoc">
                                <td colspan="7">
                                    <div class="study-empty-state">
                                        <div class="study-empty-icon"><i class="bi bi-journal-plus"></i></div>
                                        <h5>Chưa có môn học nào</h5>
                                        <p class="small text-muted mb-3">Bắt đầu bằng việc thêm môn học đầu tiên để tự động lập lịch trình học tập.</p>
                                        <button type="button" class="study-action-btn study-btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#createMonHocModal">
                                            <i class="bi bi-plus-lg"></i> Thêm môn học ngay
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- =====================================================================
                 4. CARD 2: BÀI TẬP & DEADLINE MÔN HỌC
                 ===================================================================== -->
            <section class="study-card">
                <div class="study-card-header">
                    <div>
                        <div class="study-card-title-group">
                            <span class="study-card-icon">📑</span>
                            <h2 class="study-card-title">Bài Tập & Deadline Môn Học</h2>
                        </div>
                        <p class="study-card-subtitle">
                            <span class="text-warning">⚡</span>
                            <span>Tự động đồng bộ và lưu vào trang Lịch dưới mục Deadline</span>
                        </p>
                    </div>

                    <button type="button" class="study-action-btn study-btn-purple mt-2 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modalThemBaiTap">
                        <i class="bi bi-plus-lg"></i>
                        <span>Thêm Bài Tập</span>
                    </button>
                </div>

                <!-- Assignment Cards List -->
                <div class="study-deadline-list" id="deadlineListContainer">
                    @forelse($baiTaps as $bt)
                    @php
                        $isCompleted = ($bt->trang_thai === 'da_hoan_thanh');
                        $priorityText = match($bt->muc_do_uu_tien) {
                            'cao' => 'Ưu Tiên: Cao',
                            'trung_binh' => 'Ưu Tiên: Trung bình',
                            default => 'Ưu Tiên: Thấp',
                        };
                        $dueFormatted = \Carbon\Carbon::parse($bt->han_nop)->format('H:i \n\g\à\y d/m/Y');
                        $baiTapPayload = [
                            'id' => $bt->id,
                            'mon_hoc_id' => $bt->mon_hoc_id,
                            'tieu_de' => $bt->tieu_de,
                            'han_nop' => $bt->han_nop ? \Carbon\Carbon::parse($bt->han_nop)->format('Y-m-d\TH:i') : '',
                            'muc_do_uu_tien' => $bt->muc_do_uu_tien ?? 'cao',
                            'trang_thai' => $bt->trang_thai ?? 'chua_hoan_thanh',
                            'mo_ta' => $bt->mo_ta ?? '',
                        ];
                    @endphp
                    <div class="study-deadline-item {{ $isCompleted ? 'completed-item' : '' }}" id="assignmentItem{{ $bt->id }}">
                        <!-- Top Row: Checkbox, Title & Badges -->
                        <div class="study-deadline-top-row">
                            <div class="study-deadline-title-wrap">
                                <input type="checkbox" 
                                       class="study-checkbox" 
                                       id="checkBt{{ $bt->id }}" 
                                       {{ $isCompleted ? 'checked' : '' }} 
                                       onchange="toggleAssignmentStatus({{ $bt->id }}, this)">
                                
                                <label for="checkBt{{ $bt->id }}" class="study-deadline-title {{ $isCompleted ? 'is-done' : '' }}" id="titleBt{{ $bt->id }}">
                                    {{ $bt->tieu_de }}
                                </label>
                            </div>

                            <div class="study-deadline-badges">
                                <span class="badge-deadline-sync">
                                    <i class="bi bi-calendar-event me-1"></i> Lịch: Deadline
                                </span>
                                <span class="badge-priority-tag priority-{{ $bt->muc_do_uu_tien }}">
                                    {{ $priorityText }}
                                </span>
                                <span class="study-rocket-badge" title="Tự động đồng bộ Lịch">🚀</span>
                            </div>
                        </div>

                        <!-- Bottom Metadata Row -->
                        <div class="study-deadline-meta-row">
                            <span class="study-meta-item">
                                <i class="bi bi-mortarboard-fill text-primary"></i>
                                <span>Môn: <strong>{{ $bt->monHoc?->ten_mon ?? 'Chưa phân môn' }}</strong></span>
                            </span>

                            <span class="study-meta-item">
                                <i class="bi bi-alarm-fill text-danger"></i>
                                <span>Hạn nộp: <strong>{{ $dueFormatted }}</strong></span>
                            </span>

                            @if(!empty($bt->mo_ta))
                            <span class="study-meta-item text-truncate d-none d-md-inline" style="max-width: 280px;" title="{{ $bt->mo_ta }}">
                                <i class="bi bi-info-circle text-muted"></i>
                                <span>{{ $bt->mo_ta }}</span>
                            </span>
                            @endif

                            <div class="study-deadline-actions d-flex align-items-center gap-1.5 ms-auto">
                                <button type="button" class="study-btn-edit-assignment" onclick='openEditBaiTapModal(@json($baiTapPayload))' title="Sửa bài tập / deadline">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Sửa</span>
                                </button>
                                <button type="button" class="study-btn-delete-assignment" onclick="confirmDeleteBaiTap({{ $bt->id }}, '{{ addslashes($bt->tieu_de) }}')" title="Xóa bài tập">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="study-empty-state" id="emptyDeadlineState">
                        <div class="study-empty-icon"><i class="bi bi-check2-circle"></i></div>
                        <h5>Chưa có bài tập hoặc deadline nào</h5>
                        <p class="small text-muted mb-3">Tất cả bài tập bạn thêm sẽ tự động xuất hiện trên Lịch trình ở chế độ nhắc việc.</p>
                        <button type="button" class="study-action-btn study-btn-purple mx-auto" data-bs-toggle="modal" data-bs-target="#modalThemBaiTap">
                            <i class="bi bi-plus-lg"></i> Thêm bài tập mới
                        </button>
                    </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>

    <!-- =========================================================================
         5. MODALS (THÊM MÔN HỌC, THÊM BÀI TẬP, ĐĂNG KÝ GIA SƯ, CONFIRMATIONS)
         ========================================================================= -->

    <!-- MODAL 1: THÊM MÔN HỌC MỚI -->
    <div class="modal fade study-modal" id="createMonHocModal" tabindex="-1" aria-labelledby="modalThemMonHocLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 580px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalThemMonHocLabel">
                        <i class="bi bi-journal-plus text-primary"></i>
                        <span>Thêm Môn Học Mới</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createMonHocForm" action="{{ route('mon-hoc.store') }}" method="POST">
                    @csrf
                    <div class="study-modal-body">
                        <div class="row g-3">
                            <!-- Tên môn học -->
                            <div class="col-12">
                                <label for="input_ten_mon" class="study-form-label">Tên môn học <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="input_ten_mon" name="ten_mon" placeholder="VD: Lập trình Web máy chủ, Chuyên đề PHP..." required>
                            </div>

                            <!-- Mã môn học & Số tín chỉ -->
                            <div class="col-sm-6">
                                <label for="input_ma_mon" class="study-form-label">Mã môn học <small class="text-muted fw-normal">(Tùy chọn)</small></label>
                                <input type="text" class="study-form-control text-uppercase" id="input_ma_mon" name="ma_mon" placeholder="VD: WEB301">
                            </div>
                            <div class="col-sm-6">
                                <label for="input_so_tin_chi" class="study-form-label">Số tín chỉ</label>
                                <input type="number" class="study-form-control" id="input_so_tin_chi" name="so_tin_chi" value="3" min="1" max="15">
                            </div>

                            <!-- Giảng viên & Phòng học -->
                            <div class="col-sm-6">
                                <label for="input_giang_vien" class="study-form-label">Giảng viên phụ trách</label>
                                <input type="text" class="study-form-control" id="input_giang_vien" name="giang_vien" placeholder="VD: GV. Trương Châu Long">
                            </div>
                            <div class="col-sm-6">
                                <label for="input_phong_hoc" class="study-form-label">Phòng học</label>
                                <input type="text" class="study-form-control" id="input_phong_hoc" name="phong_hoc" placeholder="VD: A204, B102, Zoom Online...">
                            </div>

                            <!-- Ngày bắt đầu & Ngày kết thúc -->
                            <div class="col-sm-6">
                                <label for="input_ngay_bat_dau" class="study-form-label">Ngày bắt đầu</label>
                                <input type="date" class="study-form-control" id="input_ngay_bat_dau" name="ngay_bat_dau" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-sm-6">
                                <label for="input_ngay_ket_thuc" class="study-form-label">Ngày kết thúc</label>
                                <input type="date" class="study-form-control" id="input_ngay_ket_thuc" name="ngay_ket_thuc" value="{{ date('Y-m-d', strtotime('+3 weeks')) }}">
                            </div>

                            <!-- Khung giờ & Lịch học hàng tuần -->
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background-color: var(--study-surface-subtle); border: 1px solid var(--study-border);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="study-form-label mb-0"><i class="bi bi-calendar-week text-primary me-1"></i> Lịch học trong tuần</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="check_dong_bo_calendar" name="dong_bo_calendar" value="1" checked>
                                            <label class="form-check-label small fw-bold text-primary" for="check_dong_bo_calendar">Đồng bộ vào Lịch</label>
                                        </div>
                                    </div>
                                    <div class="study-dow-container mb-3" id="create_dow_group">
                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_2" name="ngay_trong_tuan[]" value="1">
                                        <label class="study-dow-label" for="dow_2">Thứ 2</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_3" name="ngay_trong_tuan[]" value="2">
                                        <label class="study-dow-label" for="dow_3">Thứ 3</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_4" name="ngay_trong_tuan[]" value="3">
                                        <label class="study-dow-label" for="dow_4">Thứ 4</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_5" name="ngay_trong_tuan[]" value="4">
                                        <label class="study-dow-label" for="dow_5">Thứ 5</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_6" name="ngay_trong_tuan[]" value="5" checked>
                                        <label class="study-dow-label" for="dow_6">Thứ 6</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_7" name="ngay_trong_tuan[]" value="6">
                                        <label class="study-dow-label" for="dow_7">Thứ 7</label>

                                        <input type="checkbox" class="study-dow-check create-dow-check" id="dow_cn" name="ngay_trong_tuan[]" value="7">
                                        <label class="study-dow-label" for="dow_cn">CN</label>
                                    </div>

                                    <!-- Khung giờ chung (áp dụng khi chọn 1 buổi hoặc làm mẫu đồng bộ) -->
                                    <div class="row g-2 mb-2" id="create_common_time_row">
                                        <div class="col-6">
                                            <label class="small text-muted mb-1" id="create_lbl_gio_bat_dau">Giờ bắt đầu</label>
                                            <input type="time" class="study-form-control form-control-sm" id="create_gio_bat_dau" name="gio_bat_dau" value="07:00">
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1" id="create_lbl_gio_ket_thuc">Giờ kết thúc</label>
                                            <input type="time" class="study-form-control form-control-sm" id="create_gio_ket_thuc" name="gio_ket_thuc" value="10:00">
                                        </div>
                                    </div>

                                    <!-- Khung giờ riêng cho từng buổi (tự động kích hoạt khi chọn >= 2 buổi) -->
                                    <div id="create_per_day_container" class="d-none mt-2.5 pt-2.5 border-top" style="border-color: var(--study-border) !important;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="small fw-bold text-primary">
                                                <i class="bi bi-clock-history me-1"></i> Khung giờ riêng từng buổi học:
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" id="btn_apply_common_create" style="font-size: 0.76rem;">
                                                <i class="bi bi-arrow-repeat me-1"></i>Đồng bộ giờ chung
                                            </button>
                                        </div>
                                        <div id="create_per_day_list" class="d-flex flex-column gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trạng thái & Màu sắc -->
                            <div class="col-sm-6">
                                <label for="select_trang_thai" class="study-form-label">Trạng thái</label>
                                <select class="study-form-select" id="select_trang_thai" name="trang_thai">
                                    <option value="dang_hoc" selected>Đang Học</option>
                                    <option value="da_hoan_thanh">Đã Hoàn Thành</option>
                                    <option value="tam_dung">Tạm Dừng</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="study-form-label">Màu nhận diện</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border-0 p-0 rounded-circle flex-shrink-0" name="mau_sac" value="#3b82f6" style="width: 38px; height: 38px;">
                                    <small class="text-muted">Màu hiển thị trên Lịch</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="study-modal-footer">
                        <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="study-action-btn study-btn-primary" id="btnSubmitMonHoc">
                            <i class="bi bi-check-lg"></i>
                            <span>Lưu môn học</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: CHỈNH SỬA MÔN HỌC -->
    <div class="modal fade study-modal" id="editMonHocModal" tabindex="-1" aria-labelledby="modalEditMonHocLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 580px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalEditMonHocLabel">
                        <i class="bi bi-pencil-square text-primary"></i>
                        <span>Chỉnh Sửa Môn Học</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditMonHoc" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_mh_id" name="id">
                    <div class="study-modal-body">
                        <div class="row g-3">
                            <!-- Tên môn học -->
                            <div class="col-12">
                                <label for="edit_input_ten_mon" class="study-form-label">Tên môn học <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="edit_input_ten_mon" name="ten_mon" placeholder="VD: Lập trình Web máy chủ..." required>
                            </div>

                            <!-- Mã môn học & Số tín chỉ -->
                            <div class="col-sm-6">
                                <label for="edit_input_ma_mon" class="study-form-label">Mã môn học <small class="text-muted fw-normal">(Tùy chọn)</small></label>
                                <input type="text" class="study-form-control text-uppercase" id="edit_input_ma_mon" name="ma_mon" placeholder="VD: WEB301">
                            </div>
                            <div class="col-sm-6">
                                <label for="edit_input_so_tin_chi" class="study-form-label">Số tín chỉ</label>
                                <input type="number" class="study-form-control" id="edit_input_so_tin_chi" name="so_tin_chi" value="3" min="1" max="15">
                            </div>

                            <!-- Giảng viên & Phòng học -->
                            <div class="col-sm-6">
                                <label for="edit_input_giang_vien" class="study-form-label">Giảng viên phụ trách</label>
                                <input type="text" class="study-form-control" id="edit_input_giang_vien" name="giang_vien" placeholder="VD: GV. Trương Châu Long">
                            </div>
                            <div class="col-sm-6">
                                <label for="edit_input_phong_hoc" class="study-form-label">Phòng học</label>
                                <input type="text" class="study-form-control" id="edit_input_phong_hoc" name="phong_hoc" placeholder="VD: A204, B102, Zoom Online...">
                            </div>

                            <!-- Ngày bắt đầu & Ngày kết thúc -->
                            <div class="col-sm-6">
                                <label for="edit_input_ngay_bat_dau" class="study-form-label">Ngày bắt đầu</label>
                                <input type="date" class="study-form-control" id="edit_input_ngay_bat_dau" name="ngay_bat_dau">
                            </div>
                            <div class="col-sm-6">
                                <label for="edit_input_ngay_ket_thuc" class="study-form-label">Ngày kết thúc</label>
                                <input type="date" class="study-form-control" id="edit_input_ngay_ket_thuc" name="ngay_ket_thuc">
                            </div>

                            <!-- Khung giờ & Lịch học hàng tuần -->
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background-color: var(--study-surface-subtle); border: 1px solid var(--study-border);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="study-form-label mb-0"><i class="bi bi-calendar-week text-primary me-1"></i> Lịch học trong tuần</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="edit_check_dong_bo_calendar" name="dong_bo_calendar" value="1" checked>
                                            <label class="form-check-label small fw-bold text-primary" for="edit_check_dong_bo_calendar">Đồng bộ vào Lịch</label>
                                        </div>
                                    </div>
                                    <div class="study-dow-container mb-3" id="edit_dow_group">
                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_2" name="ngay_trong_tuan[]" value="1">
                                        <label class="study-dow-label" for="edit_dow_2">Thứ 2</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_3" name="ngay_trong_tuan[]" value="2">
                                        <label class="study-dow-label" for="edit_dow_3">Thứ 3</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_4" name="ngay_trong_tuan[]" value="3">
                                        <label class="study-dow-label" for="edit_dow_4">Thứ 4</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_5" name="ngay_trong_tuan[]" value="4">
                                        <label class="study-dow-label" for="edit_dow_5">Thứ 5</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_6" name="ngay_trong_tuan[]" value="5">
                                        <label class="study-dow-label" for="edit_dow_6">Thứ 6</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_7" name="ngay_trong_tuan[]" value="6">
                                        <label class="study-dow-label" for="edit_dow_7">Thứ 7</label>

                                        <input type="checkbox" class="study-dow-check edit-dow-check" id="edit_dow_cn" name="ngay_trong_tuan[]" value="7">
                                        <label class="study-dow-label" for="edit_dow_cn">CN</label>
                                    </div>

                                    <!-- Giờ học chung (áp dụng khi chọn 1 buổi hoặc làm mẫu đồng bộ) -->
                                    <div class="row g-2 mb-2" id="edit_common_time_row">
                                        <div class="col-6">
                                            <label class="small text-muted mb-1" id="edit_lbl_gio_bat_dau">Giờ bắt đầu</label>
                                            <input type="time" class="study-form-control form-control-sm" id="edit_input_gio_bat_dau" name="gio_bat_dau" value="07:00">
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1" id="edit_lbl_gio_ket_thuc">Giờ kết thúc</label>
                                            <input type="time" class="study-form-control form-control-sm" id="edit_input_gio_ket_thuc" name="gio_ket_thuc" value="10:00">
                                        </div>
                                    </div>

                                    <!-- Khung giờ riêng cho từng buổi (tự động kích hoạt khi chọn >= 2 buổi) -->
                                    <div id="edit_per_day_container" class="d-none mt-2.5 pt-2.5 border-top" style="border-color: var(--study-border) !important;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="small fw-bold text-primary">
                                                <i class="bi bi-clock-history me-1"></i> Khung giờ riêng từng buổi học:
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" id="btn_apply_common_edit" style="font-size: 0.76rem;">
                                                <i class="bi bi-arrow-repeat me-1"></i>Đồng bộ giờ chung
                                            </button>
                                        </div>
                                        <div id="edit_per_day_list" class="d-flex flex-column gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trạng thái & Màu sắc -->
                            <div class="col-sm-6">
                                <label for="edit_select_trang_thai" class="study-form-label">Trạng thái</label>
                                <select class="study-form-select" id="edit_select_trang_thai" name="trang_thai">
                                    <option value="dang_hoc">Đang Học</option>
                                    <option value="da_hoan_thanh">Đã Hoàn Thành</option>
                                    <option value="tam_dung">Tạm Dừng</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="study-form-label">Màu nhận diện</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border-0 p-0 rounded-circle flex-shrink-0" id="edit_input_mau_sac" name="mau_sac" value="#3b82f6" style="width: 38px; height: 38px;">
                                    <small class="text-muted">Màu hiển thị trên Lịch</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="study-modal-footer">
                        <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="study-action-btn study-btn-primary" id="btnSubmitEditMonHoc">
                            <i class="bi bi-check-lg"></i>
                            <span>Cập nhật môn học</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: THÊM BÀI TẬP & DEADLINE -->
    <div class="modal fade study-modal" id="modalThemBaiTap" tabindex="-1" aria-labelledby="modalThemBaiTapLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 520px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalThemBaiTapLabel">
                        <i class="bi bi-clock-history text-purple" style="color: var(--study-purple);"></i>
                        <span>Thêm Bài Tập & Deadline Mới</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formThemBaiTap" action="{{ route('bai-tap.store') }}" method="POST">
                    @csrf
                    <div class="study-modal-body">
                        <div class="row g-3">
                            <!-- Chọn môn học -->
                            <div class="col-12">
                                <label for="bt_mon_hoc_id" class="study-form-label">Môn học liên quan <span class="text-danger">*</span></label>
                                <select class="study-form-select" id="bt_mon_hoc_id" name="mon_hoc_id" required>
                                    @foreach($monHocs as $m)
                                    <option value="{{ $m->id }}">{{ $m->ten_mon }} ({{ $m->ma_mon }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tiêu đề bài tập -->
                            <div class="col-12">
                                <label for="bt_tieu_de" class="study-form-label">Tiêu đề bài tập / Deadline <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="bt_tieu_de" name="tieu_de" placeholder="VD: Báo cáo cuối kì CD-PHP" required>
                            </div>

                            <!-- Hạn nộp -->
                            <div class="col-sm-7">
                                <label for="bt_han_nop" class="study-form-label">Hạn nộp (Ngày & Giờ) <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="study-form-control" id="bt_han_nop" name="han_nop" value="{{ date('Y-m-d\T19:00', strtotime('+3 days')) }}" required>
                            </div>

                            <!-- Mức độ ưu tiên -->
                            <div class="col-sm-5">
                                <label for="bt_muc_do_uu_tien" class="study-form-label">Ưu tiên</label>
                                <select class="study-form-select" id="bt_muc_do_uu_tien" name="muc_do_uu_tien">
                                    <option value="cao" selected>Cao</option>
                                    <option value="trung_binh">Trung bình</option>
                                    <option value="thap">Thấp</option>
                                </select>
                            </div>

                            <!-- Mô tả / Ghi chú -->
                            <div class="col-12">
                                <label for="bt_mo_ta" class="study-form-label">Mô tả / Hướng dẫn nộp</label>
                                <textarea class="study-form-control" id="bt_mo_ta" name="mo_ta" rows="2" placeholder="Ghi chú file nộp, link GitHub, yêu cầu..."></textarea>
                            </div>

                            <!-- Banner tự động đồng bộ -->
                            <div class="col-12">
                                <div class="p-2.5 rounded-3 d-flex align-items-center gap-2 small" style="background-color: var(--study-surface-subtle); border: 1px solid var(--study-border);">
                                    <span class="fs-5">🚀</span>
                                    <span class="text-muted">Tự động đồng bộ và lưu vào trang Lịch dưới mục <strong>Deadline</strong>.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="study-modal-footer">
                        <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="study-action-btn study-btn-purple" id="btnSubmitBaiTap">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tạo bài tập</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: CHỈNH SỬA BÀI TẬP & DEADLINE -->
    <div class="modal fade study-modal" id="editBaiTapModal" tabindex="-1" aria-labelledby="modalEditBaiTapLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 520px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalEditBaiTapLabel">
                        <i class="bi bi-pencil-square text-purple" style="color: var(--study-purple);"></i>
                        <span>Chỉnh Sửa Bài Tập & Deadline</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditBaiTap" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_bt_id" name="id">
                    <div class="study-modal-body">
                        <div class="row g-3">
                            <!-- Chọn môn học -->
                            <div class="col-12">
                                <label for="edit_bt_mon_hoc_id" class="study-form-label">Môn học liên quan <span class="text-danger">*</span></label>
                                <select class="study-form-select" id="edit_bt_mon_hoc_id" name="mon_hoc_id" required>
                                    @foreach($monHocs as $m)
                                    <option value="{{ $m->id }}">{{ $m->ten_mon }} ({{ $m->ma_mon }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tiêu đề bài tập -->
                            <div class="col-12">
                                <label for="edit_bt_tieu_de" class="study-form-label">Tiêu đề bài tập / Deadline <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="edit_bt_tieu_de" name="tieu_de" placeholder="VD: Báo cáo cuối kì CD-PHP" required>
                            </div>

                            <!-- Hạn nộp -->
                            <div class="col-sm-7">
                                <label for="edit_bt_han_nop" class="study-form-label">Hạn nộp (Ngày & Giờ) <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="study-form-control" id="edit_bt_han_nop" name="han_nop" required>
                            </div>

                            <!-- Mức độ ưu tiên -->
                            <div class="col-sm-5">
                                <label for="edit_bt_muc_do_uu_tien" class="study-form-label">Ưu tiên</label>
                                <select class="study-form-select" id="edit_bt_muc_do_uu_tien" name="muc_do_uu_tien">
                                    <option value="cao">Cao</option>
                                    <option value="trung_binh">Trung bình</option>
                                    <option value="thap">Thấp</option>
                                </select>
                            </div>

                            <!-- Trạng thái hoàn thành -->
                            <div class="col-12">
                                <label for="edit_bt_trang_thai" class="study-form-label">Trạng thái hoàn thành</label>
                                <select class="study-form-select" id="edit_bt_trang_thai" name="trang_thai">
                                    <option value="chua_hoan_thanh">Chưa hoàn thành</option>
                                    <option value="dang_thuc_hien">Đang thực hiện</option>
                                    <option value="da_hoan_thanh">Đã hoàn thành</option>
                                </select>
                            </div>

                            <!-- Mô tả / Ghi chú -->
                            <div class="col-12">
                                <label for="edit_bt_mo_ta" class="study-form-label">Mô tả / Hướng dẫn nộp</label>
                                <textarea class="study-form-control" id="edit_bt_mo_ta" name="mo_ta" rows="2" placeholder="Ghi chú file nộp, link GitHub, yêu cầu..."></textarea>
                            </div>

                            <!-- Banner tự động đồng bộ -->
                            <div class="col-12">
                                <div class="p-2.5 rounded-3 d-flex align-items-center gap-2 small" style="background-color: var(--study-surface-subtle); border: 1px solid var(--study-border);">
                                    <span class="fs-5">🚀</span>
                                    <span class="text-muted">Cập nhật sẽ tự động đồng bộ sang lịch trình <strong>Deadline</strong> tương ứng.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="study-modal-footer">
                        <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="study-action-btn study-btn-purple" id="btnSubmitEditBaiTap">
                            <i class="bi bi-check-lg"></i>
                            <span>Cập nhật bài tập</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: ĐĂNG KÝ GIA SƯ -->
    <div class="modal fade study-modal" id="modalDangKyGiaSu" tabindex="-1" aria-labelledby="modalDangKyGiaSuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 540px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalDangKyGiaSuLabel">
                        <i class="bi bi-mortarboard-fill text-success"></i>
                        <span>Đăng Ký Hồ Sơ Gia Sư</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formDangKyGiaSu" action="{{ route('gia-su.dang-ky') }}" method="POST">
                    @csrf
                    <div class="study-modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="gs_ho_ten" class="study-form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="gs_ho_ten" name="ho_ten" value="{{ Auth::check() ? Auth::user()->ho_ten : '' }}" placeholder="VD: Nguyễn Văn Quang" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="gs_chuyen_mon" class="study-form-label">Chuyên môn giảng dạy <span class="text-danger">*</span></label>
                                <input type="text" class="study-form-control" id="gs_chuyen_mon" name="chuyen_mon" placeholder="VD: Lập trình Web, Laravel, CSDL..." required>
                            </div>
                            <div class="col-sm-6">
                                <label for="gs_so_dien_thoai" class="study-form-label">Số điện thoại / Zalo</label>
                                <input type="text" class="study-form-control" id="gs_so_dien_thoai" name="so_dien_thoai" placeholder="VD: 0987654321">
                            </div>

                            <div class="col-sm-6">
                                <label for="gs_email" class="study-form-label">Email liên hệ</label>
                                <input type="email" class="study-form-control" id="gs_email" name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label for="gs_hoc_phi" class="study-form-label">Học phí đề xuất (đ/giờ)</label>
                                <input type="number" class="study-form-control" id="gs_hoc_phi" name="hoc_phi_theo_gio" value="150000" step="10000">
                            </div>

                            <div class="col-12">
                                <label for="gs_mo_ta" class="study-form-label">Mô tả kinh nghiệm & phương pháp</label>
                                <textarea class="study-form-control" id="gs_mo_ta" name="mo_ta_kinh_nghiem" rows="3" placeholder="Chia sẻ kinh nghiệm giảng dạy hoặc thành tích học tập của bạn..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="study-modal-footer">
                        <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="study-action-btn study-btn-primary" id="btnSubmitGiaSu">
                            <i class="bi bi-send-fill"></i>
                            <span>Nộp hồ sơ Gia sư</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: QUICK WORLD CHAT -->
    <div class="modal fade study-modal" id="modalWorldChatQuick" tabindex="-1" aria-labelledby="modalWorldChatLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title" id="modalWorldChatLabel">
                        <i class="bi bi-chat-dots-fill text-primary"></i>
                        <span>Tin Nhắn & Kênh Trò Chuyện</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="study-modal-body p-3">
                    <div id="quickChatMessages" class="p-3 rounded-3 mb-3" style="max-height: 320px; overflow-y: auto; background-color: var(--study-surface-subtle); border: 1px solid var(--study-border); font-size: 0.85rem;">
                        <div class="text-center text-muted py-3">Đang tải tin nhắn gần đây...</div>
                    </div>
                    <form id="formSendQuickChat" class="d-flex gap-2">
                        <input type="text" id="inputQuickChatMessage" class="study-form-control" placeholder="Nhập tin nhắn..." autocomplete="off" required>
                        <button type="submit" class="study-action-btn study-btn-primary px-3">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: CONFIRM DELETE MÔN HỌC -->
    <div class="modal fade study-modal" id="modalConfirmDeleteMonHoc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 400px;">
            <div class="modal-content">
                <div class="study-modal-header">
                    <h5 class="study-modal-title text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Xác nhận xóa môn</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="study-modal-body">
                    <p class="mb-0">Bạn có chắc chắn muốn xóa môn học <strong id="deleteMonHocName"></strong>? Mọi lịch học liên quan sẽ được tự động dọn dẹp.</p>
                </div>
                <div class="study-modal-footer">
                    <button type="button" class="study-action-btn study-btn-outline" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="study-action-btn study-btn-delete px-3" id="btnConfirmDeleteMonHocAction">
                        <i class="bi bi-trash3-fill"></i> Xóa vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION FLOATING -->
    <div id="studyToast" class="study-toast toast-success">
        <i class="bi bi-check-circle-fill fs-5" id="toastIcon"></i>
        <span id="toastMsg">Thao tác thành công!</span>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Manager Script -->
    <script src="{{ asset('js/theme-manager.js') }}"></script>

    <!-- Page Logic Script -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // 1. Toast Notification Helper
        function showStudyToast(message, isError = false) {
            const toast = document.getElementById('studyToast');
            const msgEl = document.getElementById('toastMsg');
            const iconEl = document.getElementById('toastIcon');

            if (!toast) return;
            msgEl.textContent = message;
            toast.className = 'study-toast ' + (isError ? 'toast-error' : 'toast-success');
            iconEl.className = isError ? 'bi bi-exclamation-circle-fill fs-5' : 'bi bi-check-circle-fill fs-5';

            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3200);
        }

        // 2. Dark / Light Mode Toggle
        function toggleThemeMode() {
            if (window.LifePlannerTheme && typeof window.LifePlannerTheme.toggle === 'function') {
                window.LifePlannerTheme.toggle();
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const icon = document.getElementById('themeIcon');
                if (icon) {
                    icon.className = currentTheme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
                }
                return;
            }

            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            if (newTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
                document.body?.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
                document.body?.classList.remove('dark-theme');
            }
            localStorage.setItem('theme', newTheme);

            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
            }

            // Sync with backend if available
            fetch('/profile/theme/quick-toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ giao_dien: newTheme })
            }).catch(e => console.log('Theme toggle background sync:', e));
        }

        // Sync initial icon state
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = currentTheme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
            }
        });

        // 3. Toggle Assignment Status (Square Checkbox)
        async function toggleAssignmentStatus(id, checkbox) {
            try {
                const res = await fetch(`/bai-tap/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const titleEl = document.getElementById(`titleBt${id}`);
                    const cardEl = document.getElementById(`assignmentItem${id}`);
                    if (data.is_completed) {
                        titleEl?.classList.add('is-done');
                        cardEl?.classList.add('completed-item');
                    } else {
                        titleEl?.classList.remove('is-done');
                        cardEl?.classList.remove('completed-item');
                    }
                    showStudyToast(data.message || 'Cập nhật trạng thái bài tập thành công!');
                }
            } catch (err) {
                console.error(err);
                checkbox.checked = !checkbox.checked;
                showStudyToast('Lỗi khi cập nhật trạng thái!', true);
            }
        }

        // 4. Delete Course Confirmation & Action
        let courseIdToDelete = null;
        function confirmDeleteMonHoc(id, name) {
            courseIdToDelete = id;
            document.getElementById('deleteMonHocName').textContent = `"${name}"`;
            const modalEl = document.getElementById('modalConfirmDeleteMonHoc');
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }

        document.getElementById('btnConfirmDeleteMonHocAction')?.addEventListener('click', async function() {
            if (!courseIdToDelete) return;
            try {
                const res = await fetch(`/mon-hoc/${courseIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const row = document.getElementById(`rowMonHoc${courseIdToDelete}`);
                    if (row) {
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 250);
                    }
                    bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteMonHoc')).hide();
                    showStudyToast('Đã xóa môn học thành công!');

                    // Update count
                    const rowsCount = document.querySelectorAll('#tbodyMonHoc tr[id^="rowMonHoc"]').length - 1;
                    const countEl = document.getElementById('totalSubjectCountText');
                    if (countEl) countEl.textContent = `${Math.max(0, rowsCount)} Môn Học`;
                }
            } catch (err) {
                console.error(err);
                showStudyToast('Lỗi khi xóa môn học!', true);
            }
        });

        // 5. Delete Assignment Quick Action
        async function confirmDeleteBaiTap(id, title) {
            if (!confirm(`Bạn có chắc chắn muốn xóa bài tập "${title}"?`)) return;
            try {
                const res = await fetch(`/bai-tap/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const item = document.getElementById(`assignmentItem${id}`);
                    if (item) {
                        item.style.opacity = '0';
                        setTimeout(() => item.remove(), 250);
                    }
                    showStudyToast('Đã xóa bài tập thành công!');
                }
            } catch (err) {
                console.error(err);
                showStudyToast('Lỗi khi xóa bài tập!', true);
            }
        }

        // 5.0 Multiple Sessions Per-Day Time Settings Helper
        const DOW_NAMES_MAP = {
            1: 'Thứ 2',
            2: 'Thứ 3',
            3: 'Thứ 4',
            4: 'Thứ 5',
            5: 'Thứ 6',
            6: 'Thứ 7',
            7: 'Chủ Nhật'
        };

        function updatePerDayTimeSlots(prefix, customTimeMap = {}) {
            const container = document.getElementById(`${prefix}_per_day_container`);
            const listEl = document.getElementById(`${prefix}_per_day_list`);
            const commonStartInput = document.getElementById(prefix === 'create' ? 'create_gio_bat_dau' : 'edit_input_gio_bat_dau');
            const commonEndInput = document.getElementById(prefix === 'create' ? 'create_gio_ket_thuc' : 'edit_input_gio_ket_thuc');

            if (!container || !listEl) return;

            const defaultStart = commonStartInput?.value || '07:00';
            const defaultEnd = commonEndInput?.value || '10:00';

            const checkboxes = document.querySelectorAll(`.${prefix}-dow-check:checked`);
            const checkedDays = Array.from(checkboxes).map(cb => parseInt(cb.value, 10)).sort((a, b) => a - b);

            if (checkedDays.length < 2) {
                container.classList.add('d-none');
                listEl.innerHTML = '';
                return;
            }

            container.classList.remove('d-none');

            // Preserve existing user inputs if already typed in this session
            const currentValues = { ...customTimeMap };
            listEl.querySelectorAll('.study-dow-time-row').forEach(row => {
                const day = parseInt(row.getAttribute('data-day'), 10);
                const sIn = row.querySelector(`input[name="gio_bat_dau_theo_thu[${day}]"]`);
                const eIn = row.querySelector(`input[name="gio_ket_thuc_theo_thu[${day}]"]`);
                if (sIn && eIn && !currentValues[day]) {
                    currentValues[day] = { start: sIn.value, end: eIn.value };
                }
            });

            listEl.innerHTML = checkedDays.map(dayIso => {
                const dayName = DOW_NAMES_MAP[dayIso] || `Thứ ${dayIso}`;
                const startTime = currentValues[dayIso]?.start || defaultStart;
                const endTime = currentValues[dayIso]?.end || defaultEnd;

                return `
                    <div class="study-dow-time-row d-flex align-items-center justify-content-between p-2 rounded-3" data-day="${dayIso}" style="background: var(--nm-surface); border: 1px solid var(--study-border); box-shadow: var(--nm-shadow-flat-sm);">
                        <div class="d-flex align-items-center gap-2 study-dow-time-badge-col">
                            <span class="badge rounded-pill" style="background: var(--nm-time-bg); color: var(--nm-time-text); font-weight: 700; font-size: 0.8rem; padding: 5px 12px; border: 1px solid rgba(59, 130, 246, 0.25);">
                                <i class="bi bi-calendar3 me-1"></i>${dayName}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 study-dow-time-inputs-col">
                            <div class="d-flex align-items-center gap-1 flex-grow-1">
                                <span class="small text-muted" style="font-size: 0.75rem;">Từ</span>
                                <input type="time" class="study-form-control form-control-sm text-center" style="width: 108px;" name="gio_bat_dau_theo_thu[${dayIso}]" value="${startTime}">
                            </div>
                            <span class="text-muted small">&rarr;</span>
                            <div class="d-flex align-items-center gap-1 flex-grow-1">
                                <span class="small text-muted" style="font-size: 0.75rem;">Đến</span>
                                <input type="time" class="study-form-control form-control-sm text-center" style="width: 108px;" name="gio_ket_thuc_theo_thu[${dayIso}]" value="${endTime}">
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // 5.1 Open & Submit Edit Môn Học Modal
        function openEditMonHocModal(data) {
            const form = document.getElementById('formEditMonHoc');
            if (!form) return;
            form.action = `/mon-hoc/${data.id}`;
            document.getElementById('edit_mh_id').value = data.id;
            document.getElementById('edit_input_ten_mon').value = data.ten_mon || '';
            document.getElementById('edit_input_ma_mon').value = data.ma_mon || '';
            document.getElementById('edit_input_so_tin_chi').value = data.so_tin_chi || 3;
            document.getElementById('edit_input_giang_vien').value = data.giang_vien || '';
            document.getElementById('edit_input_phong_hoc').value = data.phong_hoc || '';
            document.getElementById('edit_input_ngay_bat_dau').value = data.ngay_bat_dau || '';
            document.getElementById('edit_input_ngay_ket_thuc').value = data.ngay_ket_thuc || '';
            document.getElementById('edit_select_trang_thai').value = data.trang_thai || 'dang_hoc';
            document.getElementById('edit_input_mau_sac').value = data.mau_sac || '#3b82f6';
            document.getElementById('edit_input_gio_bat_dau').value = data.gio_bat_dau || '07:00';
            document.getElementById('edit_input_gio_ket_thuc').value = data.gio_ket_thuc || '10:00';
            document.getElementById('edit_check_dong_bo_calendar').checked = (data.dong_bo_calendar !== 0 && data.dong_bo_calendar !== false);

            const dows = Array.isArray(data.ngay_trong_tuan) ? data.ngay_trong_tuan.map(Number) : [];
            const dowMap = { 1: 'edit_dow_2', 2: 'edit_dow_3', 3: 'edit_dow_4', 4: 'edit_dow_5', 5: 'edit_dow_6', 6: 'edit_dow_7', 7: 'edit_dow_cn' };
            Object.entries(dowMap).forEach(([val, elId]) => {
                const el = document.getElementById(elId);
                if (el) el.checked = dows.includes(Number(val));
            });

            const customTimeMap = {};
            if (Array.isArray(data.lich_hocs)) {
                data.lich_hocs.forEach(lh => {
                    customTimeMap[lh.ngay_trong_tuan] = {
                        start: lh.gio_bat_dau,
                        end: lh.gio_ket_thuc
                    };
                });
            }

            updatePerDayTimeSlots('edit', customTimeMap);

            const modalEl = document.getElementById('editMonHocModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        // Listeners for checkbox changes in create and edit modals
        document.querySelectorAll('.create-dow-check').forEach(cb => {
            cb.addEventListener('change', () => updatePerDayTimeSlots('create'));
        });

        document.querySelectorAll('.edit-dow-check').forEach(cb => {
            cb.addEventListener('change', () => updatePerDayTimeSlots('edit'));
        });

        document.getElementById('btn_apply_common_create')?.addEventListener('click', function() {
            const s = document.getElementById('create_gio_bat_dau')?.value;
            const e = document.getElementById('create_gio_ket_thuc')?.value;
            document.querySelectorAll('#create_per_day_list input[name^="gio_bat_dau_theo_thu"]').forEach(inp => inp.value = s);
            document.querySelectorAll('#create_per_day_list input[name^="gio_ket_thuc_theo_thu"]').forEach(inp => inp.value = e);
            showStudyToast('Đã đồng bộ khung giờ chung cho tất cả các buổi!');
        });

        document.getElementById('btn_apply_common_edit')?.addEventListener('click', function() {
            const s = document.getElementById('edit_input_gio_bat_dau')?.value;
            const e = document.getElementById('edit_input_gio_ket_thuc')?.value;
            document.querySelectorAll('#edit_per_day_list input[name^="gio_bat_dau_theo_thu"]').forEach(inp => inp.value = s);
            document.querySelectorAll('#edit_per_day_list input[name^="gio_ket_thuc_theo_thu"]').forEach(inp => inp.value = e);
            showStudyToast('Đã đồng bộ khung giờ chung cho tất cả các buổi!');
        });

        // Initialize create modal per-day slots when modal opens
        document.getElementById('createMonHocModal')?.addEventListener('show.bs.modal', function() {
            updatePerDayTimeSlots('create');
        });

        // AJAX submit for createMonHocForm
        document.getElementById('createMonHocForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitMonHoc');
            if (btn) btn.disabled = true;
            try {
                const formData = new FormData(this);
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await res.json();
                if (res.ok && result.success) {
                    showStudyToast(result.message || 'Thêm môn học thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('createMonHocModal'))?.hide();
                    setTimeout(() => location.reload(), 500);
                } else {
                    showStudyToast(result.message || 'Lỗi khi thêm môn học!', true);
                }
            } catch (err) {
                console.error(err);
                showStudyToast('Lỗi khi gửi yêu cầu thêm môn học!', true);
            } finally {
                if (btn) btn.disabled = false;
            }
        });

        document.getElementById('formEditMonHoc')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitEditMonHoc');
            if (btn) btn.disabled = true;
            try {
                const formData = new FormData(this);
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await res.json();
                if (res.ok && result.success) {
                    showStudyToast(result.message || 'Cập nhật môn học thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('editMonHocModal'))?.hide();
                    setTimeout(() => location.reload(), 500);
                } else {
                    showStudyToast(result.message || 'Lỗi khi cập nhật môn học!', true);
                }
            } catch (err) {
                console.error(err);
                showStudyToast('Lỗi khi gửi yêu cầu cập nhật!', true);
            } finally {
                if (btn) btn.disabled = false;
            }
        });

        // 5.2 Open & Submit Edit Bài Tập Modal
        function openEditBaiTapModal(data) {
            const form = document.getElementById('formEditBaiTap');
            if (!form) return;
            form.action = `/bai-tap/${data.id}`;
            document.getElementById('edit_bt_id').value = data.id;
            document.getElementById('edit_bt_mon_hoc_id').value = data.mon_hoc_id || '';
            document.getElementById('edit_bt_tieu_de').value = data.tieu_de || '';
            document.getElementById('edit_bt_han_nop').value = data.han_nop || '';
            document.getElementById('edit_bt_muc_do_uu_tien').value = data.muc_do_uu_tien || 'cao';
            document.getElementById('edit_bt_trang_thai').value = data.trang_thai || 'chua_hoan_thanh';
            document.getElementById('edit_bt_mo_ta').value = data.mo_ta || '';

            const modalEl = document.getElementById('editBaiTapModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        document.getElementById('formEditBaiTap')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitEditBaiTap');
            if (btn) btn.disabled = true;
            try {
                const formData = new FormData(this);
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await res.json();
                if (res.ok && result.success) {
                    showStudyToast(result.message || 'Cập nhật bài tập thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('editBaiTapModal'))?.hide();
                    setTimeout(() => location.reload(), 500);
                } else {
                    showStudyToast(result.message || 'Lỗi khi cập nhật bài tập!', true);
                }
            } catch (err) {
                console.error(err);
                showStudyToast('Lỗi khi gửi yêu cầu cập nhật!', true);
            } finally {
                if (btn) btn.disabled = false;
            }
        });

        // 6. World Chat Messages quick load & send
        const modalWorldChat = document.getElementById('modalWorldChatQuick');
        modalWorldChat?.addEventListener('show.bs.modal', async function () {
            const container = document.getElementById('quickChatMessages');
            try {
                const res = await fetch('/world-chat/messages');
                const data = await res.json();
                if (data.success && data.data && data.data.length > 0) {
                    container.innerHTML = data.data.slice(-15).map(m => `
                        <div class="mb-2">
                            <span class="fw-bold text-primary">${m.user_name || 'Người dùng'}:</span>
                            <span>${m.message || m.noi_dung}</span>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">${m.time || ''}</small>
                        </div>
                    `).join('');
                    container.scrollTop = container.scrollHeight;
                } else {
                    container.innerHTML = '<div class="text-center text-muted py-3">Chưa có tin nhắn nào. Hãy gửi lời chào đầu tiên!</div>';
                }
            } catch (err) {
                container.innerHTML = '<div class="text-center text-muted py-3">Kênh trò chuyện đang sẵn sàng kết nối.</div>';
            }
        });

        document.getElementById('formSendQuickChat')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const input = document.getElementById('inputQuickChatMessage');
            const msg = input.value.trim();
            if (!msg) return;

            try {
                const res = await fetch('/world-chat/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: msg, noi_dung: msg })
                });
                input.value = '';
                const container = document.getElementById('quickChatMessages');
                container.innerHTML += `
                    <div class="mb-2">
                        <span class="fw-bold text-success">Bạn:</span>
                        <span>${msg}</span>
                    </div>
                `;
                container.scrollTop = container.scrollHeight;
            } catch (err) {
                console.error(err);
            }
        });

        // 7. Mobile Study Actions Collapse: Tự động đóng khi nhấn mở modal hoặc bấm ra ngoài
        document.querySelectorAll('#studyActionsCollapse [data-bs-toggle="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                if (window.innerWidth >= 992) return;
                const collapseEl = document.getElementById('studyActionsCollapse');
                if (collapseEl && collapseEl.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(collapseEl)?.hide();
                }
            });
        });

        document.addEventListener('click', function(e) {
            if (window.innerWidth >= 992) return;
            const wrapper = document.querySelector('.study-actions-wrapper');
            const collapseEl = document.getElementById('studyActionsCollapse');
            if (wrapper && collapseEl && collapseEl.classList.contains('show')) {
                if (!wrapper.contains(e.target)) {
                    bootstrap.Collapse.getInstance(collapseEl)?.hide();
                }
            }
        });
    </script>

    <!-- Floating Action Menu (Bong bóng điều hướng nhanh) -->
    <x-floating-menu />

    <!-- PWA Install Modal -->
    <x-pwa-install-modal />
</body>

</html>