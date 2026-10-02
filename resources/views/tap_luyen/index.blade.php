<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Theo Dõi Thể Chất - Life Planner</title>

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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <!-- CSS riêng của Module Thể Chất -->
    <link rel="stylesheet" href="{{ asset('css/tap-luyen.css') }}?v={{ time() }}">
</head>

<body>

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH FULL WIDTH - FLOATING MENU) -->
    <div class="main-wrapper main-wrapper-full">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Theo dõi Thể chất'])

        <!-- CONTENT BODY (CUỘN ĐỘC LẬP) -->
        <main class="content-body">

            <!-- SUB NAV TABS: THEO DÕI / BÁO CÁO -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-inline-flex p-1 bg-white border rounded-pill shadow-xs">
                    <a href="{{ route('tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill btn-primary fw-semibold shadow-xs">
                        <i class="bi bi-heart-pulse-fill me-1"></i> Theo dõi Thể chất
                    </a>
                    <a href="{{ route('bao-cao-tap-luyen.index') }}" class="btn btn-sm px-3 py-1.5 rounded-pill text-secondary fw-semibold">
                        <i class="bi bi-bar-chart-fill me-1"></i> Báo cáo
                    </a>
                </div>

                @if($activePlan)
                    <div class="d-flex align-items-center gap-2">
                        <form method="POST" action="{{ route('workout.share.generate', ['id' => $activePlan->id]) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 shadow-2xs d-inline-flex align-items-center gap-1.5" title="Chia sẻ kế hoạch này cho bạn bè hoặc cộng đồng">
                                <i class="bi bi-share-fill"></i>
                                <span class="fw-semibold">{{ $activePlan->is_shared ? 'Mã: ' . $activePlan->share_code : 'Chia sẻ Plan này' }}</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- 1. TIÊU ĐỀ TRANG & HÀNH ĐỘNG CHÍNH (COMPACT HEADER ROW) -->
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2.5">
                <div>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span>Theo dõi Thể chất & Rèn luyện</span>
                    </h5>
                    <small class="text-muted">Quản lý kế hoạch tập luyện, theo dõi thời gian và lịch sử rèn luyện mỗi ngày.</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-2xs d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="modal" data-bs-target="#manageScheduleModal">
                        <i class="bi bi-calendar3"></i>
                        <span class="fw-semibold">Quản lý lịch tập</span>
                    </button>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 shadow-2xs d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                        <i class="bi bi-pencil-square"></i>
                        <span class="fw-semibold">Tùy chỉnh buổi tập</span>
                    </button>
                </div>
            </div>

            <!-- 2. BANNER LỊCH TẬP TUẦN (COMPACT PILLS) -->
            <div class="card border-0 shadow-2xs schedule-orientation-card mb-3">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge bg-indigo-subtle text-indigo px-2.5 py-1.5 rounded-pill fw-semibold fs-8">
                            <i class="bi bi-calendar-week me-1"></i> {{ $activePlan->ten_ke_hoach ?? 'Kế hoạch cá nhân' }}
                        </span>
                        <span class="text-muted fs-8 d-none d-xl-inline">Lịch trình tuần:</span>
                    </div>
                    <div class="flex-grow-1 min-w-0" id="weekly-schedule-pills">
                        @foreach($lichTuan as $dayIso => $dh)
                            @php
                                $isToday = ($dayIso === $todayIso);
                                $isSelected = ($currentBuoiTap && $dh['buoi_tap_id'] === $currentBuoiTap->id);
                            @endphp
                            <a href="{{ $dh['buoi_tap_id'] ? route('tap-luyen.index', ['buoi_tap_id' => $dh['buoi_tap_id']]) : route('tap-luyen.index') }}"
                               class="schedule-day-pill text-decoration-none {{ $isToday ? 'active-today' : '' }} {{ $isSelected ? 'border-primary' : '' }} {{ $dh['is_rest'] ? 'rest-day-pill' : 'workout-day-pill' }}"
                               title="{{ $dh['thu'] }}: {{ $dh['mo_ta'] }}"
                               data-day="{{ $dayIso }}"
                               data-buoi-tap-id="{{ $dh['buoi_tap_id'] ?? '' }}"
                               data-name="{{ $dh['ten'] }}">
                                <span class="day-label">{{ $dh['short'] ?? $dh['thu'] }}</span>
                                <span class="split-name {{ $dh['is_rest'] ? 'text-muted fst-italic' : '' }}">
                                    @if($dh['is_rest'])
                                        <i class="bi bi-cup-hot me-1 text-secondary"></i>
                                    @endif
                                    {{ $dh['ten'] }}
                                </span>
                                @if($isToday)
                                    <span class="today-marker">Hôm nay</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($isRestDay)
                <!-- ==================== GIAO DIỆN REST DAY ==================== -->
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white rest-day-card mb-4 my-auto">
                    <div class="rest-day-icon mb-3 rest-day-icon-bounce" style="font-size: 3.8rem;">😴</div>
                    <h2 class="fw-bold text-dark mb-2 letter-spacing-1">REST DAY</h2>
                    <h5 class="text-primary fw-bold mb-2">Buổi tập hôm nay: Hôm nay là ngày nghỉ</h5>
                    <p class="text-muted fs-6 mb-4" style="max-width: 520px; margin: 0 auto; line-height: 1.6;">
                        Hãy nghỉ ngơi, nạp đủ dinh dưỡng và phục hồi cơ bắp để sẵn sàng cho các buổi rèn luyện tiếp theo.
                    </p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                        <span class="text-muted fs-7">Muốn tập luyện buổi khác hôm nay?</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold dropdown-toggle shadow-2xs" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-play-circle me-1.5"></i> Chọn buổi tập khác
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
                <!-- ==================== GIAO DIỆN BUỔI TẬP THỰC CHIẾN ==================== -->

                <!-- 1. TIẾN ĐỘ BUỔI TẬP (TOP PROGRESS BAR) -->
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-35 mb-3 workout-progress-card">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-trophy-fill fs-6"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Buổi tập hôm nay: <span class="text-primary" id="workout-title-display">{{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : 'Buổi rèn luyện' }}</span></h6>
                                <small class="text-muted" id="workout-progress-subtitle">Tiến độ buổi tập • Đánh dấu bài tập trong checklist để cập nhật tiến độ</small>
                            </div>
                        </div>
                        <div class="text-sm-end">
                            <span class="fw-bold fs-7 text-primary" id="workout-progress-text">0 / {{ count($danhSachBaiTap) }} bài tập hoàn thành (0%)</span>
                        </div>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-success rounded-pill transition-all" id="workout-progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- 2. KHU VỰC THỰC CHIẾN: CHECKLIST TRÁI + TIMER PHẢI -->
                <div class="row g-3 mb-4">

                    <!-- CỘT TRÁI: CHECKLIST BÀI TẬP (SCROLL ĐỘC LẬP) -->
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-35 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-check2-square text-primary fs-5"></i>
                                        <span>Checklist bài tập</span>
                                    </h6>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-8" id="checklist-count-badge">
                                        {{ count($danhSachBaiTap) }} bài
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted fs-8" id="btn-uncheck-all" title="Bỏ chọn tất cả bài tập">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Làm mới
                                    </button>
                                </div>
                            </div>

                            <!-- VÙNG SCROLL ĐỘC LẬP CHO CHECKLIST (KHÔNG KÉO TIMER) -->
                            <div class="workout-checklist-container d-flex flex-column gap-2 flex-grow-1" id="workout-checklist-items">
                                @forelse($danhSachBaiTap as $idx => $ex)
                                    <div class="checklist-exercise-item card border rounded-3 p-2.5 {{ $idx === 0 ? 'active-exercise' : '' }}" 
                                         id="checklist-item-{{ $ex['id'] }}" 
                                         data-id="{{ $ex['id'] }}" 
                                         data-index="{{ $idx }}"
                                         data-name="{{ $ex['name'] }}"
                                         data-type="{{ $ex['type'] ?? 'strength' }}"
                                         data-metric="{{ $ex['metric_display'] ?: ($ex['sets'].' sets × '.$ex['reps'].' reps') }}">
                                        <div class="d-flex align-items-center justify-content-between gap-2.5">
                                            <div class="d-flex align-items-center gap-2.5 min-w-0 flex-grow-1">
                                                <input type="checkbox" class="custom-check-box workout-exercise-checkbox" id="chk-ex-{{ $ex['id'] }}" data-id="{{ $ex['id'] }}" data-index="{{ $idx }}">
                                                <div class="min-w-0 flex-grow-1">
                                                    <div class="fw-bold text-dark fs-7 text-truncate exercise-title">
                                                        {{ $idx + 1 }}. {{ $ex['name'] }}
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center gap-1.5 fs-9 text-muted mt-0.5">
                                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5">{{ $ex['group'] ?: 'Toàn thân' }}</span>
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fw-semibold">{{ $ex['metric_display'] ?: ($ex['sets'].' sets × '.$ex['reps'].' reps') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 fs-9 completed-tag d-none">
                                                <i class="bi bi-check-circle-fill me-1"></i>Xong
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted my-auto">
                                        <div class="mb-2"><i class="bi bi-inbox fs-1 text-secondary"></i></div>
                                        <h6 class="fw-bold text-dark mb-1">Chưa có bài tập nào</h6>
                                        <p class="fs-8 text-muted mb-3">Buổi tập này chưa được thiết lập danh sách bài tập.</p>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                                            <i class="bi bi-plus-lg me-1"></i>Thêm bài tập vào buổi này
                                        </button>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- CỘT PHẢI: TIMER TẬP LUYỆN (STICKY CỐ ĐỊNH, KHÔNG BỊ TRÔI) -->
                    <div class="col-12 col-lg-5">
                        <div class="workout-timer-sticky">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-stopwatch text-primary fs-5"></i>
                                        <span>Đồng hồ tập</span>
                                    </h6>
                                    <span class="badge workout-status-badge badge-not-started" id="workout-status-badge">
                                        <i class="bi bi-circle-fill me-1 fs-9"></i>
                                        <span id="workout-status-text">Chưa bắt đầu</span>
                                    </span>
                                </div>

                                <!-- HỘP TIMER KỸ THUẬT SỐ -->
                                <div class="workout-timer-box text-center py-3 mb-3">
                                    <div class="timer-display" id="workout-timer">00:00:00</div>
                                    <small class="text-muted fs-8">Bấm "Bắt đầu tập" để tính thời gian rèn luyện</small>
                                </div>

                                <!-- BÀI TẬP ĐANG ACTIVE TRONG PLAYER -->
                                <div class="card bg-light border-0 rounded-3 p-2.5 mb-3 text-center">
                                    <div class="mb-1" id="active-ex-type-container">
                                        <span class="badge exercise-type-badge badge-type-strength px-2.5 py-0.5 rounded-pill fs-9" id="active-ex-type-badge">Strength</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 exercise-title-text text-truncate px-2" id="active-ex-name">
                                        {{ !empty($danhSachBaiTap[0]) ? '1. ' . $danhSachBaiTap[0]['name'] : 'Chưa có bài tập' }}
                                    </h6>
                                    <div class="text-muted fw-semibold fs-8" id="active-ex-set-rep">
                                        {{ !empty($danhSachBaiTap[0]) ? ($danhSachBaiTap[0]['metric_display'] ?: ($danhSachBaiTap[0]['sets'].' sets × '.$danhSachBaiTap[0]['reps'].' reps')) : '-- x --' }}
                                    </div>
                                </div>

                                <!-- BỘ NÚT ĐIỀU KHIỂN TIMER -->
                                <div class="d-flex flex-column gap-2">
                                    <!-- Nút Bắt đầu tập / Tạm dừng / Tiếp tục -->
                                    <button class="btn btn-primary py-2.5 rounded-pill fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2" id="toggle-timer-btn">
                                        <i class="bi bi-play-fill fs-5" id="toggle-timer-icon"></i>
                                        <span id="toggle-timer-text">Bắt đầu tập</span>
                                    </button>

                                    <div class="d-flex gap-2">
                                        <!-- Nút Bài tiếp theo -->
                                        <button class="btn btn-outline-secondary py-2 rounded-pill fw-semibold flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" id="next-ex-btn">
                                            <i class="bi bi-skip-forward-fill"></i>
                                            <span>Bài tiếp theo</span>
                                        </button>

                                        <!-- Nút Hoàn thành buổi tập -->
                                        <button class="btn btn-success py-2 rounded-pill fw-bold text-white flex-grow-1 shadow-xs d-flex align-items-center justify-content-center gap-1.5" id="finish-ex-btn">
                                            <i class="bi bi-check2-circle fs-6"></i>
                                            <span>Hoàn thành</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabs chọn nhanh buổi tập khác nếu có -->
                                @if(count($buoiTapList) > 1)
                                    <div class="mt-3 pt-2.5 border-top">
                                        <small class="text-muted d-block mb-1.5 fs-9 fw-semibold text-uppercase">Các buổi tập trong kế hoạch:</small>
                                        <div class="d-flex flex-wrap gap-1" id="workout-tabs-container">
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

                </div>
            @endif

        </main>
    </div>

    <!-- MODAL 1: QUẢN LÝ LỊCH TẬP & KẾ HOẠCH (MANAGE SCHEDULE MODAL) -->
    <div class="modal fade" id="manageScheduleModal" tabindex="-1" aria-labelledby="manageScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="manageScheduleModalLabel">
                        <i class="bi bi-calendar3 me-2 text-primary"></i>Quản lý Lịch tập & Kế hoạch
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <ul class="nav nav-pills mb-3 border-bottom pb-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-semibold px-3 py-1.5 fs-8" id="pills-sessions-tab" data-bs-toggle="pill" data-bs-target="#pills-sessions" type="button" role="tab">
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
                        <!-- TAB 1: CÁC BUỔI TẬP TRONG TUẦN -->
                        <div class="tab-pane fade show active" id="pills-sessions" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Kế hoạch hiện tại: <span class="text-primary">{{ $activePlan->ten_ke_hoach ?? 'Chưa đặt tên' }}</span></h6>
                                    <small class="text-muted">Gán thứ trong tuần (1=Thứ 2 ... 7=Chủ Nhật). Ngày trống sẽ tự tính là Ngày Nghỉ.</small>
                                </div>
                            </div>

                            <!-- Form Thêm buổi tập mới -->
                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm buổi tập mới vào kế hoạch này</h6>
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
                                <table class="table table-hover align-middle">
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
                                                    <div class="fw-bold text-dark">{{ $bt['title'] }}</div>
                                                    <small class="text-muted">{{ $bt['mo_ta'] ?: 'Không có mô tả' }}</small>
                                                </td>
                                                <td>
                                                    @if(!empty($bt['ten_thu']))
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">{{ $bt['ten_thu'] }}</span>
                                                    @else
                                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1">Tùy chọn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border rounded-pill">{{ count($bt['exercises']) }} bài</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill text-danger btn-delete-session" data-id="{{ $bt['id'] }}" title="Xóa buổi tập">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">Chưa có buổi tập nào trong kế hoạch này.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: QUẢN LÝ CÁC KẾ HOẠCH -->
                        <div class="tab-pane fade" id="pills-plans" role="tabpanel">
                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-folder-plus text-primary me-1"></i>Tạo kế hoạch tập luyện mới</h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Tên kế hoạch</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="new-plan-name" placeholder="VD: Upper/Lower Split, Full Body...">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fs-8 text-muted mb-1 fw-semibold">Mô tả (tùy chọn)</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="new-plan-desc" placeholder="VD: Tập 4 buổi mỗi tuần">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" id="btn-create-plan">
                                            <i class="bi bi-plus-lg me-1"></i>Tạo kế hoạch
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2" id="plans-list-container">
                                @foreach($keHoachList as $kh)
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 border bg-white {{ $kh->is_active ? 'border-primary shadow-xs' : '' }}">
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark">{{ $kh->ten_ke_hoach }}</span>
                                                @if($kh->is_active)
                                                    <span class="badge bg-primary text-white rounded-pill fs-8">Đang áp dụng</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $kh->mo_ta ?: 'Không có mô tả' }} • {{ $kh->buoiTaps->count() }} buổi tập</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!$kh->is_active)
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-activate-plan" data-id="{{ $kh->id }}">
                                                    Kích hoạt
                                                </button>
                                                @if($keHoachList->count() > 1)
                                                    <button type="button" class="btn btn-sm btn-light border text-danger rounded-circle btn-delete-plan" data-id="{{ $kh->id }}" title="Xóa kế hoạch">
                                                        <i class="bi bi-trash"></i>
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
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal" onclick="window.location.reload();">Đóng & Tải lại</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: CHỈNH SỬA / THÊM BÀI TẬP (MODAL BOOTSTRAP 5) -->
    <div class="modal fade" id="editWorkoutModal" tabindex="-1" aria-labelledby="editWorkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="editWorkoutModalLabel">
                        <i class="bi bi-sliders me-2 text-primary"></i>Tùy chỉnh buổi tập
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <input type="hidden" id="modal-buoi-tap-id" value="{{ $currentBuoiTap ? $currentBuoiTap->id : '' }}">

                    <!-- Chọn hoặc nhập tên buổi tập -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Tên buổi tập</label>
                            <input type="text" class="form-control rounded-3" id="modal-workout-title" placeholder="VD: Push, Upper Body, Chân...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Ngày trong tuần</label>
                            <select class="form-select rounded-3" id="modal-workout-day">
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

                    <!-- Thêm bài tập mới đa dạng loại bài (Strength, Core, Cardio, Other) -->
                    <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0 small">
                                <i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm bài tập vào buổi này
                            </h6>
                            <span class="text-muted fs-8">Hỗ trợ Sets/Reps, Thời gian & Cardio</span>
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
                            <h6 class="fw-bold text-dark mb-0 small">Danh sách bài tập hiện tại</h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill small" id="exercise-count-badge">0 bài tập</span>
                        </div>

                        <div class="d-flex flex-column gap-2" id="modal-exercise-list" style="max-height: 280px; overflow-y: auto;">
                            <!-- Render tự động bởi JS -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-2" id="save-workout-btn">
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
            currentMonth: {{ $currentMonth }},
            currentYear: {{ $currentYear }},
            currentType: "{{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : ($dinhHuongHomNay['ten'] ?? 'Tập tự do') }}",
            currentBuoiTapId: {{ $currentBuoiTap ? $currentBuoiTap->id : 'null' }},
            activePlanId: {{ $activePlan ? $activePlan->id : 'null' }},
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

    <!-- Đồng bộ Theme Icon khi đổi theme -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quickThemeBtn = document.getElementById('quickThemeBtn');
            const quickThemeIcon = document.getElementById('quickThemeIcon');

            function syncThemeIcon(theme) {
                if (!quickThemeIcon) return;
                if (theme === 'dark') {
                    quickThemeIcon.className = 'bi bi-sun-fill text-warning fs-6';
                } else {
                    quickThemeIcon.className = 'bi bi-moon-stars-fill text-primary fs-6';
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            syncThemeIcon(currentTheme);

            if (quickThemeBtn) {
                quickThemeBtn.addEventListener('click', function () {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    if (newTheme === 'dark') {
                        document.documentElement.classList.add('dark-theme');
                    } else {
                        document.documentElement.classList.remove('dark-theme');
                    }
                    localStorage.setItem('theme', newTheme);
                    syncThemeIcon(newTheme);

                    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch("{{ route('profile.theme.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ theme: newTheme })
                        }).catch(err => console.log('Lỗi lưu giao diện:', err));
                    }
                });
            }

            const btnQuickToggleNotif = document.getElementById('btnQuickToggleNotif');
            if (btnQuickToggleNotif) {
                btnQuickToggleNotif.addEventListener('change', function () {
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
                            body: JSON.stringify({ enabled: isEnabled })
                        }).catch(err => console.log('Lỗi cập nhật thông báo:', err));
                    }
                });
            }
        });
    </script>
    <!-- =========================================================================
         KÊNH CHAT THẾ GIỚI GYMER - GLOBAL COMMUNITY POPUP (GÓC PHẢI BÊN DƯỚI)
         ========================================================================= -->
    <div class="community-messenger-popup shadow-lg" id="communityMessengerPopup" role="dialog" aria-modal="true" aria-label="Kênh Chat Thế Giới Gymer">
        
        <!-- HEADER KÊNH CHAT THẾ GIỚI -->
        <div class="community-popup-header d-flex align-items-center justify-content-between px-3 py-2.5 border-bottom">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="header-icon-circle bg-primary-subtle text-primary flex-shrink-0">
                    <i class="bi bi-globe-americas"></i>
                </div>
                <div class="min-w-0">
                    <h6 class="fw-bold mb-0 text-dark fs-7 text-truncate">Kênh Chat Thế Giới</h6>
                    <small class="text-success fs-9 d-flex align-items-center gap-1 fw-medium">
                        <span class="online-indicator-dot"></span>
                        <span id="worldOnlineCountText">{{ max(count($communityGymers ?? []), 4) + 8 }} Gymer đang online</span>
                    </small>
                </div>
            </div>

            <!-- Nút công cụ & đóng -->
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-light rounded-circle p-1 text-muted" id="btnRefreshWorldChat" title="Tải lại tin nhắn">
                    <i class="bi bi-arrow-clockwise fs-8"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-circle p-1 text-muted" id="btnCloseCommunityPopup" aria-label="Đóng popup" title="Đóng">
                    <i class="bi bi-x-lg fs-7"></i>
                </button>
            </div>
        </div>

        <!-- BODY: DÒNG THỜI GIAN TIN NHẮN THẾ GIỚI (CUỘN ĐỘC LẬP) -->
        <div class="world-chat-body flex-grow-1 p-3 d-flex flex-column min-h-0" id="worldChatContainer">
            <div class="world-chat-messages d-flex flex-column gap-2.5 flex-grow-1 overflow-y-auto" id="worldChatMessages">
                <!-- Tin nhắn chào mừng Kênh Thế Giới -->
                <div class="text-center my-1">
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 fs-9 fw-normal shadow-2xs">
                        <i class="bi bi-broadcast me-1 text-primary"></i> Chào mừng bạn đến với Kênh Chat Thế Giới Gymer!
                    </span>
                </div>
            </div>
        </div>

        <!-- FOOTER: KHUNG NHẬP LIỆU GỬI TIN NHẮN LÊN KÊNH THẾ GIỚI -->
        <div class="world-chat-footer p-2.5 border-top bg-white">
            <form id="worldChatForm" class="d-flex align-items-center gap-2" onsubmit="return false;">
                <input type="text" class="form-control form-control-sm rounded-pill px-3 py-1.5 fs-8" id="worldChatMessageInput" placeholder="Nhắn tin lên kênh thế giới..." autocomplete="off">
                <button type="submit" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" id="btnSendWorldMessage" style="width: 34px; height: 34px;" title="Gửi lên kênh thế giới">
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
        <i class="bi bi-globe2 icon-community-open"></i>
        <i class="bi bi-chat-dots-fill icon-community-active d-none"></i>
        <span class="community-tooltip">Cộng đồng Gymer</span>
    </button>

    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />
</body>

</html>
