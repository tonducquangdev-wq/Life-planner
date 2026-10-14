<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tập Luyện & Thể Chất - Life Planner</title>

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

    <!-- CSS Dashboard dùng chung của hệ thống -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : '1.0' }}">
    <!-- CSS riêng của Module Thể Chất & Tập Luyện -->
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}?v={{ file_exists(public_path('css/tap-luyen.css')) ? filemtime(public_path('css/tap-luyen.css')) : '1.0' }}">
    @include('layouts.pwa-head')
</head>

<body>

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH FULL WIDTH - FLOATING MENU) -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Theo dõi Thể chất'])

        <!-- CONTENT BODY (CUỘN ĐỘC LẬP) -->
        <main class="content-body">

            <!-- 1. HEADER TẬP LUYỆN (SECTION 6) -->
            <div class="workout-header-wrapper">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2.5">
                    <!-- Title & Subtitle with Hierarchy + Mobile Toggle Button -->
                    <div class="d-flex align-items-center justify-content-between w-100 w-md-auto">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="workout-title-icon"><i class="bi bi-heart-pulse-fill text-danger"></i></span>
                                <h4 class="workout-page-title mb-0">Tập luyện</h4>
                            </div>
                            <p class="workout-page-subtitle mb-0 mt-0.5">Quản lý lịch tập, kế hoạch và theo dõi tiến độ tập luyện</p>
                        </div>

                        <!-- NÚT BẬT/TẮT THU GỌN TOÀN BỘ CÔNG CỤ TRÊN MOBILE -->
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 shadow-2xs d-inline-flex align-items-center gap-1.5 d-md-none flex-shrink-0 ms-2"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#workoutHeaderActionsCollapse"
                            aria-expanded="false"
                            aria-controls="workoutHeaderActionsCollapse"
                            id="btnToggleWorkoutActions">
                            <i class="bi bi-sliders"></i>
                            <span>Tùy chọn</span>
                            <i class="bi bi-chevron-down ms-0.5 fs-8 transition-transform" id="iconToggleWorkoutActions"></i>
                        </button>
                    </div>

                    <!-- Toolbar Actions & Tabs: Thu gọn toàn bộ vào nút Tùy chọn trên Mobile, bấm vào mới cuộn ra -->
                    <div class="collapse d-md-block w-100 w-md-auto" id="workoutHeaderActionsCollapse">
                        <div class="workout-actions-inner d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 pt-2 pt-md-0">

                            <!-- NHÓM 1: 3 CHẾ ĐỘ XEM TRÊN MOBILE (BUỔI TẬP / KẾ HOẠCH / LỊCH SỬ) -->
                            <div class="d-md-none workout-mobile-tabs-section">
                                <div class="text-muted fs-9 fw-bold text-uppercase letter-spacing-1 mb-2 px-1 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-layers-fill text-primary"></i> <span>Chế độ xem</span>
                                </div>
                                <div class="nav nav-pills workout-mobile-nav-pills d-flex flex-column gap-1.5" id="workoutMobileTabs" role="tablist">
                                    <!-- Tab 1: Buổi tập hôm nay -->
                                    <button class="nav-link active workout-mobile-tab-btn" id="mob-tab-today-workout-btn" data-bs-toggle="pill" data-bs-target="#tab-today-workout" type="button" role="tab" aria-selected="true">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <span class="d-inline-flex align-items-center">
                                                <i class="bi bi-lightning-charge-fill me-2 text-warning fs-6"></i>
                                                <span class="fw-semibold">Buổi tập hôm nay</span>
                                            </span>
                                            <i class="bi bi-check2 text-primary active-check-icon fs-7"></i>
                                        </div>
                                    </button>

                                    <!-- Tab 2: Kế hoạch rèn luyện -->
                                    <button class="nav-link workout-mobile-tab-btn" id="mob-tab-plans-btn" data-bs-toggle="pill" data-bs-target="#tab-plans" type="button" role="tab" aria-selected="false">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <span class="d-inline-flex align-items-center">
                                                <i class="bi bi-collection-fill me-2 text-primary fs-6"></i>
                                                <span class="fw-semibold">Kế hoạch rèn luyện</span>
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-9 fw-bold">{{ $keHoachList->count() }}</span>
                                        </div>
                                    </button>

                                    <!-- Tab 3: Lịch sử hoạt động -->
                                    <button class="nav-link workout-mobile-tab-btn" id="mob-tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" role="tab" aria-selected="false">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <span class="d-inline-flex align-items-center">
                                                <i class="bi bi-clock-history me-2 text-success fs-6"></i>
                                                <span class="fw-semibold">Lịch sử hoạt động</span>
                                            </span>
                                            <i class="bi bi-check2 text-primary active-check-icon fs-7"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- ĐƯỜNG KẺ PHÂN CÁCH TRÊN MOBILE -->
                            <div class="workout-mobile-divider d-md-none my-1"></div>

                            <!-- NHÓM 2: CÁC NÚT CÔNG CỤ & TÁC VỤ (CẢ MOBILE LẪN DESKTOP) -->
                            <div class="workout-actions-buttons-section">
                                <div class="d-md-none text-muted fs-9 fw-bold text-uppercase letter-spacing-1 mb-2 px-1 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-sliders text-primary"></i> <span>Tùy chỉnh & Công cụ</span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <!-- Tạo kế hoạch (Primary CTA) -->
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs"
                                        data-bs-toggle="modal" data-bs-target="#manageScheduleModal"
                                        onclick="setTimeout(() => document.getElementById('pills-presets-tab')?.click(), 150);">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tạo kế hoạch</span>
                                    </button>

                                    <!-- Tùy chỉnh buổi tập -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs"
                                        data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                                        <i class="bi bi-sliders"></i>
                                        <span>Tùy chỉnh buổi tập</span>
                                    </button>

                                    <!-- Báo cáo thể chất -->
                                    <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs text-decoration-none">
                                        <i class="bi bi-bar-chart-fill me-1"></i>
                                        <span>Báo cáo</span>
                                    </a>

                                    <!-- Cộng đồng Gymer -->
                                    <a href="{{ route('workout.community') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs text-decoration-none d-inline-flex align-items-center gap-1.5" title="Khám phá và chia sẻ lịch tập trong Cộng đồng Gymer">
                                        <i class="bi bi-people-fill text-primary"></i>
                                        <span>Cộng đồng Gymer</span>
                                    </a>

                                    <!-- Chia sẻ Plan (nếu có active plan) -->
                                    @if($activePlan)
                                    <form method="POST" action="{{ route('workout.share.generate', ['id' => $activePlan->id]) }}" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs" title="Chia sẻ kế hoạch này cho bạn bè hoặc cộng đồng">
                                            <i class="bi bi-share me-1"></i>
                                            <span>{{ $activePlan->is_shared ? 'Mã: ' . $activePlan->share_code : 'Chia sẻ' }}</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. VIEW SWITCHER TABS (MODERN PRODUCTIVITY SEGMENT CONTROL - CHỈ HIỂN THỊ TRÊN DESKTOP, TRÊN MOBILE ĐÃ ĐƯỢC ĐƯA VÀO NÚT TÙY CHỌN) -->
            <div class="d-none d-md-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <ul class="nav nav-pills nav-segment-control p-1 rounded-pill shadow-2xs" id="workoutMainTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-today-workout-btn" data-bs-toggle="pill" data-bs-target="#tab-today-workout" type="button" role="tab" aria-selected="true">
                            <i class="bi bi-lightning-charge-fill me-1.5 text-warning"></i>Buổi tập hôm nay
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-plans-btn" data-bs-toggle="pill" data-bs-target="#tab-plans" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-collection-fill me-1.5 text-primary"></i>Kế hoạch rèn luyện ({{ $keHoachList->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-clock-history me-1.5 text-success"></i>Lịch sử hoạt động
                        </button>
                    </li>
                </ul>

                <!-- Active Plan Quick Info Badge -->
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="text-muted fs-8">Kế hoạch áp dụng:</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8 fw-bold">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ $activePlan->ten_ke_hoach ?? 'Kế hoạch cá nhân' }}
                    </span>
                </div>
            </div>

            <!-- 3. TAB CONTENT -->
            <div class="tab-content" id="workoutMainTabContent">

                <!-- ====================================================================
                     TAB 1: BUỔI TẬP HÔM NAY & RÈN LUYỆN THỰC CHIẾN
                     ==================================================================== -->
                <div class="tab-pane fade show active workout-tab-pane" id="tab-today-workout" role="tabpanel" aria-labelledby="tab-today-workout-btn">

                    <!-- 1. LỊCH TRÌNH 7 NGÀY (WEEKLY SCHEDULE STRIP - ĐƯỢC ĐƯA LÊN ĐẦU TRÊN MOBILE) -->
                    <div class="card schedule-strip-card shadow-2xs p-2.5 px-3 mb-3 workout-section-schedule">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                            <!-- Label info -->
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-pill fw-semibold fs-8">
                                    <i class="bi bi-calendar3 me-1"></i>Lịch tuần
                                </span>
                                <span class="text-muted fs-8 d-none d-xl-inline">Phân bổ rèn luyện:</span>
                            </div>

                            <!-- 7-Day Grid -->
                            <div class="weekly-schedule-grid flex-grow-1" id="weekly-schedule-pills">
                                @foreach($lichTuan as $dayIso => $dh)
                                @php
                                $isToday = ($dayIso === $todayIso);
                                $isSelected = ($currentBuoiTap && $dh['buoi_tap_id'] === $currentBuoiTap->id);
                                @endphp
                                <a href="{{ $dh['buoi_tap_id'] ? route('tap-luyen.index', ['buoi_tap_id' => $dh['buoi_tap_id']]) : route('tap-luyen.index') }}"
                                    class="schedule-day-box schedule-day-pill {{ $isToday ? 'is-today' : '' }} {{ $isSelected ? 'is-selected' : '' }} {{ $dh['is_rest'] ? 'is-rest' : 'is-workout' }}"
                                    title="{{ $dh['thu'] }}: {{ $dh['mo_ta'] }}"
                                    data-day="{{ $dayIso }}"
                                    data-buoi-tap-id="{{ $dh['buoi_tap_id'] ?? '' }}"
                                    data-name="{{ $dh['ten'] }}">
                                    <div class="d-flex align-items-center justify-content-between w-100 mb-0.5">
                                        <span class="day-code">{{ $dh['short'] ?? $dh['thu'] }}</span>
                                        @if($isToday)
                                        <span class="today-dot" title="Hôm nay"></span>
                                        @endif
                                    </div>
                                    <div class="session-name">
                                        @if($dh['is_rest'])
                                        <i class="bi bi-cup-hot me-1 text-muted"></i>Nghỉ
                                        @else
                                        {{ $dh['ten'] }}
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($isRestDay)
                    <!-- 2. REST DAY VIEW (GIAO DIỆN NGÀY NGHỈ PHỤC HỒI) -->
                    <div class="rest-day-card shadow-2xs text-center mb-3 workout-section-combat">
                        <div class="rest-day-icon mb-2.5 rest-day-icon-bounce" style="font-size: 3rem;">😴</div>
                        <h5 class="fw-bold text-dark mb-1 fs-5">HÔM NAY LÀ NGÀY NGHỈ (REST DAY)</h5>
                        <p class="text-muted fs-8 mb-3 mx-auto" style="max-width: 480px; line-height: 1.5;">
                            Hãy nghỉ ngơi, nạp đủ dinh dưỡng và thư giãn cơ bắp để tái tạo năng lượng cho các buổi rèn luyện tiếp theo.
                        </p>
                        <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                            <span class="text-muted fs-8">Vẫn muốn rèn luyện hôm nay?</span>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold dropdown-toggle shadow-2xs" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-play-circle me-1"></i> Chọn buổi tập khác
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    @forelse($buoiTapList as $b)
                                    <li>
                                        <a class="dropdown-item py-2 fs-8" href="{{ route('tap-luyen.index', ['buoi_tap_id' => $b['id']]) }}">
                                            <i class="bi bi-fire text-danger me-2"></i>{{ $b['title'] }} ({{ count($b['exercises']) }} bài)
                                        </a>
                                    </li>
                                    @empty
                                    <li><span class="dropdown-item text-muted fs-8">Chưa có buổi tập</span></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- 2. KHU VỰC THỰC CHIẾN CÂN ĐỐI: TIMER LÊN ĐẦU TRÊN MOBILE + CHECKLIST -->
                    <div class="workout-section-combat mb-3">
                        <div class="row g-3 align-items-start">

                            <!-- CỘT TIMER: TRÊN MOBILE HIỂN THỊ ĐẦU TIÊN (order-1), TRÊN DESKTOP NẰM BÊN PHẢI (order-lg-2) -->
                            <div class="col-12 col-lg-5 order-1 order-lg-2">
                                <div class="workout-timer-sticky">
                                    <div class="workout-main-card shadow-2xs">
                                        <div class="d-flex align-items-center justify-content-between mb-2.5 border-bottom pb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-1.5 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                    <i class="bi bi-stopwatch fs-6"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0 fs-7">Đồng hồ tập</h6>
                                                </div>
                                            </div>
                                            <span class="badge workout-status-badge badge-not-started" id="workout-status-badge">
                                                <i class="bi bi-circle-fill me-1 fs-9"></i>
                                                <span id="workout-status-text">Chưa bắt đầu</span>
                                            </span>
                                        </div>

                                        <!-- HỘP TIMER KỸ THUẬT SỐ -->
                                        <div class="workout-timer-box text-center mb-2.5">
                                            <div class="timer-display" id="workout-timer">00:00:00</div>
                                            <small class="text-muted fs-8">Bấm "Bắt đầu tập" để tính thời gian rèn luyện</small>
                                        </div>

                                        <!-- BÀI TẬP ĐANG ACTIVE TRONG PLAYER -->
                                        <div class="active-exercise-card mb-2.5 text-center">
                                            <div class="mb-1" id="active-ex-type-container">
                                                <span class="badge exercise-type-badge badge-type-strength px-2.5 py-0.5 rounded-pill fs-9" id="active-ex-type-badge">Strength</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1 exercise-title-text line-clamp-1 px-2 fs-7" id="active-ex-name">
                                                {{ !empty($danhSachBaiTap[0]) ? '1. ' . $danhSachBaiTap[0]['name'] : 'Chưa có bài tập' }}
                                            </h6>
                                            <div class="text-muted fw-semibold fs-8" id="active-ex-set-rep">
                                                {{ !empty($danhSachBaiTap[0]) ? ($danhSachBaiTap[0]['metric_display'] ?: ($danhSachBaiTap[0]['sets'].' sets × '.$danhSachBaiTap[0]['reps'].' reps')) : '-- x --' }}
                                            </div>
                                        </div>

                                        <!-- BỘ NÚT ĐIỀU KHIỂN TIMER -->
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-primary py-2 rounded-pill fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2 fs-7" id="toggle-timer-btn">
                                                <i class="bi bi-play-fill fs-5" id="toggle-timer-icon"></i>
                                                <span id="toggle-timer-text">Bắt đầu tập</span>
                                            </button>

                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-secondary py-1.5 rounded-pill fw-semibold flex-grow-1 d-flex align-items-center justify-content-center gap-1.5 fs-8" id="next-ex-btn">
                                                    <i class="bi bi-skip-forward-fill"></i>
                                                    <span>Bài tiếp theo</span>
                                                </button>

                                                <button class="btn btn-success py-1.5 rounded-pill fw-bold text-white flex-grow-1 shadow-xs d-flex align-items-center justify-content-center gap-1.5 fs-8" id="finish-ex-btn">
                                                    <i class="bi bi-check2-circle fs-6"></i>
                                                    <span>Hoàn thành</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tabs chọn nhanh buổi tập khác trong kế hoạch -->
                                        @if(count($buoiTapList) > 1)
                                        <div class="mt-3 pt-2.5 border-top">
                                            <small class="text-muted d-block mb-1.5 fs-9 fw-semibold text-uppercase">Các buổi tập trong kế hoạch:</small>
                                            <div class="d-flex flex-wrap gap-1.5" id="workout-tabs-container">
                                                @foreach($buoiTapList as $session)
                                                <a href="{{ route('tap-luyen.index', ['buoi_tap_id' => $session['id']]) }}"
                                                    class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-9 workout-tab {{ ($currentBuoiTap && $currentBuoiTap->id === $session['id']) ? 'active bg-primary text-white border-primary' : 'text-dark' }}"
                                                    data-id="{{ $session['id'] }}"
                                                    data-type="{{ $session['title'] }}">
                                                    {{ $session['title'] }}
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <!-- CỘT CHECKLIST: TRÊN MOBILE HIỂN THỊ DƯỚI TIMER (order-2), TRÊN DESKTOP NẰM BÊN TRÁI (order-lg-1) -->
                            <div class="col-12 col-lg-7 order-2 order-lg-1">
                                <!-- SLIM HERO PROGRESS CARD (SECTION 8) -->
                                <div class="workout-progress-card shadow-2xs mb-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="workout-progress-icon-badge">
                                                <i class="bi bi-trophy-fill text-warning"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="text-muted fs-8 fw-semibold text-uppercase letter-spacing-1">Tiến độ buổi tập:</span>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-8 fw-bold" id="workout-title-display">
                                                        {{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : 'Buổi rèn luyện' }}
                                                    </span>
                                                </div>
                                                <small class="text-muted fs-9 d-block mt-0.5" id="workout-progress-subtitle">Đánh dấu bài tập trong checklist để cập nhật tiến độ thực tế</small>
                                            </div>
                                        </div>
                                        <div class="text-sm-end">
                                            <span class="fw-bold fs-8 text-primary font-numeric" id="workout-progress-text">
                                                0 / {{ count($danhSachBaiTap) }} bài tập hoàn thành (0%)
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Slim 6px progress bar -->
                                    <div class="progress workout-slim-progress">
                                        <div class="progress-bar bg-success" id="workout-progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="workout-main-card shadow-2xs d-flex flex-column h-100">
                                    <!-- HEADER CHECKLIST (SECTION 12 & 13) -->
                                    <div class="checklist-header-box">
                                        <div class="checklist-header-top">
                                            <div class="checklist-header-title-wrap">
                                                <div class="checklist-header-icon">
                                                    <i class="bi bi-check2-square"></i>
                                                </div>
                                                <div>
                                                    <h6 class="checklist-header-title">Checklist bài tập</h6>
                                                    <span class="checklist-header-count" id="checklist-header-count-text">
                                                        0 / {{ count($danhSachBaiTap) }} bài đã hoàn thành
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-9 d-none d-sm-inline-block" id="checklist-count-badge">
                                                    {{ count($danhSachBaiTap) }} bài
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-muted fs-9 shadow-2xs" id="btn-uncheck-all" title="Bỏ chọn tất cả bài tập">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Làm mới
                                                </button>
                                            </div>
                                        </div>

                                        <!-- PROGRESS BAR MỎNG 6PX ĐỒNG BỘ THEO DÕI TIẾN ĐỘ -->
                                        <div class="checklist-header-progress-wrap">
                                            <div class="progress checklist-header-progress-bar">
                                                <div class="progress-bar bg-success" id="checklist-inline-progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="checklist-header-percent" id="checklist-inline-progress-percent">0%</span>
                                        </div>
                                    </div>

                                    <!-- VÙNG SCROLL ĐỘC LẬP CHO CHECKLIST (MAX-HEIGHT THÍCH ỨNG) -->
                                    <div class="workout-checklist-container d-flex flex-column flex-grow-1" id="workout-checklist-items">
                                        @forelse($danhSachBaiTap as $idx => $ex)
                                        <div class="checklist-exercise-item {{ $idx === 0 ? 'active-exercise' : '' }}"
                                            id="checklist-item-{{ $ex['id'] }}"
                                            data-id="{{ $ex['id'] }}"
                                            data-index="{{ $idx }}"
                                            data-name="{{ $ex['name'] }}"
                                            data-type="{{ $ex['type'] ?? 'strength' }}"
                                            data-metric="{{ $ex['metric_display'] ?: ($ex['sets'].' sets × '.$ex['reps'].' reps') }}"
                                            title="Bấm để chọn bài tập này trong trình tập luyện">

                                            <!-- CỘT 1: CHECKBOX (CÙNG KÍCH THƯỚC 18x18px, CÙNG VỊ TRÍ, KHÔNG BỊ CO LỆCH) -->
                                            <div class="checklist-item-checkbox-col">
                                                <input type="checkbox"
                                                    class="custom-check-box workout-exercise-checkbox"
                                                    id="chk-ex-{{ $ex['id'] }}"
                                                    data-id="{{ $ex['id'] }}"
                                                    data-index="{{ $idx }}"
                                                    aria-label="{{ $ex['name'] }}">
                                            </div>

                                            <!-- THÂN ITEM (4-ZONE STRUCTURE) -->
                                            <div class="checklist-item-content">
                                                <!-- CỘT 2: TÊN BÀI TẬP (BẮT ĐẦU CÙNG MỘT VỊ TRÍ, TỐI ĐA 2 DÒNG, KHÔNG ĐẨY CỘT KHÁC) -->
                                                <div class="checklist-item-title-col">
                                                    <h6 class="exercise-title" title="{{ $ex['name'] }}">
                                                        {{ $ex['name'] }}
                                                    </h6>
                                                </div>

                                                <!-- CONTAINER PHỤ CHO RESPONSIVE MOBILE -->
                                                <div class="checklist-item-bottom-row d-flex align-items-center">
                                                    <!-- CỘT 3: SETS / REPS / THÔNG TIN PHỤ (TABULAR DEDICATED ZONE) -->
                                                    <div class="checklist-item-meta-col">
                                                        <span class="checklist-item-metric-val">
                                                            {{ $ex['metric_display'] ?: ($ex['sets'].' sets × '.$ex['reps'].' reps') }}
                                                        </span>
                                                        @if(!empty($ex['group']))
                                                        <span class="checklist-item-group-tag d-none d-xxl-inline-block">
                                                            {{ $ex['group'] }}
                                                        </span>
                                                        @endif
                                                    </div>

                                                    <!-- CỘT 4: ACTION & TRẠNG THÁI (ĐẶT BÊN PHẢI, CÙNG VỊ TRÍ, KHÔNG LÀM NHẢY LAYOUT) -->
                                                    <div class="checklist-item-action-area">
                                                        <span class="badge bg-success-subtle text-success rounded-pill completed-tag d-none">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Xong
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-4 text-muted my-auto">
                                            <div class="mb-2"><i class="bi bi-inbox fs-2 text-secondary opacity-50"></i></div>
                                            <h6 class="fw-bold text-dark mb-1 fs-7">Chưa có bài tập nào</h6>
                                            <p class="fs-8 text-muted mb-2.5">Buổi tập này chưa được thiết lập danh sách bài tập.</p>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-8" data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                                                <i class="bi bi-plus-lg me-1"></i>Thêm bài tập vào buổi này
                                            </button>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif

                    <!-- 3. QUICK KPI STRIP (SLIM COMPACT STATS - NẰM DƯỚI CÙNG TRÊN MOBILE) -->
                    <div class="workout-kpi-strip workout-section-kpi">
                        <div class="workout-kpi-card shadow-2xs">
                            <div class="workout-kpi-icon bg-danger-subtle text-danger">
                                <i class="bi bi-fire"></i>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="workout-kpi-label">Chuỗi rèn luyện</div>
                                <div class="workout-kpi-val">{{ $chuoiNgayTap }} ngày 🔥</div>
                            </div>
                        </div>

                        <div class="workout-kpi-card shadow-2xs">
                            <div class="workout-kpi-icon bg-primary-subtle text-primary">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="workout-kpi-label">Buổi tập tuần này</div>
                                <div class="workout-kpi-val">{{ $buoiTapTuanNay }} buổi</div>
                            </div>
                        </div>

                        <div class="workout-kpi-card shadow-2xs">
                            <div class="workout-kpi-icon bg-success-subtle text-success">
                                <i class="bi bi-stopwatch-fill"></i>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="workout-kpi-label">Thời gian hôm nay</div>
                                <div class="workout-kpi-val">{{ $thoiGianTapHomNay }} phút</div>
                            </div>
                        </div>

                        <div class="workout-kpi-card shadow-2xs">
                            <div class="workout-kpi-icon bg-info-subtle text-info">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="workout-kpi-label">Tổng tích lũy</div>
                                <div class="workout-kpi-val">{{ $tongBuoiTap }} buổi</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ====================================================================
                     TAB 2: KẾ HOẠCH RÈN LUYỆN (SECTION 7 & SECTION 10)
                     ==================================================================== -->
            <div class="tab-pane fade" id="tab-plans" role="tabpanel" aria-labelledby="tab-plans-btn">

                <!-- Header bar of plans -->
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-6">Danh Sách Kế Hoạch Rèn Luyện</h5>
                        <small class="text-muted fs-8">Quản lý mục tiêu phân bổ nhóm cơ và kích hoạt kế hoạch tập luyện</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs"
                        data-bs-toggle="modal" data-bs-target="#manageScheduleModal"
                        onclick="setTimeout(() => document.getElementById('pills-plans-tab')?.click(), 150);">
                        <i class="bi bi-folder-plus me-1"></i>Thêm kế hoạch mới
                    </button>
                </div>

                <!-- GRID CÁC CARD KẾ HOẠCH TẬP LUYỆN (SECTION 7) -->
                <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 mb-4">
                    @foreach($keHoachList as $kh)
                    @php
                    $sessionCount = $kh->buoiTaps->count();
                    // Calculate weekly progress percentage
                    $planProgress = $sessionCount > 0 ? min(100, round(($buoiTapTuanNay / $sessionCount) * 100)) : 0;
                    @endphp
                    <div class="col">
                        <div class="plan-card-item h-100 shadow-2xs {{ $kh->is_active ? 'is-active-plan' : '' }}">
                            <!-- Top Row: Title & Action Menu -->
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                                <div class="min-w-0 flex-grow-1">
                                    <h6 class="plan-card-title mb-0 line-clamp-1" title="{{ $kh->ten_ke_hoach }}">
                                        {{ $kh->ten_ke_hoach }}
                                    </h6>
                                </div>
                                <div class="dropdown flex-shrink-0">
                                    <button class="btn btn-sm btn-light border-0 p-1 text-muted rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-8"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-1 fs-8">
                                        @if(!$kh->is_active)
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 btn-activate-plan" data-id="{{ $kh->id }}">
                                                <i class="bi bi-check-circle me-1.5 text-primary"></i>Kích hoạt kế hoạch này
                                            </button>
                                        </li>
                                        @endif
                                        @if($keHoachList->count() > 1 && !$kh->is_active)
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 text-danger btn-delete-plan" data-id="{{ $kh->id }}">
                                                <i class="bi bi-trash me-1.5"></i>Xóa kế hoạch
                                            </button>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('workout.community') }}">
                                                <i class="bi bi-share me-1.5 text-info"></i>Chia sẻ lên cộng đồng
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Description (line-clamp-2) -->
                            <p class="plan-card-desc mb-2.5 line-clamp-2">
                                {{ $kh->mo_ta ?: 'Chưa có mô tả chi tiết cho kế hoạch này. Bấm tùy chỉnh để cấu hình thêm.' }}
                            </p>

                            <!-- Progress (Section 8: Slim 6px progress bar) -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="plan-card-progress-label">Tiến độ tuần</span>
                                    <span class="plan-card-progress-val font-numeric">{{ $planProgress }}%</span>
                                </div>
                                <div class="progress workout-slim-progress">
                                    <div class="progress-bar {{ $kh->is_active ? 'bg-primary' : 'bg-secondary' }}"
                                        role="progressbar"
                                        style="width: {{ $planProgress }}%;"
                                        aria-valuenow="{{ $planProgress }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <!-- Meta row: Sessions/week and Status badge -->
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto mb-2.5">
                                <div class="plan-card-meta d-flex align-items-center gap-1.5">
                                    <i class="bi bi-calendar-week text-muted"></i>
                                    <span>{{ $sessionCount }} buổi/tuần</span>
                                </div>
                                <div>
                                    @if($kh->is_active)
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-9 fw-bold">
                                        <i class="bi bi-check2-circle me-1"></i>Đang thực hiện
                                    </span>
                                    @else
                                    <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 fs-9">
                                        Chưa kích hoạt
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Button Row -->
                            <div class="d-flex align-items-center gap-2">
                                @if(!$kh->is_active)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 btn-activate-plan" data-id="{{ $kh->id }}">
                                    <i class="bi bi-check-lg me-1"></i>Kích hoạt
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1"
                                    data-bs-toggle="modal" data-bs-target="#manageScheduleModal">
                                    <i class="bi bi-sliders me-1"></i>Quản lý buổi tập
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- DANH SÁCH BUỔI TẬP TRONG KẾ HOẠCH ĐANG ÁP DỤNG (SECTION 10) -->
                @if($activePlan)
                <div class="workout-main-card shadow-2xs">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-1.5 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="bi bi-list-task fs-6"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-7">Các buổi tập trong kế hoạch: <span class="text-primary">{{ $activePlan->ten_ke_hoach }}</span></h6>
                                <small class="text-muted fs-8">Gồm {{ $activePlan->buoiTaps->count() }} buổi phân bổ theo lịch</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-2xs"
                            data-bs-toggle="modal" data-bs-target="#manageScheduleModal">
                            <i class="bi bi-plus-lg me-1"></i>Thêm buổi tập
                        </button>
                    </div>

                    <div class="d-flex flex-column gap-2" id="sessions-detailed-list">
                        @forelse($activePlan->buoiTaps as $bt)
                        @php
                        $exCount = $bt->chiTietBuoiTaps ? $bt->chiTietBuoiTaps->count() : 0;
                        $isCurrentSession = ($currentBuoiTap && $currentBuoiTap->id === $bt->id);
                        @endphp
                        <div class="workout-session-item {{ $isCurrentSession ? 'border-primary' : '' }}">
                            <!-- Main Info -->
                            <div class="session-main-info d-flex align-items-center gap-3">
                                <div class="p-2 rounded-3 bg-light border text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                    <i class="bi bi-fire fs-6"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="session-title text-truncate">{{ $bt->ten_buoi_tap }}</span>
                                        @if(!empty($bt->ten_thu))
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-9 fw-semibold">{{ $bt->ten_thu }}</span>
                                        @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-9">Tự do</span>
                                        @endif
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-9">{{ $exCount }} bài tập</span>
                                    </div>
                                    <div class="session-meta text-truncate mt-0.5">
                                        {{ $bt->mo_ta ?: 'Buổi rèn luyện theo giáo án' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                <a href="{{ route('tap-luyen.index', ['buoi_tap_id' => $bt->id]) }}" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fs-9">
                                    <i class="bi bi-play-fill"></i><span>Tập buổi này</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger p-1 btn-delete-session" data-id="{{ $bt->id }}" title="Xóa buổi tập">
                                    <i class="bi bi-trash fs-8"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 opacity-50 d-block mb-1"></i>
                            <span class="fs-8">Kế hoạch này chưa có buổi tập nào. Hãy bấm "Thêm buổi tập" để thiết lập.</span>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif

            </div>

            <!-- ====================================================================
                     TAB 3: LỊCH SỬ HOẠT ĐỘNG
                     ==================================================================== -->
            <div class="tab-pane fade" id="tab-history" role="tabpanel" aria-labelledby="tab-history-btn">
                <div class="workout-main-card shadow-2xs">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-7">Lịch Sử Buổi Tập Gần Đây</h6>
                            <small class="text-muted fs-8">Ghi nhận các buổi tập đã hoàn thành trong hệ thống</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-8">
                            {{ count($hoatDongGanDay) }} buổi gần nhất
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @forelse($hoatDongGanDay as $act)
                        <div class="workout-session-item">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="p-2 rounded-3 bg-{{ $act['color'] }}-subtle text-{{ $act['color'] }} d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                    <i class="bi {{ $act['icon'] }} fs-6"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark fs-7 text-truncate">{{ $act['ten'] }}</div>
                                    <div class="d-flex align-items-center gap-2 fs-9 text-muted mt-0.5">
                                        <span><i class="bi bi-clock me-1"></i>{{ $act['thoi_gian'] }}</span>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-check2-circle me-1 text-success"></i>{{ $act['so_bai'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 fs-9">{{ $act['thoi_diem'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 opacity-40 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark fs-7">Chưa có lịch sử tập luyện</h6>
                            <p class="fs-8 text-muted mb-0">Hãy bắt đầu một buổi tập và bấm "Hoàn thành" để hệ thống tự động lưu lịch sử rèn luyện.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

    </div>

    </main>
    </div>

    <!-- =========================================================================
         MODAL 1: QUẢN LÝ LỊCH TẬP & KẾ HOẠCH (MANAGE SCHEDULE MODAL)
         ========================================================================= -->
    <div class="modal fade" id="manageScheduleModal" tabindex="-1" aria-labelledby="manageScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark fs-6" id="manageScheduleModalLabel">
                        <i class="bi bi-calendar3 me-2 text-primary"></i>Quản lý Lịch tập & Kế hoạch
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <ul class="nav nav-pills mb-3 border-bottom pb-2 gap-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-semibold px-3 py-1.5 fs-8 text-primary" id="pills-presets-tab" data-bs-toggle="pill" data-bs-target="#pills-presets" type="button" role="tab">
                                <i class="bi bi-stars me-1 text-warning"></i> Lịch tập mẫu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-semibold px-3 py-1.5 fs-8" id="pills-sessions-tab" data-bs-toggle="pill" data-bs-target="#pills-sessions" type="button" role="tab">
                                <i class="bi bi-card-checklist me-1"></i> Các buổi tập trong tuần
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-semibold px-3 py-1.5 fs-8" id="pills-plans-tab" data-bs-toggle="pill" data-bs-target="#pills-plans" type="button" role="tab">
                                <i class="bi bi-layers-half me-1"></i> Danh sách Kế hoạch
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- TAB 1: LỊCH TẬP MẪU CHUẨN KHOA HỌC (PRESETS) -->
                        <div class="tab-pane fade show active" id="pills-presets" role="tabpanel">
                            <!-- BƯỚC 1 & 2: BỘ LỌC VÀ DANH SÁCH LỊCH TẬP MẪU -->
                            <div id="presetStepSelection">
                                <div class="rounded-4 p-3 mb-3 bg-primary-subtle border border-primary-subtle">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill fw-bold fs-9"><i class="bi bi-stars"></i> Gợi ý khoa học</span>
                                        <h6 class="fw-bold text-dark mb-0 fs-7">Lịch tập mẫu & Tự động lên lịch</h6>
                                    </div>
                                    <p class="text-muted fs-8 mb-0">Chọn số buổi tập, thời lượng và cường độ mong muốn. Hệ thống sẽ gợi ý giáo án phù hợp và cho phép bạn đặt giờ tập của từng buổi để tự động thêm vào <strong>Trang Lịch (Calendar)</strong>.</p>
                                </div>

                                <!-- BỘ LỌC 3 TIÊU CHÍ (SỐ BUỔI, THỜI GIAN, CƯỜNG ĐỘ) -->
                                <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                    <div class="row g-2.5">
                                        <!-- 1. Số buổi tập -->
                                        <div class="col-12 col-sm-4">
                                            <label class="form-label fs-8 fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar-week text-primary"></i> Số buổi tập / tuần
                                            </label>
                                            <select class="form-select form-select-sm rounded-3 shadow-2xs" id="filterPresetDays">
                                                <option value="all">Tất cả số buổi</option>
                                                <option value="3" selected>3 buổi / tuần (Phổ biến)</option>
                                                <option value="4">4 buổi / tuần (Tối ưu tăng cơ)</option>
                                                <option value="5">5 buổi / tuần (Chuyên sâu)</option>
                                                <option value="6">6 buổi / tuần (Cường độ cao)</option>
                                            </select>
                                        </div>

                                        <!-- 2. Thời gian tập -->
                                        <div class="col-6 col-sm-4">
                                            <label class="form-label fs-8 fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                                                <i class="bi bi-stopwatch text-primary"></i> Thời gian mỗi buổi
                                            </label>
                                            <select class="form-select form-select-sm rounded-3 shadow-2xs" id="filterPresetDuration">
                                                <option value="all">Tất cả thời lượng</option>
                                                <option value="45">45 phút (Nhanh gọn)</option>
                                                <option value="60" selected>60 phút (Tiêu chuẩn)</option>
                                                <option value="90">90 phút (Chuyên sâu)</option>
                                            </select>
                                        </div>

                                        <!-- 3. Cường độ luyện tập -->
                                        <div class="col-6 col-sm-4">
                                            <label class="form-label fs-8 fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                                                <i class="bi bi-speedometer2 text-primary"></i> Cường độ luyện tập
                                            </label>
                                            <select class="form-select form-select-sm rounded-3 shadow-2xs" id="filterPresetIntensity">
                                                <option value="all">Tất cả cường độ</option>
                                                <option value="nhe">Nhẹ / Người mới</option>
                                                <option value="trung_binh" selected>Trung bình / Tăng cơ</option>
                                                <option value="cao">Cao / Sức mạnh tối đa</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- THÔNG BÁO SỐ LƯỢNG KẾT QUẢ -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="fs-8 fw-bold text-dark d-flex align-items-center gap-1">
                                        <i class="bi bi-list-check text-primary"></i>
                                        <span id="presetResultCountText">Các lịch tập mẫu phù hợp:</span>
                                    </span>
                                    <span class="badge bg-light text-muted border rounded-pill fs-9" id="presetActiveFilterBadge">Đang lọc</span>
                                </div>

                                <!-- CONTAINER RENDER DANH SÁCH LỊCH MẪU -->
                                <div class="d-flex flex-column gap-2.5 mb-2" id="presetCardsContainer">
                                    <!-- Rendered dynamically via JS -->
                                </div>
                            </div>

                            <!-- BƯỚC 3: MÀN HÌNH CÀI ĐẶT GIỜ TẬP TỪNG BUỔI VÀ ĐỒNG BỘ LỊCH -->
                            <div id="presetStepConfig" class="d-none">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fs-8 d-inline-flex align-items-center gap-1" id="btnBackToPresetList">
                                        <i class="bi bi-arrow-left"></i> <span>Chọn lịch mẫu khác</span>
                                    </button>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-8 fw-semibold" id="cfgPresetBadge"></span>
                                </div>

                                <!-- Card thông tin lịch -->
                                <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                    <h6 class="fw-bold text-dark mb-1 fs-7" id="cfgPresetTitle">Tên lịch</h6>
                                    <p class="text-muted fs-8 mb-2.5" id="cfgPresetDesc">Mô tả chi tiết</p>

                                    <div>
                                        <label class="form-label fs-8 fw-semibold text-muted mb-1">Tên kế hoạch của bạn</label>
                                        <input type="text" class="form-control form-control-sm rounded-3 bg-white" id="cfgPlanCustomName" placeholder="Tên kế hoạch">
                                    </div>
                                </div>

                                <!-- Danh sách các buổi tập: Nhập Time bắt đầu và Time kết thúc -->
                                <div class="card border rounded-4 p-3 mb-3 shadow-2xs">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-7 d-flex align-items-center gap-1.5">
                                            <i class="bi bi-clock-history text-primary"></i> <span>Cài đặt giờ tập cho từng buổi</span>
                                        </h6>
                                        <small class="text-primary fw-semibold fs-9">Lặp lại hàng tuần</small>
                                    </div>
                                    <p class="text-muted fs-8 mb-3">Vui lòng điều chỉnh thứ trong tuần, giờ bắt đầu và giờ kết thúc cho từng buổi tập để hệ thống tự động lên lịch chính xác vào trang <strong>Lịch (Calendar)</strong>.</p>

                                    <div class="d-flex flex-column gap-2" id="cfgSessionsListContainer">
                                        <!-- Render các buổi tập ở đây -->
                                    </div>
                                </div>

                                <!-- Tùy chọn đồng bộ Lịch Calendar -->
                                <div class="card border-0 bg-primary-subtle rounded-4 p-3 mb-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="cfgSyncCalendar" checked>
                                        <label class="form-check-label fw-bold text-dark fs-8" for="cfgSyncCalendar">
                                            <i class="bi bi-calendar2-check-fill text-primary me-1"></i> Tự động add lịch tập vào trang Lịch (Calendar)
                                        </label>
                                    </div>

                                    <div class="row g-2 align-items-center" id="cfgCalendarOptionsWrapper">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-label fs-9 text-muted mb-1 fw-semibold">Thời gian duy trì chuỗi lịch lặp</label>
                                            <select class="form-select form-select-sm rounded-3 bg-white" id="cfgSyncWeeks">
                                                <option value="4" selected>4 tuần (1 tháng)</option>
                                                <option value="8">8 tuần (2 tháng)</option>
                                                <option value="12">12 tuần (3 tháng)</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-check mt-3 mt-sm-3.5">
                                                <input class="form-check-input" type="checkbox" id="cfgEnableNotif" checked>
                                                <label class="form-check-label fs-8 text-dark fw-medium" for="cfgEnableNotif">
                                                    Nhắc nhở qua email / thông báo
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút xác nhận áp dụng -->
                                <div class="d-flex align-items-center justify-content-end gap-2 pt-1">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3.5" id="btnCancelPresetConfig">Hủy</button>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" id="btnApplyPresetPlan">
                                        <i class="bi bi-calendar-plus-fill"></i>
                                        <span>Tạo kế hoạch & Thêm vào lịch</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: CÁC BUỔI TẬP TRONG TUẦN -->
                        <div class="tab-pane fade" id="pills-sessions" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark fs-7">Kế hoạch hiện tại: <span class="text-primary">{{ $activePlan->ten_ke_hoach ?? 'Chưa đặt tên' }}</span></h6>
                                    <small class="text-muted fs-8">Gán thứ trong tuần (1=Thứ 2 ... 7=Chủ Nhật). Ngày trống sẽ tự tính là Ngày Nghỉ.</small>
                                </div>
                            </div>

                            <!-- Form Thêm buổi tập mới -->
                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                <h6 class="fw-bold text-dark mb-2 fs-8"><i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm buổi tập mới vào kế hoạch này</h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-sm-5">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Tên buổi tập</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="new-session-name" placeholder="VD: Upper Body, Full Body...">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Ngày tập trong tuần</label>
                                        <select class="form-select form-select-sm rounded-3" id="new-session-day">
                                            <option value="">Không cố định</option>
                                            <option value="1">Thứ 2</option>
                                            <option value="2">Thứ 3</option>
                                            <option value="3">Thứ 4</option>
                                            <option value="4">Thứ 5</option>
                                            <option value="5">Thứ 6</option>
                                            <option value="6">Thứ 7</option>
                                            <option value="7">Chủ Nhật</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold d-flex align-items-center justify-content-center gap-1" id="btn-add-session">
                                            <i class="bi bi-plus-lg"></i>
                                            <span>Thêm buổi</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng danh sách buổi tập -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="fs-8 text-muted">
                                            <th>Buổi tập</th>
                                            <th>Ngày trong tuần</th>
                                            <th>Số bài tập</th>
                                            <th class="text-end">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sessions-table-body">
                                        @forelse($buoiTapList as $bt)
                                        <tr data-session-id="{{ $bt['id'] }}">
                                            <td>
                                                <div class="fw-semibold text-dark fs-7">{{ $bt['title'] }}</div>
                                                <small class="text-muted fs-8">{{ $bt['mo_ta'] ?: 'Không có mô tả' }}</small>
                                            </td>
                                            <td>
                                                @if(!empty($bt['ten_thu']))
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-9">{{ $bt['ten_thu'] }}</span>
                                                @else
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-9">Tùy chọn</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5 fs-9">{{ count($bt['exercises']) }} bài</span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill text-danger btn-delete-session" data-id="{{ $bt['id'] }}" title="Xóa buổi tập">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3 fs-8">Chưa có buổi tập nào trong kế hoạch này.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: QUẢN LÝ CÁC KẾ HOẠCH -->
                        <div class="tab-pane fade" id="pills-plans" role="tabpanel">
                            <!-- Action Header: Title & Nút Cộng đồng Gymer -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-7">
                                        <i class="bi bi-layers-half text-primary me-1"></i>Danh sách Kế hoạch rèn luyện
                                    </h6>
                                    <small class="text-muted fs-8">Chọn kế hoạch áp dụng hoặc kết nối cùng cộng đồng gymer</small>
                                </div>
                                <a href="{{ route('workout.community') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fs-8 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs text-decoration-none">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Cộng đồng Gymer</span>
                                </a>
                            </div>

                            <!-- Gợi ý chọn từ Lịch tập mẫu chuẩn -->
                            <div class="d-flex align-items-center justify-content-between p-2.5 px-3 mb-3 rounded-4 bg-primary-subtle border border-primary-subtle flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-stars text-warning fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark fs-8">Chưa biết cách chia buổi tập tối ưu?</div>
                                        <div class="text-muted fs-9">Chọn Lịch tập mẫu theo mục tiêu & số buổi, tự động add vào Calendar</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-8 fw-semibold shadow-2xs" onclick="document.getElementById('pills-presets-tab')?.click();">
                                    <span>Khám phá Lịch mẫu</span> &rarr;
                                </button>
                            </div>

                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                <h6 class="fw-bold text-dark mb-2 fs-8"><i class="bi bi-folder-plus text-primary me-1"></i>Tạo kế hoạch tập luyện mới</h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Tên kế hoạch</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="new-plan-name" placeholder="VD: Upper/Lower Split, Full Body...">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Mô tả (tùy chọn)</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="new-plan-desc" placeholder="VD: Tập 4 buổi mỗi tuần">
                                    </div>
                                    <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <a href="{{ route('workout.community') }}" class="text-primary fs-8 fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-globe-americas"></i> Khám phá giáo án Cộng đồng Gymer &rarr;
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" id="btn-create-plan">
                                            <i class="bi bi-plus-lg me-1"></i>Tạo kế hoạch
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2" id="plans-list-container">
                                @foreach($keHoachList as $kh)
                                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 rounded-3 border bg-white {{ $kh->is_active ? 'border-primary shadow-2xs' : '' }}">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold text-dark fs-7">{{ $kh->ten_ke_hoach }}</span>
                                            @if($kh->is_active)
                                            <span class="badge bg-primary-subtle text-primary rounded-pill fs-9 fw-bold">Đang áp dụng</span>
                                            @endif
                                        </div>
                                        <small class="text-muted fs-8">{{ $kh->mo_ta ?: 'Không có mô tả' }} &bull; {{ $kh->buoiTaps->count() }} buổi tập</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if(!$kh->is_active)
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fs-9 btn-activate-plan" data-id="{{ $kh->id }}">
                                            Kích hoạt
                                        </button>
                                        @if($keHoachList->count() > 1)
                                        <button type="button" class="btn btn-sm btn-light border text-danger rounded-circle btn-delete-plan p-1" data-id="{{ $kh->id }}" title="Xóa kế hoạch">
                                            <i class="bi bi-trash fs-8"></i>
                                        </button>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" data-bs-dismiss="modal" onclick="window.location.reload();">Đóng & Tải lại</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         MODAL 2: CHỈNH SỬA / THÊM BÀI TẬP (EDIT WORKOUT MODAL)
         ========================================================================= -->
    <div class="modal fade" id="editWorkoutModal" tabindex="-1" aria-labelledby="editWorkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark fs-6" id="editWorkoutModalLabel">
                        <i class="bi bi-sliders me-2 text-primary"></i>Tùy chỉnh buổi tập & bài tập
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <input type="hidden" id="modal-buoi-tap-id" value="{{ $currentBuoiTap ? $currentBuoiTap->id : '' }}">

                    <!-- Chọn hoặc nhập tên buổi tập -->
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold fs-8 text-muted mb-1">Tên buổi tập</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="modal-workout-title" placeholder="VD: Push, Upper Body, Chân...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold fs-8 text-muted mb-1">Ngày trong tuần</label>
                            <select class="form-select form-select-sm rounded-3" id="modal-workout-day">
                                <option value="">Không cố định</option>
                                <option value="1">Thứ 2</option>
                                <option value="2">Thứ 3</option>
                                <option value="3">Thứ 4</option>
                                <option value="4">Thứ 5</option>
                                <option value="5">Thứ 6</option>
                                <option value="6">Thứ 7</option>
                                <option value="7">Chủ Nhật</option>
                            </select>
                        </div>
                    </div>

                    <!-- Thêm bài tập mới đa dạng loại bài -->
                    <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0 fs-8">
                                <i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm bài tập vào buổi này
                            </h6>
                            <span class="text-muted fs-9">Hỗ trợ Sets/Reps, Thời gian & Cardio</span>
                        </div>

                        <!-- 1. Hàng chọn Loại bài tập và Nhập tên bài tập -->
                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-5">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Loại bài tập</label>
                                <select class="form-select form-select-sm rounded-3 fw-semibold" id="new-ex-type">
                                    <option value="strength" selected>🏋️ Strength (Kháng lực / Tạ)</option>
                                    <option value="core">🧘 Core (Bụng / Thân giữa)</option>
                                    <option value="cardio">🏃 Cardio (Đi bộ / Chạy / Đạp xe)</option>
                                    <option value="other">⚡ Other (Khác / Tùy chọn)</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-7">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Tên bài tập</label>
                                <input type="text" class="form-control form-control-sm rounded-3" id="new-ex-name" placeholder="VD: Bench Press, Plank, Đi bộ...">
                            </div>
                        </div>

                        <!-- 2. Tùy chọn cách đo lường (Chỉ hiện khi chọn Core hoặc Other) -->
                        <div class="mb-2 p-2 bg-white rounded-3 border d-none" id="core-measure-selector">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-8 fw-semibold text-muted">Cách đo:</span>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="measure-mode" id="mode-reps" value="reps" checked>
                                    <label class="form-check-label fs-8 fw-semibold cursor-pointer" for="mode-reps">Sets / Reps</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="measure-mode" id="mode-duration" value="duration">
                                    <label class="form-check-label fs-8 fw-semibold cursor-pointer" for="mode-duration">Thời gian</label>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Khu vực nhập thông số động -->
                        <div class="row g-2 align-items-end">
                            <div class="col-6 col-sm-4" id="field-group-sets">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Số sets</label>
                                <input type="number" class="form-control form-control-sm rounded-3" id="new-ex-sets" placeholder="VD: 3" min="1" max="20" value="3">
                            </div>

                            <div class="col-6 col-sm-5" id="field-group-reps">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Reps</label>
                                <input type="text" class="form-control form-control-sm rounded-3" id="new-ex-reps" placeholder="VD: 8-10 hoặc 10-12" value="8-12">
                            </div>

                            <div class="col-7 col-sm-5 d-none" id="field-group-duration">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Thời lượng</label>
                                <input type="number" class="form-control form-control-sm rounded-3" id="new-ex-duration" placeholder="VD: 30" min="1" max="600">
                            </div>

                            <div class="col-5 col-sm-4 d-none" id="field-group-unit">
                                <label class="form-label fw-semibold fs-8 text-muted mb-1">Đơn vị</label>
                                <select class="form-select form-select-sm rounded-3" id="new-ex-unit">
                                    <option value="phut" selected>Phút</option>
                                    <option value="giay">Giây</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-3 ms-auto">
                                <button type="button" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold d-flex align-items-center justify-content-center gap-1" id="add-exercise-btn">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Thêm</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách bài tập trong buổi -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 fs-8">Danh sách bài tập hiện tại</h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill fs-9" id="exercise-count-badge">0 bài tập</span>
                        </div>

                        <div class="d-flex flex-column gap-2" id="modal-exercise-list" style="max-height: 260px; overflow-y: auto;">
                            <!-- Render tự động bởi JS -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 d-inline-flex align-items-center gap-1.5" id="save-workout-btn">
                        <span class="spinner-border spinner-border-sm d-none" id="save-workout-spinner" role="status" aria-hidden="true"></span>
                        <i class="bi bi-check-lg" id="save-workout-icon"></i>
                        <span>Lưu & Áp dụng bài tập</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dữ liệu ban đầu từ Backend truyền sang JS -->
    <script>
        window.FITNESS_CONFIG = {
            currentMonth: {
                {
                    $currentMonth
                }
            },
            currentYear: {
                {
                    $currentYear
                }
            },
            currentType: "{{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : ($dinhHuongHomNay['ten'] ?? 'Tập tự do') }}",
            currentBuoiTapId: {
                {
                    $currentBuoiTap ? $currentBuoiTap - > id : 'null'
                }
            },
            activePlanId: {
                {
                    $activePlan ? $activePlan - > id : 'null'
                }
            },
            danhSachBaiTap: @json($danhSachBaiTap),
            buoiTapList: @json($buoiTapList ?? []),
            workoutPlansByType: @json($workoutPlansByType ?? []),
            lichTuan: @json($lichTuan ?? []),
            monthlyHeatmap: @json($monthlyHeatmap),
            completeRoute: "{{ route('tap-luyen.hoan-thanh') }}",
            updateWorkoutRoute: "{{ route('tap-luyen.cap-nhat-buoi-tap') }}",
            createPlanRoute: "{{ route('tap-luyen.ke-hoach.store') }}",
            activatePlanUrl: "{{ url('/tap-luyen/ke-hoach') }}",
            deletePlanUrl: "{{ url('/tap-luyen/ke-hoach') }}",
            createSessionRoute: "{{ route('tap-luyen.buoi-tap.store') }}",
            deleteSessionUrl: "{{ url('/tap-luyen/buoi-tap') }}"
        };
    </script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Chức năng Thể Chất / Tập Luyện -->
    <script src="{{ asset('js/tap-luyen.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnQuickToggleNotif = document.getElementById('btnQuickToggleNotif');
            if (btnQuickToggleNotif) {
                btnQuickToggleNotif.addEventListener('change', function() {
                    const isEnabled = this.checked;
                    const mainBell = document.getElementById('mainNotifBellIcon');
                    const badgeDot = document.getElementById('notifBadgeDot');
                    const headerIcon = document.getElementById('notifHeaderIcon');

                    if (mainBell) mainBell.className = isEnabled ? 'bi bi-bell-fill' : 'bi bi-bell-slash-fill text-muted';
                    if (badgeDot) badgeDot.classList.toggle('d-none', !isEnabled);
                    if (headerIcon) headerIcon.className = isEnabled ? 'bi bi-bell-fill text-primary' : 'bi bi-bell-slash-fill text-muted';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch("{{ route('profile.notifications.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                enabled: isEnabled
                            })
                        }).catch(err => console.log('Lỗi cập nhật thông báo:', err));
                    }
                });
            }

            // Đồng bộ tab giữa desktop và mobile (trong menu Tùy chọn)
            const allWorkoutTabPills = document.querySelectorAll('[data-bs-toggle="pill"][data-bs-target^="#tab-"]');
            allWorkoutTabPills.forEach(pill => {
                pill.addEventListener('shown.bs.tab', function(e) {
                    const targetSelector = e.target.getAttribute('data-bs-target');
                    if (!targetSelector) return;

                    allWorkoutTabPills.forEach(otherPill => {
                        const isMatch = otherPill.getAttribute('data-bs-target') === targetSelector;
                        otherPill.classList.toggle('active', isMatch);
                        otherPill.setAttribute('aria-selected', isMatch ? 'true' : 'false');
                    });

                    // Nếu ở mobile và đang mở collapse tùy chọn, tự đóng lại khi người dùng chọn tab
                    if (window.innerWidth < 768) {
                        const collapseEl = document.getElementById('workoutHeaderActionsCollapse');
                        if (collapseEl && collapseEl.classList.contains('show')) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                            if (bsCollapse) {
                                bsCollapse.hide();
                            }
                        }
                    }
                });
            });

            // =========================================================================
            // LỊCH TẬP MẪU CHUẨN KHOA HỌC & ĐỒNG BỘ TRANG LỊCH (PRESET ROUTINES)
            // =========================================================================
            const WORKOUT_PRESETS = [{
                    id: 'ppl_3',
                    title: 'Push - Pull - Legs (PPL Cơ bản)',
                    days: 3,
                    duration: 60,
                    intensity: 'trung_binh',
                    intensityLabel: 'Trung bình',
                    badge: 'Phổ biến nhất',
                    badgeClass: 'bg-primary-subtle text-primary border border-primary-subtle',
                    desc: 'Phân bổ 3 buổi kinh điển: Đẩy (Ngực, Vai, Tay sau) - Kéo (Lưng, Tay trước) - Chân & Bụng. Tối ưu thời gian phục hồi cơ bắp.',
                    sessions: [{
                            name: 'Push (Ngực, Vai, Tay sau)',
                            day: 1,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Barbell Bench Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực trên',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Dumbbell Lateral Raise',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 4,
                                    reps: '12-15'
                                },
                                {
                                    name: 'Triceps Rope Pushdown',
                                    type: 'strength',
                                    group: 'Tay sau',
                                    sets: 3,
                                    reps: '12-15'
                                }
                            ]
                        },
                        {
                            name: 'Pull (Lưng, Xô, Tay trước)',
                            day: 3,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Lat Pulldown',
                                    type: 'strength',
                                    group: 'Lưng xô',
                                    sets: 4,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Seated Cable Row',
                                    type: 'strength',
                                    group: 'Lưng giữa',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Face Pull',
                                    type: 'strength',
                                    group: 'Vai sau',
                                    sets: 3,
                                    reps: '15'
                                },
                                {
                                    name: 'Barbell Bicep Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '10-12'
                                }
                            ]
                        },
                        {
                            name: 'Legs & Core (Đùi, Mông, Bụng)',
                            day: 5,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Barbell Back Squat',
                                    type: 'strength',
                                    group: 'Đùi trước',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Romanian Deadlift',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Standing Calf Raise',
                                    type: 'strength',
                                    group: 'Bắp chân',
                                    sets: 4,
                                    reps: '15'
                                },
                                {
                                    name: 'Plank Core',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    duration: 60,
                                    duration_unit: 'giay'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'fullbody_3',
                    title: 'Full Body 3 Ngày (Toàn thân cân đối)',
                    days: 3,
                    duration: 45,
                    intensity: 'nhe',
                    intensityLabel: 'Nhẹ / Người mới',
                    badge: 'Người bận rộn',
                    badgeClass: 'bg-success-subtle text-success border border-success-subtle',
                    desc: 'Kích hoạt toàn bộ nhóm cơ trong mỗi buổi tập với thời lượng ngắn gọn 45 phút. Lựa chọn số 1 cho người mới hoặc người có quỹ thời gian ít.',
                    sessions: [{
                            name: 'Full Body A (Sức mạnh nền tảng)',
                            day: 1,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Barbell Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Dumbbell Flat Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Dumbbell Bent Over Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Plank',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    duration: 45,
                                    duration_unit: 'giay'
                                }
                            ]
                        },
                        {
                            name: 'Full Body B (Hypertrophy & Cân bằng)',
                            day: 3,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Romanian Deadlift',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực trên',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Lat Pulldown',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Standing Dumbbell Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        },
                        {
                            name: 'Full Body C (Sức bền & Thể lực)',
                            day: 5,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Leg Press',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '12'
                                },
                                {
                                    name: 'Overhead Dumbbell Press',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Cable Chest Fly',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 3,
                                    reps: '12-15'
                                },
                                {
                                    name: 'Hanging Knee Raise',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'upper_lower_4',
                    title: 'Upper / Lower (Thân Trên / Thân Dưới)',
                    days: 4,
                    duration: 60,
                    intensity: 'trung_binh',
                    intensityLabel: 'Trung bình - Nâng cao',
                    badge: 'Khoa học tối ưu',
                    badgeClass: 'bg-primary-subtle text-primary border border-primary-subtle',
                    desc: 'Lịch tập chuẩn khoa học giúp tăng cơ tối ưu nhất hiện nay. Mỗi nhóm cơ được kích thích 2 lần mỗi tuần với 3 ngày nghỉ ngơi phục hồi.',
                    sessions: [{
                            name: 'Upper A (Thân trên Sức mạnh)',
                            day: 1,
                            startTime: '18:00',
                            endTime: '19:00',
                            exercises: [{
                                    name: 'Flat Barbell Bench Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 4,
                                    reps: '6-8'
                                },
                                {
                                    name: 'Barbell Bent Over Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 4,
                                    reps: '6-8'
                                },
                                {
                                    name: 'Overhead Shoulder Press',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 3,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Barbell Bicep Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '10-12'
                                }
                            ]
                        },
                        {
                            name: 'Lower A (Thân dưới Sức mạnh)',
                            day: 2,
                            startTime: '18:00',
                            endTime: '19:00',
                            exercises: [{
                                    name: 'Barbell Back Squat',
                                    type: 'strength',
                                    group: 'Đùi trước',
                                    sets: 4,
                                    reps: '6-8'
                                },
                                {
                                    name: 'Romanian Deadlift',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Bulgarian Split Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Standing Calf Raise',
                                    type: 'strength',
                                    group: 'Bắp chân',
                                    sets: 4,
                                    reps: '12-15'
                                }
                            ]
                        },
                        {
                            name: 'Upper B (Thân trên Phì đại)',
                            day: 4,
                            startTime: '18:00',
                            endTime: '19:00',
                            exercises: [{
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực trên',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Lat Pulldown',
                                    type: 'strength',
                                    group: 'Lưng xô',
                                    sets: 4,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Dumbbell Lateral Raise',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 4,
                                    reps: '12-15'
                                },
                                {
                                    name: 'Triceps Overhead Extension',
                                    type: 'strength',
                                    group: 'Tay sau',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        },
                        {
                            name: 'Lower B (Thân dưới Phì đại & Core)',
                            day: 5,
                            startTime: '18:00',
                            endTime: '19:00',
                            exercises: [{
                                    name: 'Leg Press',
                                    type: 'strength',
                                    group: 'Đùi trước',
                                    sets: 4,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Lying Leg Curl',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 4,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Seated Calf Raise',
                                    type: 'strength',
                                    group: 'Bắp chân',
                                    sets: 4,
                                    reps: '15'
                                },
                                {
                                    name: 'Cable Woodchopper',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    reps: '15'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'push_pull_4',
                    title: 'Push / Pull 4 Ngày (Đẩy & Kéo Cân Bằng)',
                    days: 4,
                    duration: 45,
                    intensity: 'nhe',
                    intensityLabel: 'Nhẹ nhàng - Cân bằng',
                    badge: 'Cân bằng',
                    badgeClass: 'bg-info-subtle text-info border border-info-subtle',
                    desc: 'Phân bổ vận động đẩy và kéo giúp cơ thể phát triển đồng đều, giảm áp lực lên các khớp cổ tay và khuỷu tay.',
                    sessions: [{
                            name: 'Push A (Ngực & Đùi trước)',
                            day: 1,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Dumbbell Flat Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Goblet Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Dumbbell Shoulder Press',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Cable Tricep Pushdown',
                                    type: 'strength',
                                    group: 'Tay sau',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        },
                        {
                            name: 'Pull A (Lưng & Đùi sau)',
                            day: 2,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Lat Pulldown',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Dumbbell Romanian Deadlift',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Face Pull',
                                    type: 'strength',
                                    group: 'Vai sau',
                                    sets: 3,
                                    reps: '15'
                                },
                                {
                                    name: 'Dumbbell Hammer Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        },
                        {
                            name: 'Push B (Ngực trên & Vai giữa)',
                            day: 4,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực trên',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Leg Extension',
                                    type: 'strength',
                                    group: 'Đùi trước',
                                    sets: 3,
                                    reps: '12'
                                },
                                {
                                    name: 'Lateral Raise',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 3,
                                    reps: '15'
                                },
                                {
                                    name: 'Plank',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    duration: 45,
                                    duration_unit: 'giay'
                                }
                            ]
                        },
                        {
                            name: 'Pull B (Lưng giữa & Cánh tay)',
                            day: 5,
                            startTime: '17:30',
                            endTime: '18:15',
                            exercises: [{
                                    name: 'Seated Cable Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Lying Leg Curl',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '12'
                                },
                                {
                                    name: 'Preacher Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Hanging Knee Raise',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'bro_split_5',
                    title: 'Bro Split 5 Ngày (Tập Riêng Từng Nhóm Cơ)',
                    days: 5,
                    duration: 60,
                    intensity: 'trung_binh',
                    intensityLabel: 'Trung bình - Cao',
                    badge: 'Classic Bodybuilding',
                    badgeClass: 'bg-warning-subtle text-warning border border-warning-subtle',
                    desc: 'Phương pháp thể hình truyền thống tập trung tối đa cho 1 nhóm cơ mỗi ngày để bơm máu (pump) tối đa.',
                    sessions: [{
                            name: 'Chest Day (Ngực & Bụng)',
                            day: 1,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Flat Bench Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Cable Chest Fly',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 3,
                                    reps: '12-15'
                                },
                                {
                                    name: 'Cable Crunch',
                                    type: 'core',
                                    group: 'Bụng',
                                    sets: 3,
                                    reps: '15'
                                }
                            ]
                        },
                        {
                            name: 'Back Day (Lưng & Xô)',
                            day: 2,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Deadlift',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 4,
                                    reps: '6-8'
                                },
                                {
                                    name: 'Lat Pulldown',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Barbell Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '10'
                                }
                            ]
                        },
                        {
                            name: 'Shoulders Day (Vai & Cầu vai)',
                            day: 3,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Overhead Press',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Lateral Raise',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 4,
                                    reps: '12-15'
                                },
                                {
                                    name: 'Face Pull',
                                    type: 'strength',
                                    group: 'Vai sau',
                                    sets: 3,
                                    reps: '15'
                                }
                            ]
                        },
                        {
                            name: 'Arms Day (Tay trước & Tay sau)',
                            day: 4,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Barbell Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Hammer Curl',
                                    type: 'strength',
                                    group: 'Tay trước',
                                    sets: 3,
                                    reps: '12'
                                },
                                {
                                    name: 'Skull Crusher',
                                    type: 'strength',
                                    group: 'Tay sau',
                                    sets: 3,
                                    reps: '10'
                                }
                            ]
                        },
                        {
                            name: 'Leg Day (Đùi, Mông, Bắp chân)',
                            day: 5,
                            startTime: '17:30',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Barbell Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Leg Press',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Calf Raise',
                                    type: 'strength',
                                    group: 'Bắp chân',
                                    sets: 4,
                                    reps: '15'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'hybrid_5',
                    title: 'Upper / Lower / PPL (Hybrid 5 Buổi)',
                    days: 5,
                    duration: 90,
                    intensity: 'cao',
                    intensityLabel: 'Cường độ cao',
                    badge: 'Chuyên sâu',
                    badgeClass: 'bg-danger-subtle text-danger border border-danger-subtle',
                    desc: 'Kết hợp tối thượng: 2 ngày đầu tuần tập Sức mạnh (Power), 3 ngày cuối tuần tập Phì đại (Hypertrophy).',
                    sessions: [{
                            name: 'Upper Power',
                            day: 1,
                            startTime: '17:00',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Flat Bench Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 4,
                                    reps: '5'
                                },
                                {
                                    name: 'Pendlay Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 4,
                                    reps: '5'
                                },
                                {
                                    name: 'Military Press',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 3,
                                    reps: '6'
                                }
                            ]
                        },
                        {
                            name: 'Lower Power',
                            day: 2,
                            startTime: '17:00',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Back Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 4,
                                    reps: '5'
                                },
                                {
                                    name: 'Romanian Deadlift',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '6'
                                }
                            ]
                        },
                        {
                            name: 'Push Hypertrophy',
                            day: 4,
                            startTime: '17:00',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Incline Dumbbell Press',
                                    type: 'strength',
                                    group: 'Ngực',
                                    sets: 4,
                                    reps: '10-12'
                                },
                                {
                                    name: 'Lateral Raise',
                                    type: 'strength',
                                    group: 'Vai',
                                    sets: 4,
                                    reps: '15'
                                }
                            ]
                        },
                        {
                            name: 'Pull Hypertrophy',
                            day: 5,
                            startTime: '17:00',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Pull Up',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 4,
                                    reps: '8-10'
                                },
                                {
                                    name: 'Seated Cable Row',
                                    type: 'strength',
                                    group: 'Lưng',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        },
                        {
                            name: 'Legs & Core',
                            day: 6,
                            startTime: '17:00',
                            endTime: '18:30',
                            exercises: [{
                                    name: 'Front Squat',
                                    type: 'strength',
                                    group: 'Chân',
                                    sets: 3,
                                    reps: '10'
                                },
                                {
                                    name: 'Lying Leg Curl',
                                    type: 'strength',
                                    group: 'Đùi sau',
                                    sets: 3,
                                    reps: '12'
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'ppl_x2_6',
                    title: 'Push - Pull - Legs x2 (PPL 6 Buổi Tối Đa)',
                    days: 6,
                    duration: 90,
                    intensity: 'cao',
                    intensityLabel: 'Cường độ cao nhất',
                    badge: 'Hardcore Gymer',
                    badgeClass: 'bg-danger-subtle text-danger border border-danger-subtle',
                    desc: 'Lịch tập 6 buổi mỗi tuần cho vận động viên và gymer lâu năm muốn đạt tối đa khối lượng cơ bắp.',
                    sessions: [{
                            name: 'Push A (Ngực trọng tâm)',
                            day: 1,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Barbell Bench Press',
                                type: 'strength',
                                group: 'Ngực',
                                sets: 4,
                                reps: '8'
                            }]
                        },
                        {
                            name: 'Pull A (Lưng trọng tâm)',
                            day: 2,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Deadlift',
                                type: 'strength',
                                group: 'Lưng',
                                sets: 4,
                                reps: '6'
                            }]
                        },
                        {
                            name: 'Legs A (Đùi trước trọng tâm)',
                            day: 3,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Barbell Squat',
                                type: 'strength',
                                group: 'Chân',
                                sets: 4,
                                reps: '8'
                            }]
                        },
                        {
                            name: 'Push B (Vai trọng tâm)',
                            day: 4,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Overhead Press',
                                type: 'strength',
                                group: 'Vai',
                                sets: 4,
                                reps: '8'
                            }]
                        },
                        {
                            name: 'Pull B (Độ dày lưng)',
                            day: 5,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Barbell Row',
                                type: 'strength',
                                group: 'Lưng',
                                sets: 4,
                                reps: '8'
                            }]
                        },
                        {
                            name: 'Legs B (Đùi sau & Mông)',
                            day: 6,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Romanian Deadlift',
                                type: 'strength',
                                group: 'Đùi sau',
                                sets: 4,
                                reps: '8'
                            }]
                        }
                    ]
                },
                {
                    id: 'arnold_6',
                    title: 'Arnold Split (Lịch Tập Huyền Thoại 6 Buổi)',
                    days: 6,
                    duration: 90,
                    intensity: 'cao',
                    intensityLabel: 'Cường độ cao nhất',
                    badge: 'Arnold Classic',
                    badgeClass: 'bg-dark text-white',
                    desc: 'Lịch tập đối kháng kinh điển Ngực - Lưng, Vai - Tay, Chân giúp tạo nên vóc dáng chữ V huyền thoại.',
                    sessions: [{
                            name: 'Ngực & Lưng A',
                            day: 1,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Barbell Bench Press',
                                type: 'strength',
                                group: 'Ngực',
                                sets: 4,
                                reps: '8-10'
                            }, {
                                name: 'Wide Grip Pull Up',
                                type: 'strength',
                                group: 'Lưng',
                                sets: 4,
                                reps: '8-10'
                            }]
                        },
                        {
                            name: 'Vai & Tay A',
                            day: 2,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Clean & Press',
                                type: 'strength',
                                group: 'Vai',
                                sets: 4,
                                reps: '8'
                            }, {
                                name: 'Barbell Curl',
                                type: 'strength',
                                group: 'Tay trước',
                                sets: 4,
                                reps: '10'
                            }]
                        },
                        {
                            name: 'Chân & Bụng A',
                            day: 3,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Barbell Squat',
                                type: 'strength',
                                group: 'Chân',
                                sets: 4,
                                reps: '8-10'
                            }, {
                                name: 'Straight Leg Deadlift',
                                type: 'strength',
                                group: 'Đùi sau',
                                sets: 4,
                                reps: '10'
                            }]
                        },
                        {
                            name: 'Ngực & Lưng B',
                            day: 4,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Incline Bench Press',
                                type: 'strength',
                                group: 'Ngực',
                                sets: 4,
                                reps: '10'
                            }, {
                                name: 'T-Bar Row',
                                type: 'strength',
                                group: 'Lưng',
                                sets: 4,
                                reps: '10'
                            }]
                        },
                        {
                            name: 'Vai & Tay B',
                            day: 5,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Seated Dumbbell Press',
                                type: 'strength',
                                group: 'Vai',
                                sets: 4,
                                reps: '10'
                            }, {
                                name: 'Skull Crusher',
                                type: 'strength',
                                group: 'Tay sau',
                                sets: 4,
                                reps: '10'
                            }]
                        },
                        {
                            name: 'Chân & Bụng B',
                            day: 6,
                            startTime: '17:30',
                            endTime: '19:00',
                            exercises: [{
                                name: 'Front Squat',
                                type: 'strength',
                                group: 'Chân',
                                sets: 4,
                                reps: '10'
                            }, {
                                name: 'Lying Leg Curl',
                                type: 'strength',
                                group: 'Đùi sau',
                                sets: 4,
                                reps: '12'
                            }]
                        }
                    ]
                }
            ];

            const MAP_DAY_NAME = {
                1: 'Thứ 2',
                2: 'Thứ 3',
                3: 'Thứ 4',
                4: 'Thứ 5',
                5: 'Thứ 6',
                6: 'Thứ 7',
                7: 'Chủ Nhật'
            };

            let currentSelectedPreset = null;

            function renderPresetCards() {
                const daysFilter = document.getElementById('filterPresetDays')?.value || 'all';
                const durationFilter = document.getElementById('filterPresetDuration')?.value || 'all';
                const intensityFilter = document.getElementById('filterPresetIntensity')?.value || 'all';
                const container = document.getElementById('presetCardsContainer');
                if (!container) return;

                const filtered = WORKOUT_PRESETS.filter(p => {
                    const matchDays = (daysFilter === 'all') || (p.days.toString() === daysFilter);
                    const matchDuration = (durationFilter === 'all') || (p.duration.toString() === durationFilter);
                    const matchIntensity = (intensityFilter === 'all') || (p.intensity === intensityFilter);
                    return matchDays && matchDuration && matchIntensity;
                });

                const countEl = document.getElementById('presetResultCountText');
                if (countEl) countEl.textContent = `Tìm thấy ${filtered.length} lịch tập phù hợp:`;

                const badgeEl = document.getElementById('presetActiveFilterBadge');
                if (badgeEl) {
                    const dTxt = daysFilter === 'all' ? 'Mọi số buổi' : `${daysFilter} buổi`;
                    const tTxt = durationFilter === 'all' ? 'Mọi thời lượng' : `${durationFilter}p`;
                    badgeEl.textContent = `${dTxt} • ${tTxt}`;
                }

                if (filtered.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4 px-3 bg-light rounded-4 border">
                            <i class="bi bi-funnel text-muted fs-3 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark fs-8 mb-1">Không có lịch tập nào khớp với bộ lọc hiện tại</h6>
                            <p class="text-muted fs-9 mb-2">Thử chọn "Tất cả số buổi" hoặc điều chỉnh cường độ để xem thêm các lịch tập mẫu khác.</p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-8" onclick="document.getElementById('filterPresetDays').value='all'; document.getElementById('filterPresetDuration').value='all'; document.getElementById('filterPresetIntensity').value='all'; renderPresetCards();">
                                Đặt lại bộ lọc
                            </button>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = filtered.map(p => {
                    const sessionChips = p.sessions.map(s => `
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fs-9 fw-normal">
                            <strong class="text-primary">${MAP_DAY_NAME[s.day] || 'T' + (s.day + 1)}:</strong> ${s.name.split('(')[0].trim()}
                        </span>
                    `).join('');

                    return `
                        <div class="card preset-card p-3 rounded-4 border bg-white shadow-2xs transition-all">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <h6 class="fw-bold text-dark mb-0 fs-7">${p.title}</h6>
                                        <span class="badge ${p.badgeClass} rounded-pill px-2 py-0.5 fs-9 fw-semibold">${p.badge}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="text-muted fs-9"><i class="bi bi-calendar3 me-0.5 text-primary"></i>${p.days} buổi/tuần</span>
                                        <span class="text-muted fs-9">&bull;</span>
                                        <span class="text-muted fs-9"><i class="bi bi-stopwatch me-0.5 text-primary"></i>${p.duration} phút/buổi</span>
                                        <span class="text-muted fs-9">&bull;</span>
                                        <span class="text-muted fs-9"><i class="bi bi-lightning-charge me-0.5 text-warning"></i>${p.intensityLabel}</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-8 fw-semibold shadow-xs btn-choose-preset" data-preset-id="${p.id}">
                                    <span>Chọn lịch này</span> &rarr;
                                </button>
                            </div>
                            <p class="text-muted fs-8 mb-2.5 line-clamp-2">${p.desc}</p>
                            <div class="d-flex flex-wrap gap-1.5">
                                ${sessionChips}
                            </div>
                        </div>
                    `;
                }).join('');

                container.querySelectorAll('.btn-choose-preset').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const pid = this.getAttribute('data-preset-id');
                        openPresetConfig(pid);
                    });
                });
            }

            function openPresetConfig(presetId) {
                const preset = WORKOUT_PRESETS.find(p => p.id === presetId);
                if (!preset) return;
                currentSelectedPreset = preset;

                document.getElementById('presetStepSelection')?.classList.add('d-none');
                document.getElementById('presetStepConfig')?.classList.remove('d-none');

                const titleEl = document.getElementById('cfgPresetTitle');
                if (titleEl) titleEl.textContent = preset.title;

                const descEl = document.getElementById('cfgPresetDesc');
                if (descEl) descEl.textContent = preset.desc;

                const badgeEl = document.getElementById('cfgPresetBadge');
                if (badgeEl) badgeEl.textContent = `${preset.days} buổi/tuần • ${preset.duration} phút • ${preset.intensityLabel}`;

                const nameInput = document.getElementById('cfgPlanCustomName');
                if (nameInput) nameInput.value = preset.title;

                // Render session time rows
                const container = document.getElementById('cfgSessionsListContainer');
                if (!container) return;

                container.innerHTML = preset.sessions.map((s, idx) => {
                    const dayOptions = [1, 2, 3, 4, 5, 6, 7].map(d => `
                        <option value="${d}" ${d === s.day ? 'selected' : ''}>${MAP_DAY_NAME[d]}</option>
                    `).join('');

                    const exCount = s.exercises ? s.exercises.length : 0;
                    const exPreview = s.exercises ? s.exercises.map(e => e.name).slice(0, 3).join(', ') : '';

                    return `
                        <div class="cfg-session-item p-2.5 rounded-3 bg-light border" data-session-idx="${idx}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 fs-9">Buổi ${idx + 1}</span>
                                    <span class="fw-bold text-dark fs-8 cfg-session-name">${s.name}</span>
                                </div>
                                <span class="badge bg-white text-muted border rounded-pill fs-9">${exCount} bài tập</span>
                            </div>
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-sm-4">
                                    <label class="form-label fs-9 text-muted mb-0.5 fw-semibold">Ngày tập trong tuần</label>
                                    <select class="form-select form-select-sm rounded-2 cfg-session-day bg-white shadow-2xs">
                                        ${dayOptions}
                                    </select>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="form-label fs-9 text-muted mb-0.5 fw-semibold">Giờ bắt đầu</label>
                                    <input type="time" class="form-control form-control-sm rounded-2 cfg-session-start bg-white shadow-2xs" value="${s.startTime}" required>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="form-label fs-9 text-muted mb-0.5 fw-semibold">Giờ kết thúc</label>
                                    <input type="time" class="form-control form-control-sm rounded-2 cfg-session-end bg-white shadow-2xs" value="${s.endTime}" required>
                                </div>
                            </div>
                            ${exPreview ? `<div class="mt-1.5 fs-10 text-muted text-truncate"><i class="bi bi-card-checklist me-1"></i>Bài tập: ${exPreview}...</div>` : ''}
                        </div>
                    `;
                }).join('');
            }

            // Gắn sự kiện bộ lọc
            ['filterPresetDays', 'filterPresetDuration', 'filterPresetIntensity'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', renderPresetCards);
            });

            // Gắn sự kiện quay lại / hủy
            document.getElementById('btnBackToPresetList')?.addEventListener('click', () => {
                document.getElementById('presetStepConfig')?.classList.add('d-none');
                document.getElementById('presetStepSelection')?.classList.remove('d-none');
            });
            document.getElementById('btnCancelPresetConfig')?.addEventListener('click', () => {
                document.getElementById('presetStepConfig')?.classList.add('d-none');
                document.getElementById('presetStepSelection')?.classList.remove('d-none');
            });

            // Toggle hiển thị tùy chọn lịch
            const cfgSyncCalendar = document.getElementById('cfgSyncCalendar');
            const cfgCalendarOptionsWrapper = document.getElementById('cfgCalendarOptionsWrapper');
            if (cfgSyncCalendar && cfgCalendarOptionsWrapper) {
                cfgSyncCalendar.addEventListener('change', function() {
                    cfgCalendarOptionsWrapper.classList.toggle('d-none', !this.checked);
                });
            }

            // Nút Áp dụng Kế hoạch mẫu & Thêm vào Lịch Calendar
            document.getElementById('btnApplyPresetPlan')?.addEventListener('click', function() {
                if (!currentSelectedPreset) return;
                const btn = this;
                const planName = document.getElementById('cfgPlanCustomName')?.value.trim() || currentSelectedPreset.title;
                const syncCalendar = document.getElementById('cfgSyncCalendar')?.checked ?? true;
                const syncWeeks = parseInt(document.getElementById('cfgSyncWeeks')?.value || '4', 10);
                const enableNotif = document.getElementById('cfgEnableNotif')?.checked ?? true;

                // Thu thập sessions
                const sessionRows = document.querySelectorAll('#cfgSessionsListContainer .cfg-session-item');
                const sessionsPayload = [];

                for (let row of sessionRows) {
                    const idx = parseInt(row.getAttribute('data-session-idx'), 10);
                    const origSession = currentSelectedPreset.sessions[idx] || {};
                    const day = parseInt(row.querySelector('.cfg-session-day')?.value || '1', 10);
                    const start = row.querySelector('.cfg-session-start')?.value || '17:30';
                    const end = row.querySelector('.cfg-session-end')?.value || '18:30';

                    if (!start || !end) {
                        alert('Vui lòng nhập đầy đủ giờ bắt đầu và giờ kết thúc cho từng buổi tập.');
                        return;
                    }

                    sessionsPayload.push({
                        ten_buoi_tap: origSession.name || `Buổi tập ${idx + 1}`,
                        ngay_trong_tuan: day,
                        gio_bat_dau: start,
                        gio_ket_thuc: end,
                        mo_ta: `Lịch tập theo mẫu: ${currentSelectedPreset.title}`,
                        exercises: origSession.exercises || []
                    });
                }

                // Gửi AJAX POST lên server
                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang tạo lịch...`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch("{{ route('tap-luyen.ke-hoach.ap-dung-mau') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            preset_id: currentSelectedPreset.id,
                            ten_ke_hoach: planName,
                            mo_ta: currentSelectedPreset.desc,
                            dong_bo_calendar: syncCalendar,
                            so_tuan_lap: syncWeeks,
                            bat_thong_bao: enableNotif,
                            sessions: sessionsPayload
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btn.innerHTML = `<i class="bi bi-check-lg me-1"></i> Thành công!`;
                            setTimeout(() => {
                                window.location.reload();
                            }, 600);
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi tạo kế hoạch.');
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi áp dụng lịch mẫu:', err);
                        alert('Lỗi kết nối máy chủ. Vui lòng thử lại!');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            });

            // Khởi tạo render lần đầu
            renderPresetCards();
        });
    </script>

    <!-- =========================================================================
         KÊNH CHAT THẾ GIỚI GYMER - GLOBAL COMMUNITY POPUP (GÓC PHẢI BÊN DƯỚI)
         ========================================================================= -->
    <div class="community-messenger-popup shadow-lg" id="communityMessengerPopup" role="dialog" aria-modal="true" aria-label="Kênh Chat Thế Giới Gymer">

        <!-- HEADER KÊNH CHAT THẾ GIỚI -->
        <div class="community-popup-header">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="header-icon-circle">
                    <i class="bi bi-globe-americas"></i>
                </div>
                <div class="min-w-0">
                    <h6 class="fw-bold mb-0 text-dark fs-7 text-truncate">Kênh Chat Thế Giới</h6>
                    <small class="text-success fs-9 d-flex align-items-center gap-1.5 fw-medium">
                        <span class="online-indicator-dot"></span>
                        <span id="worldOnlineCountText">{{ max(count($communityGymers ?? []), 4) + 8 }} Gymer đang online</span>
                    </small>
                </div>
            </div>

            <!-- Nút công cụ & đóng -->
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="button" class="chat-tool-btn" id="btnRefreshWorldChat" title="Tải lại tin nhắn">
                    <i class="bi bi-arrow-clockwise fs-7"></i>
                </button>
                <button type="button" class="chat-tool-btn" id="btnCloseCommunityPopup" aria-label="Đóng popup" title="Đóng">
                    <i class="bi bi-x-lg fs-7"></i>
                </button>
            </div>
        </div>

        <!-- BODY: DÒNG THỜI GIAN TIN NHẮN THẾ GIỚI (CUỘN ĐỘC LẬP) -->
        <div class="world-chat-body" id="worldChatContainer">
            <div class="world-chat-messages" id="worldChatMessages">
                <!-- Tin nhắn chào mừng Kênh Thế Giới -->
                <div class="text-center my-1">
                    <span class="chat-system-pill">
                        <i class="bi bi-broadcast me-1 text-primary"></i> Chào mừng bạn đến với Kênh Chat Thế Giới Gymer!
                    </span>
                </div>
            </div>
        </div>

        <!-- FOOTER: KHUNG NHẬP LIỆU GỬI TIN NHẮN LÊN KÊNH THẾ GIỚI -->
        <div class="world-chat-footer">
            <form id="worldChatForm" class="d-flex align-items-center gap-2" onsubmit="return false;">
                <input type="text" class="form-control" id="worldChatMessageInput" placeholder="Nhắn tin lên kênh thế giới..." autocomplete="off">
                <button type="submit" id="btnSendWorldMessage" title="Gửi lên kênh thế giới">
                    <i class="bi bi-send-fill fs-8"></i>
                </button>
            </form>
            <div class="mt-1 px-1 d-flex align-items-center justify-content-between text-nowrap">
                <small class="text-muted fs-10 text-truncate me-2">Tin nhắn công khai toàn cầu</small>
                <a href="{{ route('workout.community') }}" class="text-decoration-none fs-10 text-primary fw-semibold flex-shrink-0">
                    <i class="bi bi-collection-play me-0.5"></i> Thư viện giáo án
                </a>
            </div>
        </div>

    </div>

    <!-- Nút Cộng đồng Gymer nổi (Mở Messenger Popup) -->
    <button type="button" class="floating-community-btn" id="floatingCommunityToggle" title="Cộng đồng Gymer" aria-label="Cộng đồng Gymer">
        <i class="bi bi-chat-dots-fill icon-community-open"></i>
        <i class="bi bi-x-lg icon-community-close"></i>
        <span class="community-tooltip">Cộng đồng Gymer</span>
    </button>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Mobile Bottom Navigation Bar (Flutter Style) -->
    @include('layouts.bottom-nav')

    <!-- PWA Install Modal -->
    <x-pwa-install-modal />
</body>

</html>