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

    <!-- SIDEBAR BÊN TRÁI DÙNG CHUNG -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH 100VH) -->
    <div class="main-wrapper">

        <!-- TOPBAR TRÊN CÙNG COMPACT 58PX -->
        @include('layouts.topbar', ['title' => 'Theo dõi Thể chất'])

        <!-- CONTENT BODY (CUỘN ĐỘC LẬP) -->
        <main class="content-body">

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

            <!-- 2. BỐN THẺ KPI THỐNG KÊ (MICRO KPI ROW - DATA THẬT TỪ DATABASE) -->
            <div class="row g-2 mb-2.5">
                <!-- 1. Thời gian tập hôm nay -->
                <div class="col-6 col-md-3">
                    <div class="kpi-micro-card shadow-2xs">
                        <div class="kpi-micro-icon bg-primary-subtle text-primary">
                            <i class="bi bi-stopwatch-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="kpi-micro-title text-truncate">Thời gian hôm nay</div>
                            <div class="kpi-micro-val text-truncate" id="kpi-duration">
                                {{ $thoiGianTapHomNay }} <span class="fs-8 fw-normal text-muted">phút</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Buổi tập trong tuần -->
                <div class="col-6 col-md-3">
                    <div class="kpi-micro-card shadow-2xs">
                        <div class="kpi-micro-icon bg-danger-subtle text-danger">
                            <i class="bi bi-fire"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="kpi-micro-title text-truncate">Buổi trong tuần</div>
                            <div class="kpi-micro-val text-truncate" id="kpi-week">
                                {{ $buoiTapTuanNay }} <span class="fs-8 fw-normal text-muted">buổi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Tổng số buổi tập đã hoàn thành -->
                <div class="col-6 col-md-3">
                    <div class="kpi-micro-card shadow-2xs">
                        <div class="kpi-micro-icon bg-success-subtle text-success">
                            <i class="bi bi-check2-all"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="kpi-micro-title text-truncate">Tổng hoàn thành</div>
                            <div class="kpi-micro-val text-truncate" id="kpi-total">
                                {{ $tongBuoiTap }} <span class="fs-8 fw-normal text-muted">buổi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Chuỗi hoạt động liên tục (Streak) -->
                <div class="col-6 col-md-3">
                    <div class="kpi-micro-card shadow-2xs">
                        <div class="kpi-micro-icon bg-warning-subtle text-warning">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="kpi-micro-title text-truncate">Chuỗi rèn luyện</div>
                            <div class="kpi-micro-val text-truncate" id="kpi-streak">
                                {{ $chuoiNgayTap }} <span class="fs-8 fw-normal text-muted">ngày</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. BANNER LỊCH TẬP TUẦN TỰ DO THEO DATABASE (COMPACT SINGLE ROW) -->
            <div class="card border-0 shadow-2xs schedule-orientation-card mb-2.5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge bg-indigo-subtle text-indigo px-2.5 py-1.5 rounded-pill fw-semibold fs-8">
                            <i class="bi bi-calendar-week me-1"></i> {{ $activePlan->ten_ke_hoach ?? 'Kế hoạch cá nhân' }}
                        </span>
                        <span class="text-muted fs-8 d-none d-xl-inline">Bấm vào ngày có buổi tập để chọn:</span>
                    </div>
                    <div class="flex-grow-1 min-w-0" id="weekly-schedule-pills">
                        @foreach($lichTuan as $dayIso => $dh)
                            @php
                                $isToday = ($dayIso === $todayIso);
                            @endphp
                            <div class="schedule-day-pill {{ $isToday ? 'active-today' : '' }} {{ $dh['is_rest'] ? 'rest-day-pill' : 'workout-day-pill' }}"
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
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 4. KHU VỰC CHÍNH: TRÌNH TẬP LUYỆN (WORKOUT PLAYER) & CỘT BÊN PHẢI (LỊCH + HOẠT ĐỘNG) -->
            <div class="row g-3 mb-3">

                <!-- CỘT TRÁI: TRÌNH TẬP LUYỆN (WORKOUT PLAYER COMPACT) -->
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-35 h-100 d-flex flex-column justify-content-between">

                        <!-- Header card & Tabs chọn buổi tập trong kế hoạch -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-0.5">
                                    <h6 class="fw-bold text-dark mb-0">Buổi tập hôm nay</h6>
                                    @if($currentBuoiTap && $currentBuoiTap->ten_thu)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-8">
                                            {{ $currentBuoiTap->ten_thu }}
                                        </span>
                                    @endif
                                    @if($dinhHuongHomNay['is_rest'])
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill fs-8">
                                            <i class="bi bi-moon-stars me-1"></i> Hôm nay là ngày nghỉ
                                        </span>
                                    @endif
                                </div>
                                <span class="text-muted fs-8" id="workout-title-display">
                                    {{ $currentBuoiTap ? $currentBuoiTap->ten_buoi_tap : ($dinhHuongHomNay['ten'] ?? 'Tập tự do') }}
                                </span>
                            </div>

                            <!-- Tabs chọn loại buổi tập (Được sinh tự động từ các buổi tập trong Kế hoạch DB) -->
                            <div class="d-flex flex-wrap gap-1.5" id="workout-tabs-container">
                                @forelse($buoiTapList as $session)
                                    <button type="button"
                                            class="workout-type-tab workout-tab {{ ($currentBuoiTap && $currentBuoiTap->id === $session['id']) ? 'active' : '' }}"
                                            data-id="{{ $session['id'] }}"
                                            data-type="{{ $session['title'] }}">
                                        {{ $session['title'] }}
                                        @if(!empty($session['ten_thu']))
                                            <span class="badge bg-light text-dark rounded-pill ms-1 fs-9">{{ $session['ten_thu'] }}</span>
                                        @endif
                                    </button>
                                @empty
                                    <button type="button" class="workout-type-tab workout-tab active" data-id="" data-type="Tập tự do">Tập tự do</button>
                                @endforelse
                            </div>
                        </div>

                        <!-- Khung tập luyện trung tâm (Workout Box) GỌN GÀNG, KHÔNG DƯ THỪA KHOẢNG TRẮNG -->
                        <div class="workout-timer-box d-flex flex-column align-items-center justify-content-center text-center my-auto">

                            <!-- 1. [TRẠNG THÁI] -->
                            <div class="mb-1" id="workout-status-badge-container">
                                <span class="badge workout-status-badge badge-not-started shadow-2xs" id="workout-status-badge">
                                    <i class="bi bi-circle-fill me-1 fs-9"></i>
                                    <span id="workout-status-text">Chưa bắt đầu</span>
                                </span>
                            </div>

                            <!-- 2. [TIMER] Cố định 00:00:00 ban đầu, không tự chạy -->
                            <div class="timer-display" id="workout-timer">00:00:00</div>

                            <!-- 3. [TÊN BÀI TẬP & BADGE LOẠI BÀI] -->
                            <div class="mb-1" id="active-ex-type-container">
                                <span class="badge exercise-type-badge badge-type-strength px-2.5 py-0.5 rounded-pill fs-8" id="active-ex-type-badge">Strength</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-0.5 exercise-title-text" id="active-ex-name">
                                Chưa có bài tập
                            </h5>

                            <!-- 4. [SET / REP HOẶC THỜI LƯỢNG] -->
                            <div class="text-muted fw-semibold fs-7 mb-2" id="active-ex-set-rep">-- x --</div>

                            <!-- Thông báo bài tập / Empty state nhỏ trong player nếu rỗng -->
                            <div id="exercise-empty-banner" class="alert alert-light border border-dashed rounded-3 py-1 px-3 mb-2 d-none text-muted fs-8" style="max-width: 460px;">
                                <i class="bi bi-info-circle me-1 text-primary"></i> Buổi tập chưa có danh sách bài tập. Bạn có thể bấm <strong>"Thêm bài tập"</strong> hoặc bấm <strong>"Bắt đầu tập"</strong> để tính giờ tập tự do.
                            </div>

                            <!-- 5 & 6. BỘ NÚT ĐIỀU KHIỂN BUỔI TẬP HIỆN ĐẠI & GỌN GÀNG -->
                            <div class="d-flex flex-wrap gap-2 justify-content-center w-100 mt-1" style="max-width: 520px;">
                                <!-- Nút Bắt đầu tập / Tạm dừng / Tiếp tục -->
                                <button class="btn btn-primary px-3.5 py-2 rounded-pill fw-bold shadow-xs d-inline-flex align-items-center gap-1.5" id="toggle-timer-btn">
                                    <i class="bi bi-play-fill fs-5" id="toggle-timer-icon"></i>
                                    <span id="toggle-timer-text">Bắt đầu tập</span>
                                </button>

                                <!-- Nút Bài tiếp theo -->
                                <button class="btn btn-outline-secondary px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5" id="next-ex-btn">
                                    <i class="bi bi-skip-forward-fill"></i>
                                    <span>Bài tiếp theo</span>
                                </button>

                                <!-- Nút Hoàn thành buổi tập -->
                                <button class="btn btn-success px-3.5 py-2 rounded-pill fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-white" id="finish-ex-btn">
                                    <i class="bi bi-check2-circle fs-6"></i>
                                    <span>Hoàn thành</span>
                                </button>

                                <!-- Nút Thêm bài tập (Mở Modal) -->
                                <button class="btn btn-light border rounded-pill px-3 py-2 fw-semibold text-secondary d-inline-flex align-items-center gap-1.5"
                                    data-bs-toggle="modal" data-bs-target="#editWorkoutModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Thêm bài tập</span>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- CỘT PHẢI: LỊCH TẬP & HOẠT ĐỘNG GẦN ĐÂY (COMPACT RIGHT PANEL) -->
                <div class="col-12 col-xl-4 d-flex flex-column gap-3">

                    <!-- CARD: LỊCH TẬP TRONG THÁNG (HEATMAP THẬT TỪ DATABASE) -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Lịch tập trong tháng</h6>
                                <small class="text-muted fw-medium fs-8" id="calendar-month-title">Tháng {{ $currentMonth }}, {{ $currentYear }}</small>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-light border rounded-circle p-1 px-2" id="prev-month-btn" title="Tháng trước">
                                    <i class="bi bi-chevron-left fs-8"></i>
                                </button>
                                <button class="btn btn-sm btn-light border rounded-circle p-1 px-2" id="next-month-btn" title="Tháng sau">
                                    <i class="bi bi-chevron-right fs-8"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Grid 7 ngày trong tuần -->
                        <div class="calendar-heatmap-grid" id="calendar-grid">
                            <div class="text-center text-muted fw-semibold fs-8">T2</div>
                            <div class="text-center text-muted fw-semibold fs-8">T3</div>
                            <div class="text-center text-muted fw-semibold fs-8">T4</div>
                            <div class="text-center text-muted fw-semibold fs-8">T5</div>
                            <div class="text-center text-muted fw-semibold fs-8">T6</div>
                            <div class="text-center text-muted fw-semibold fs-8">T7</div>
                            <div class="text-center text-muted fw-semibold fs-8">CN</div>
                        </div>

                        <!-- Chú thích mức độ tập (Legend) -->
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <span class="text-muted fs-8">Ít</span>
                            <div class="d-flex gap-1 align-items-center">
                                <div class="rounded-1 legend-box" style="background-color: #f1f5f9;" title="Nghỉ / Chưa tập"></div>
                                <div class="rounded-1 legend-box" style="background-color: #c7d2fe;" title="Nhẹ (1-20p)"></div>
                                <div class="rounded-1 legend-box" style="background-color: #818cf8;" title="Vừa (20-40p)"></div>
                                <div class="rounded-1 legend-box" style="background-color: #6366f1;" title="Nhiều (40-60p)"></div>
                                <div class="rounded-1 legend-box" style="background-color: #4338ca;" title="Rất nhiều (>60p)"></div>
                            </div>
                            <span class="text-muted fs-8">Nhiều</span>
                        </div>
                    </div>

                    <!-- CARD: HOẠT ĐỘNG GẦN ĐÂY (DỮ LIỆU THẬT & EMPTY STATE CHUẨN) -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 flex-grow-1" id="recent-activities-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0">Hoạt động gần đây</h6>
                            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-0.5 fs-8">Lịch sử</span>
                        </div>

                        <!-- Vùng hiển thị danh sách hoặc Empty State -->
                        <div id="recent-activities-container">
                            @if($hoatDongGanDay->isEmpty())
                                <!-- EMPTY STATE -->
                                <div class="text-center py-3 px-2 empty-activity-box">
                                    <div class="empty-icon-wrapper mb-2">
                                        <i class="bi bi-clock-history fs-3 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-7">Chưa có hoạt động gần đây</h6>
                                    <p class="text-muted fs-8 mb-2">Hãy bắt đầu buổi tập đầu tiên để lịch sử của bạn xuất hiện tại đây.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-8" id="quick-start-workout-btn">
                                        <i class="bi bi-play-fill me-1"></i>Bắt đầu tập ngay
                                    </button>
                                </div>
                            @else
                                <!-- DANH SÁCH LỊCH SỬ THẬT TỪ DATABASE -->
                                <div class="d-flex flex-column gap-1.5" id="history-items-list">
                                    @foreach($hoatDongGanDay as $hoatDong)
                                    <div class="d-flex align-items-center justify-content-between p-1.5 rounded-3 border-bottom activity-record-item">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                                            <div class="metric-icon-box bg-{{ $hoatDong['color'] }}-subtle text-{{ $hoatDong['color'] }}">
                                                <i class="bi {{ $hoatDong['icon'] }}"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold text-dark fs-7 text-truncate">{{ $hoatDong['loai'] }}</div>
                                                <small class="text-muted fs-8 text-truncate d-block">{{ $hoatDong['thoi_gian'] }} • {{ $hoatDong['so_bai'] }}</small>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <span class="badge bg-light text-dark border rounded-pill fs-8">{{ $hoatDong['thoi_diem'] }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

            <!-- 5. CARD: CÁC CHỈ SỐ SỨC KHỎE (COMPACT BOTTOM SECTION) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-2">
                <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0 fs-7">
                        <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Chỉ số sức khỏe & sinh trắc
                    </h6>
                    <small class="text-muted fs-8">Cập nhật theo thiết bị / hồ sơ</small>
                </div>

                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center gap-2.5">
                            <div class="metric-icon-box bg-danger-subtle text-danger">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold fs-8">Nhịp tim trung bình</small>
                                <div class="fw-bold text-dark fs-7">-- <span class="fs-9 fw-normal text-muted">bpm</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center gap-2.5">
                            <div class="metric-icon-box bg-success-subtle text-success">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold fs-8">Cân nặng</small>
                                <div class="fw-bold text-dark fs-7">-- <span class="fs-9 fw-normal text-muted">kg</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center gap-2.5">
                            <div class="metric-icon-box bg-warning-subtle text-warning">
                                <i class="bi bi-person-standing"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold fs-8">Tỷ lệ mỡ</small>
                                <div class="fw-bold text-dark fs-7">-- <span class="fs-9 fw-normal text-muted">%</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center gap-2.5">
                            <div class="metric-icon-box bg-info-subtle text-info">
                                <i class="bi bi-droplet-half"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold fs-8">Lượng nước</small>
                                <div class="fw-bold text-dark fs-7">-- <span class="fs-9 fw-normal text-muted">lít</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
</body>

</html>
