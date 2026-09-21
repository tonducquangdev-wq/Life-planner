<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Planner - Lịch Cá Nhân (Calendar First)</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Tùy chỉnh Dashboard & Calendar -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
</head>
<body>

    <!-- ==========================================================================
         1. SIDEBAR BÊN TRÁI (LEFT SIDEBAR - CHỈ 4 MENU CHÍNH)
         ========================================================================== -->
    @include('layouts.sidebar')

    <!-- ==========================================================================
         MAIN WRAPPER (HEADER & NỘI DUNG CHÍNH)
         ========================================================================== -->
    <div class="main-wrapper">

        <!-- ==========================================================================
             2. HEADER THANH CÔNG CỤ TRÊN CÙNG
             ========================================================================== -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <!-- Nút bật/tắt Sidebar trên mobile -->
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Thanh tìm kiếm nhanh -->
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm lịch học, deadline, sự kiện...">
                </div>
            </div>

            <!-- Khu vực người dùng & thông báo -->
            <div class="header-actions">
                <!-- Nút thông báo -->
                <div class="notification-btn" title="Thông báo">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge-dot"></span>
                </div>

                <!-- Avatar người dùng & dropdown -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            <div class="user-avatar">
                                {{ Auth::check() ? Auth::user()->initials : 'TQ' }}
                            </div>
                        @endif
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'Tôn Đức Quang' }}</div>
                            <small class="text-muted fs-7">{{ Auth::user()->email ?? 'quang@gmail.com' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i>Hồ sơ cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- ==========================================================================
             3. NỘI DUNG CHÍNH (CALENDAR FIRST - MÀN HÌNH TẬP TRUNG CHÍNH)
             ========================================================================== -->
        <main class="content-body">
            
            <!-- Tiêu đề trang & Điều hướng Tháng / Tạo sự kiện -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Lịch cá nhân & Lịch trình 🗓️</h4>
                    <p class="text-muted mb-0 fs-7">Trung tâm quản lý duy nhất toàn bộ Lịch học, Lịch tập, Deadline và Sự kiện cá nhân của bạn.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 fs-7 fw-semibold bg-white shadow-sm" id="btnToday">
                        <i class="bi bi-calendar-event me-1"></i>Hôm nay
                    </button>
                    <button class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 fs-7 fw-semibold" data-bs-toggle="modal" data-bs-target="#createEventModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>+ Tạo sự kiện mới</span>
                    </button>
                </div>
            </div>

            <!-- BỐ CỤC 2 CỘT (TRÁI: LỊCH THÁNG LỚN 75% | PHẢI: CHI TIẾT & THỐNG KÊ 25%) -->
            <div class="row g-4">
                
                <!-- ==========================================================================
                     CỘT TRÁI (75%): LỊCH THÁNG LỚN (MONTHLY CALENDAR GRID)
                     ========================================================================== -->
                <div class="col-lg-8 col-xl-9">
                    <div class="custom-card mb-0">
                        <!-- HEADER CARD LỊCH: THÁNG & NÚT LỌC MÀU SỰ KIỆN -->
                        <div class="card-header-custom flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                    <span id="currentMonthTitle">{{ $currentMonthYear ?? 'Tháng 9, 2026' }}</span>
                                </h5>
                                <div class="btn-group border rounded-pill p-1 bg-light">
                                    <button class="btn btn-sm btn-white rounded-circle shadow-none py-0 px-2" id="btnPrevMonth" title="Tháng trước">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button class="btn btn-sm btn-white rounded-circle shadow-none py-0 px-2" id="btnNextMonth" title="Tháng sau">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- NÚT LỌC MÀU SỰ KIỆN ĐA LỰA CHỌN (MULTI-SELECT FILTER) -->
                            <div class="d-flex flex-wrap align-items-center gap-2" id="calendarFilterGroup">
                                <button type="button" class="legend-btn active" data-filter="all">
                                    <i class="bi bi-grid-fill me-1 fs-8"></i>Tất cả
                                </button>
                                <button type="button" class="legend-btn hoc-tap" data-filter="hoc-tap">
                                    <span class="legend-dot"></span>📘 Học tập
                                </button>
                                <button type="button" class="legend-btn tap-luyen" data-filter="tap-luyen">
                                    <span class="legend-dot"></span>🏋️ Tập luyện
                                </button>
                                <button type="button" class="legend-btn deadline" data-filter="deadline">
                                    <span class="legend-dot"></span>⏰ Deadline
                                </button>
                                <button type="button" class="legend-btn ca-nhan" data-filter="ca-nhan">
                                    <span class="legend-dot"></span>🎉 Cá nhân
                                </button>
                            </div>
                        </div>

                        <!-- LƯỚI LỊCH THÁNG LỚN (7 CỘT: CN -> T7) -->
                        <div class="card-body p-3">
                            <div class="calendar-grid-container">
                                <!-- Hàng 0: Tiêu đề các thứ trong tuần (CN -> T7) -->
                                <div class="calendar-header-day weekend">CN</div>
                                <div class="calendar-header-day">T2</div>
                                <div class="calendar-header-day">T3</div>
                                <div class="calendar-header-day">T4</div>
                                <div class="calendar-header-day">T5</div>
                                <div class="calendar-header-day">T6</div>
                                <div class="calendar-header-day weekend">T7</div>

                                <!-- Ô các ngày trong tháng (Dữ liệu Demo đầy đủ 4 loại sự kiện) -->
                                @php
                                    $demoDays = [
                                        ['day' => 31, 'is_current_month' => false, 'is_today' => false, 'events' => []],
                                        ['day' => 1,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
                                        ]],
                                        ['day' => 2,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => 'Nghỉ lễ Quốc Khánh', 'type' => 'ca-nhan', 'time' => 'Cả ngày'],
                                        ]],
                                        ['day' => 3,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '14:00 Cơ sở dữ liệu', 'type' => 'hoc-tap', 'time' => '14:00'],
                                            ['title' => '18:00 Ngực Vai Tay Sau', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 4,  'is_current_month' => true,  'is_today' => false, 'events' => []],
                                        ['day' => 5,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '09:00 Chạy bộ 5km', 'type' => 'tap-luyen', 'time' => '09:00'],
                                        ]],
                                        ['day' => 6,  'is_current_month' => true,  'is_today' => false, 'events' => []],
                                        
                                        // Hàng 2
                                        ['day' => 7,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
                                        ]],
                                        ['day' => 8,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '18:00 Lưng Tay Trước', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 9,  'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '23:59 Nộp đồ án PHP', 'type' => 'deadline', 'time' => '23:59'],
                                        ]],
                                        ['day' => 10, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '13:30 Kiểm thử phần mềm', 'type' => 'hoc-tap', 'time' => '13:30'],
                                        ]],
                                        ['day' => 11, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '18:00 Tập Chân', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 12, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => 'Sinh nhật bạn', 'type' => 'ca-nhan', 'time' => '19:00'],
                                        ]],
                                        ['day' => 13, 'is_current_month' => true,  'is_today' => false, 'events' => []],

                                        // Hàng 3 (HÔM NAY - Ngày 14)
                                        ['day' => 14, 'is_current_month' => true,  'is_today' => true,  'events' => [
                                            ['title' => '08:00 Lập trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
                                            ['title' => '14:00 Họp nhóm Đồ án', 'type' => 'ca-nhan', 'time' => '14:00'],
                                            ['title' => '18:00 Ngực Vai Tay Sau', 'type' => 'tap-luyen', 'time' => '18:00'],
                                            ['title' => '23:59 Nộp Báo cáo Lab', 'type' => 'deadline', 'time' => '23:59'],
                                            ['title' => '21:00 Đọc sách Clean Code', 'type' => 'ca-nhan', 'time' => '21:00'],
                                        ]],
                                        ['day' => 15, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '10:00 Học Tiếng Anh', 'type' => 'hoc-tap', 'time' => '10:00'],
                                        ]],
                                        ['day' => 16, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '23:59 Nộp bài CSDL', 'type' => 'deadline', 'time' => '23:59'],
                                            ['title' => '18:00 Tập Cardio', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 17, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '08:00 CSDL Nâng cao', 'type' => 'hoc-tap', 'time' => '08:00'],
                                        ]],
                                        ['day' => 18, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '23:59 Nộp Báo cáo Giữa Kỳ', 'type' => 'deadline', 'time' => '23:59'],
                                        ]],
                                        ['day' => 19, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '17:00 Đá bóng giao hữu', 'type' => 'tap-luyen', 'time' => '17:00'],
                                        ]],
                                        ['day' => 20, 'is_current_month' => true,  'is_today' => false, 'events' => []],

                                        // Hàng 4
                                        ['day' => 21, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '08:00 Thi giữa kỳ KTPM', 'type' => 'deadline', 'time' => '08:00'],
                                        ]],
                                        ['day' => 22, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '18:00 FullBody Gym', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 23, 'is_current_month' => true,  'is_today' => false, 'events' => []],
                                        ['day' => 24, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '14:00 Workshop AI', 'type' => 'hoc-tap', 'time' => '14:00'],
                                        ]],
                                        ['day' => 25, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '18:00 Tập Yoga', 'type' => 'tap-luyen', 'time' => '18:00'],
                                        ]],
                                        ['day' => 26, 'is_current_month' => true,  'is_today' => false, 'events' => []],
                                        ['day' => 27, 'is_current_month' => true,  'is_today' => false, 'events' => []],

                                        // Hàng 5
                                        ['day' => 28, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => '08:00 Thuyết trình Web', 'type' => 'hoc-tap', 'time' => '08:00'],
                                        ]],
                                        ['day' => 29, 'is_current_month' => true,  'is_today' => false, 'events' => []],
                                        ['day' => 30, 'is_current_month' => true,  'is_today' => false, 'events' => [
                                            ['title' => 'Tổng kết Tháng 9', 'type' => 'ca-nhan', 'time' => '20:00'],
                                        ]],
                                        ['day' => 1,  'is_current_month' => false, 'is_today' => false, 'events' => []],
                                        ['day' => 2,  'is_current_month' => false, 'is_today' => false, 'events' => []],
                                        ['day' => 3,  'is_current_month' => false, 'is_today' => false, 'events' => []],
                                        ['day' => 4,  'is_current_month' => false, 'is_today' => false, 'events' => []],
                                    ];
                                @endphp

                                @foreach($calendarDays ?? $demoDays as $dayData)
                                    <div class="calendar-day-cell {{ !$dayData['is_current_month'] ? 'other-month' : '' }} {{ $dayData['is_today'] ? 'is-today' : '' }}" data-day="{{ $dayData['day'] }}" data-current-month="{{ $dayData['is_current_month'] ? '1' : '0' }}">
                                        <!-- Tiêu đề ngày -->
                                        <div class="day-header">
                                            <span class="day-number">{{ $dayData['day'] }}</span>
                                            @if($dayData['is_today'])
                                                <span class="today-tag">Hôm nay</span>
                                            @endif
                                        </div>

                                        <!-- Danh sách sự kiện trong ô -->
                                        <div class="event-pill-list" data-day-events>
                                            @foreach($dayData['events'] as $evt)
                                                <div class="event-pill {{ $evt['type'] }}" data-type="{{ $evt['type'] }}" title="{{ $evt['title'] }} ({{ $evt['time'] }})">
                                                    @if($evt['type'] === 'hoc-tap')
                                                        <i class="bi bi-book-fill fs-8"></i>
                                                    @elseif($evt['type'] === 'tap-luyen')
                                                        <i class="bi bi-activity fs-8"></i>
                                                    @elseif($evt['type'] === 'deadline')
                                                        <i class="bi bi-exclamation-triangle-fill fs-8"></i>
                                                    @else
                                                        <i class="bi bi-person-fill fs-8"></i>
                                                    @endif
                                                    <span>{{ $evt['title'] }}</span>
                                                </div>
                                            @endforeach

                                            <!-- Thẻ ẩn hiển thị số lượng phụ +X khác -->
                                            <div class="event-more-pill d-none"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================================================
                     CỘT PHẢI (25%): TÓM TẮT LỊCH TRÌNH & THỐNG KÊ NHANH
                     ========================================================================== -->
                <div class="col-lg-4 col-xl-3">
                    
                    <!-- CARD 1: LỊCH TRÌNH HÔM NAY -->
                    <div class="side-card">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Lịch trình hôm nay</span>
                            </h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-7">3</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="schedule-item hoc-tap" data-type="hoc-tap">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-dark fs-7">08:00 - 10:30</span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0 fs-8">Học tập</span>
                                </div>
                                <div class="fw-semibold text-dark fs-6 mb-1">Lập trình Web & Laravel 13</div>
                                <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>Phòng B2.04</small>
                            </div>

                            <div class="schedule-item ca-nhan" data-type="ca-nhan">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-dark fs-7">14:00 - 15:30</span>
                                    <span class="badge bg-purple-subtle text-purple rounded-pill px-2 py-0 fs-8" style="background-color:#faf5ff; color:#7e22ce;">Cá nhân</span>
                                </div>
                                <div class="fw-semibold text-dark fs-6 mb-1">Họp nhóm Đồ án Life Planner</div>
                                <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>Google Meet</small>
                            </div>

                            <div class="schedule-item tap-luyen" data-type="tap-luyen">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-dark fs-7">18:00 - 19:30</span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0 fs-8">Tập luyện</span>
                                </div>
                                <div class="fw-semibold text-dark fs-6 mb-1">Tập Gym - Ngực Vai Tay Sau</div>
                                <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>Fitness Center</small>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: DEADLINE SẮP ĐẾN HẠN -->
                    <div class="side-card">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-hourglass-split text-warning"></i>
                                <span>Sắp đến hạn</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-3 bg-light">
                                <div>
                                    <div class="fw-bold text-dark fs-7">Nộp đồ án PHP</div>
                                    <small class="text-muted fs-8">Lập trình Web nâng cao</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fs-8 fw-bold">Còn 2 ngày</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-3 bg-light">
                                <div>
                                    <div class="fw-bold text-dark fs-7">Báo cáo CSDL MySQL</div>
                                    <small class="text-muted fs-8">Cơ sở dữ liệu</small>
                                </div>
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 fs-8 fw-bold">Còn 4 ngày</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

    <!-- ==========================================================================
         4. BOOTSTRAP MODAL TẠO SỰ KIỆN / LỊCH TRÌNH (4 TAB CHUẨN)
         ========================================================================== -->
    <div class="modal fade modal-custom" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="createEventModalLabel">
                        <i class="bi bi-calendar-plus text-primary"></i>
                        <span>Thêm mới vào Lịch</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- THANH 4 TAB PHÂN LOẠI CÓ MÀU TƯƠNG ỨNG -->
                <div class="px-4 pt-3 bg-light border-bottom">
                    <ul class="nav nav-pills gap-2" id="eventTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active tab-hoc-tap" id="hoc-tap-tab" data-bs-toggle="tab" data-bs-target="#tab-hoc-tap" type="button" role="tab">
                                📘 Lịch học
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-tap-luyen" id="tap-luyen-tab" data-bs-toggle="tab" data-bs-target="#tab-tap-luyen" type="button" role="tab">
                                🏋️ Lịch tập
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-deadline" id="deadline-tab" data-bs-toggle="tab" data-bs-target="#tab-deadline" type="button" role="tab">
                                ⏰ Deadline
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-ca-nhan" id="ca-nhan-tab" data-bs-toggle="tab" data-bs-target="#tab-ca-nhan" type="button" role="tab">
                                🎉 Sự kiện cá nhân
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="modal-body p-4">
                    <div class="tab-content" id="eventTabContent">
                        
                        <!-- TAB 1: LỊCH HỌC (LẶP HÀNG TUẦN) -->
                        <div class="tab-pane fade show active" id="tab-hoc-tap" role="tabpanel">
                            <form id="formLichHoc">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Chọn môn học</label>
                                    <select class="form-select rounded-3">
                                        <option value="">-- Chọn Môn học --</option>
                                        <option value="1">Lập trình Web & Laravel 13</option>
                                        <option value="2">Cơ sở dữ liệu nâng cao</option>
                                        <option value="3">Kiến trúc phần mềm</option>
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thứ trong tuần</label>
                                        <select class="form-select rounded-3">
                                            <option value="1">Thứ 2</option>
                                            <option value="2">Thứ 3</option>
                                            <option value="3">Thứ 4</option>
                                            <option value="4">Thứ 5</option>
                                            <option value="5">Thứ 6</option>
                                            <option value="6">Thứ 7</option>
                                            <option value="7">Chủ Nhật</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ bắt đầu</label>
                                        <input type="time" class="form-control rounded-3" value="08:00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ kết thúc</label>
                                        <input type="time" class="form-control rounded-3" value="10:30">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Phòng học & Giảng viên (Ghi chú)</label>
                                    <input type="text" class="form-control rounded-3" placeholder="Ví dụ: Phòng B2.04 • Thầy Nguyễn Văn A">
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: LỊCH TẬP (LẶP HÀNG TUẦN) -->
                        <div class="tab-pane fade" id="tab-tap-luyen" role="tabpanel">
                            <form id="formLichTap">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tên buổi tập</label>
                                    <input type="text" class="form-control rounded-3" placeholder="Ví dụ: Ngực - Vai - Tay sau">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thứ tập luyện</label>
                                        <select class="form-select rounded-3">
                                            <option value="1">Thứ 2</option>
                                            <option value="2">Thứ 3</option>
                                            <option value="3">Thứ 4</option>
                                            <option value="4">Thứ 5</option>
                                            <option value="5">Thứ 6</option>
                                            <option value="6">Thứ 7</option>
                                            <option value="7">Chủ Nhật</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ tập dự kiến</label>
                                        <input type="time" class="form-control rounded-3" value="18:00">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 3: DEADLINE (MỐC THỜI GIAN 1 LẦN) -->
                        <div class="tab-pane fade" id="tab-deadline" role="tabpanel">
                            <form id="formDeadline">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tiêu đề bài tập / Deadline</label>
                                    <input type="text" class="form-control rounded-3" placeholder="Ví dụ: Nộp đồ án Laravel">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thuộc Môn học</label>
                                        <select class="form-select rounded-3">
                                            <option value="1">Lập trình Web & Laravel 13</option>
                                            <option value="2">Cơ sở dữ liệu nâng cao</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Hạn nộp (Ngày & Giờ)</label>
                                        <input type="datetime-local" class="form-control rounded-3">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 4: SỰ KIỆN CÁ NHÂN (MỐC THỜI GIAN 1 LẦN) -->
                        <div class="tab-pane fade" id="tab-ca-nhan" role="tabpanel">
                            <form id="formSuKien">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tên sự kiện</label>
                                    <input type="text" class="form-control rounded-3" placeholder="Ví dụ: Sinh nhật bạn, Họp nhóm, Đi khám bệnh">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thời gian bắt đầu</label>
                                        <input type="datetime-local" class="form-control rounded-3">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thời gian kết thúc</label>
                                        <input type="datetime-local" class="form-control rounded-3">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Lưu vào Lịch</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script tương tác Giao diện Calendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Toggle Sidebar Mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar')?.classList.toggle('show');
            });

            // 2. Bộ lọc sự kiện Lịch theo Nút đa lựa chọn (Multi-select Legend Filter Buttons)
            const allBtn = document.querySelector('#calendarFilterGroup .legend-btn[data-filter="all"]');
            const categoryButtons = document.querySelectorAll('#calendarFilterGroup .legend-btn:not([data-filter="all"])');
            const filterContainer = document.getElementById('calendarFilterGroup');
            const dayCells = document.querySelectorAll('[data-day-events]');
            const scheduleItems = document.querySelectorAll('.schedule-item[data-type]');

            const selectedCategories = new Set();
            const allCategoryTypes = Array.from(categoryButtons).map(btn => btn.getAttribute('data-filter'));

            function updateDayCellEvents(cell) {
                const events = cell.querySelectorAll('.event-pill');
                const morePill = cell.querySelector('.event-more-pill');
                let totalMatching = 0;

                events.forEach(evt => {
                    const evtType = evt.getAttribute('data-type');
                    const matches = (selectedCategories.size === 0 || selectedCategories.has(evtType));

                    if (matches) {
                        totalMatching++;
                        if (totalMatching <= 3) {
                            evt.style.display = 'flex';
                        } else {
                            evt.style.display = 'none';
                        }
                    } else {
                        evt.style.display = 'none';
                    }
                });

                if (morePill) {
                    if (totalMatching > 3) {
                        morePill.textContent = `+${totalMatching - 3} khác`;
                        morePill.classList.remove('d-none');
                        morePill.style.display = 'block';
                    } else {
                        morePill.classList.add('d-none');
                        morePill.style.display = 'none';
                    }
                }
            }

            function applyFilters() {
                if (selectedCategories.size === 0 || selectedCategories.size === allCategoryTypes.length) {
                    allBtn?.classList.add('active');
                    filterContainer?.classList.remove('filter-active');
                } else {
                    allBtn?.classList.remove('active');
                    filterContainer?.classList.add('filter-active');
                }

                categoryButtons.forEach(btn => {
                    const cat = btn.getAttribute('data-filter');
                    if (selectedCategories.has(cat)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                dayCells.forEach(cell => {
                    updateDayCellEvents(cell);
                });

                scheduleItems.forEach(item => {
                    const itemType = item.getAttribute('data-type');
                    if (selectedCategories.size === 0 || selectedCategories.has(itemType)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            allBtn?.addEventListener('click', function() {
                selectedCategories.clear();
                applyFilters();
            });

            categoryButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const cat = this.getAttribute('data-filter');
                    if (selectedCategories.has(cat)) {
                        selectedCategories.delete(cat);
                    } else {
                        selectedCategories.add(cat);
                    }
                    applyFilters();
                });
            });

            // 3. Click vào ô ngày trên Lịch -> Mở Modal tạo mới
            const modalElement = document.getElementById('createEventModal');
            const bsModal = modalElement ? new bootstrap.Modal(modalElement) : null;

            document.querySelectorAll('.calendar-day-cell').forEach(cell => {
                cell.addEventListener('click', function(e) {
                    // Nếu click trực tiếp vào ngày (không phải xem sự kiện), mở Modal
                    if (!e.target.closest('.event-pill')) {
                        bsModal?.show();
                    }
                });
            });

            applyFilters();
        });
    </script>
</body>
</html>
