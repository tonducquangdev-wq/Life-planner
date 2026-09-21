<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Planner - Lịch Cá Nhân (Calendar First)</title>

    <!-- Bootstrap 5.3 CSS -->
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
         1. SIDEBAR BÊN TRÁI (DESKTOP FIXED & MOBILE OFFCANVAS)
         ========================================================================== -->
    @include('layouts.sidebar')

    <!-- ==========================================================================
         MAIN WRAPPER (HEADER & NỘI DUNG CHÍNH)
         ========================================================================== -->
    <div class="main-wrapper">

        <!-- ==========================================================================
             2. HEADER THANH CÔNG CỤ TRÊN CÙNG
             ========================================================================== -->
        <header class="top-header border-bottom bg-white px-3 px-lg-4 py-2">
            <div class="d-flex align-items-center gap-3">
                <!-- Nút bật/tắt Sidebar Offcanvas trên Mobile & Tablet (< 992px) -->
                <button class="btn btn-light d-lg-none p-1 px-2 border rounded-3" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>

                <!-- Thanh tìm kiếm nhanh -->
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control rounded-pill border-0 bg-light" id="searchCalendar" placeholder="Tìm kiếm lịch học, deadline, sự kiện...">
                </div>
            </div>

            <!-- Khu vực người dùng & nút Thông báo -->
            <div class="header-actions d-flex align-items-center gap-3">

                <!-- Nút Thông báo & Dropdown Menu -->
                <div class="notification-dropdown dropdown">
                    <a href="#" class="notification-btn position-relative text-dark text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo" id="notificationMenuBtn">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span class="badge-dot" id="notifBadgeDot"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="width: 360px; max-width: 90vw;">
                        <!-- Header Dropdown -->
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light rounded-top-4">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-bell text-primary"></i>Thông báo
                                <span class="badge bg-danger rounded-pill fs-8" id="notifCountBadge">3 mới</span>
                            </h6>
                            <button type="button" class="btn btn-link text-decoration-none p-0 fs-8 text-primary fw-semibold" id="btnMarkAllRead">Đánh dấu đã đọc</button>
                        </div>

                        <!-- Danh sách thông báo thực tế của Life Planner -->
                        <div class="notification-list p-2" style="max-height: 340px; overflow-y: auto;">
                            <!-- 1. Deadline gấp -->
                            <a href="#" class="notification-item unread d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none text-dark mb-1">
                                <div class="notif-icon bg-danger-subtle text-danger rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold fs-7 text-dark text-truncate">Deadline Nộp đồ án PHP & Laravel</div>
                                    <div class="text-muted fs-8">Hạn nộp: 23:59 hôm nay (Còn 2 giờ nữa)</div>
                                    <small class="text-primary fs-8 fw-semibold">10 phút trước</small>
                                </div>
                                <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 7px; height: 7px;"></span>
                            </a>

                            <!-- 2. Lịch học sắp tới -->
                            <a href="#" class="notification-item unread d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none text-dark mb-1">
                                <div class="notif-icon bg-primary-subtle text-primary rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-book-fill fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold fs-7 text-dark text-truncate">Sắp diễn ra: Lập trình Web</div>
                                    <div class="text-muted fs-8">08:00 - 10:30 • Phòng B2.04</div>
                                    <small class="text-primary fs-8 fw-semibold">30 phút trước</small>
                                </div>
                                <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 7px; height: 7px;"></span>
                            </a>

                            <!-- 3. Lịch tập sắp tới -->
                            <a href="#" class="notification-item unread d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none text-dark mb-1">
                                <div class="notif-icon bg-success-subtle text-success rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-activity fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold fs-7 text-dark text-truncate">Sắp diễn ra: Ngực - Vai - Tay sau</div>
                                    <div class="text-muted fs-8">18:00 - 19:30 • Fitness Center</div>
                                    <small class="text-muted fs-8">1 giờ trước</small>
                                </div>
                            </a>

                            <!-- 4. Sự kiện cá nhân -->
                            <a href="#" class="notification-item read d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none text-dark opacity-75">
                                <div class="notif-icon bg-purple-subtle text-purple rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #faf5ff; color: #7e22ce;">
                                    <i class="bi bi-person-fill fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold fs-7 text-dark text-truncate">Họp nhóm Đồ án Life Planner</div>
                                    <div class="text-muted fs-8">14:00 - 15:30 • Google Meet</div>
                                    <small class="text-muted fs-8">Hôm qua</small>
                                </div>
                            </a>
                        </div>
                    </div>
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
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
             3. NỘI DUNG CHÍNH (CALENDAR FIRST - FULL RESPONSIVE)
             ========================================================================== -->
        <main class="content-body">
            <!-- BỐ CỤC RESPONSIVE: DESKTOP/LAPTOP (75%/25%) | TABLET/MOBILE (100% STACKED) -->
            <div class="row g-4">

                <!-- CỘT TRÁI (75% DESKTOP | 100% TABLET/MOBILE): LỊCH THÁNG LỚN -->
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="custom-card mb-0 h-100 border-0 shadow-sm rounded-4">
                        <!-- HEADER CARD LỊCH: THÁNG & NÚT LỌC MÀU SỰ KIỆN -->
                        <div class="card-header-custom flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                    <span id="currentMonthTitle">Tháng 9, 2026</span>
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

                            <!-- NÚT LỌC MÀU SỰ KIỆN ĐA LỰA CHỌN -->
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

                        <!-- LƯỚI LỊCH THÁNG LỚN (7 CỘT) -->
                        <div class="card-body p-2 p-md-3">
                            <div class="calendar-grid-container" id="calendarGrid">
                                <!-- Hàng 0: Header thứ (CN -> T7) -->
                                <div class="calendar-header-day weekend">CN</div>
                                <div class="calendar-header-day">T2</div>
                                <div class="calendar-header-day">T3</div>
                                <div class="calendar-header-day">T4</div>
                                <div class="calendar-header-day">T5</div>
                                <div class="calendar-header-day">T6</div>
                                <div class="calendar-header-day weekend">T7</div>

                                <!-- Dynamic render via calendar.js -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI (25% DESKTOP | 100% TABLET/MOBILE CHUYỂN XUỐNG DƯỚI): LỊCH TRÌNH & DEADLINE -->
                <div class="col-12 col-lg-4 col-xl-3">

                    <!-- CARD 1: LỊCH TRÌNH HÔM NAY -->
                    <div class="side-card rounded-4 border-0 shadow-sm mb-3">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Lịch trình hôm nay</span>
                            </h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-7" id="todayCountBadge">0</span>
                        </div>
                        <div class="card-body p-3" id="todayScheduleList">
                            <!-- JS render via calendar.js -->
                        </div>
                    </div>

                    <!-- CARD 2: DEADLINE SẮP ĐẾN HẠN -->
                    <div class="side-card rounded-4 border-0 shadow-sm">
                        <div class="side-card-header">
                            <h6 class="side-card-title">
                                <i class="bi bi-hourglass-split text-warning"></i>
                                <span>Deadline sắp tới</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between p-2.5 mb-2 rounded-3 bg-light border-start border-warning border-3">
                                <div>
                                    <div class="fw-bold text-dark fs-7">Nộp đồ án PHP & Laravel</div>
                                    <small class="text-muted fs-8">Hạn: 23:59 • Ngày 14/09</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fs-8 fw-bold">Hôm nay</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-2.5 mb-2 rounded-3 bg-light border-start border-warning border-3">
                                <div>
                                    <div class="fw-bold text-dark fs-7">Nộp bài CSDL MySQL</div>
                                    <small class="text-muted fs-8">Hạn: 23:59 • Ngày 16/09</small>
                                </div>
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 fs-8 fw-bold">Còn 2 ngày</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

    <!-- ==========================================================================
         4. BOOTSTRAP MODAL CHI TIẾT NGÀY (DAY DETAIL MODAL)
         ========================================================================== -->
    <div class="modal fade modal-custom" id="dayDetailModal" tabindex="-1" aria-labelledby="dayDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="dayDetailModalLabel">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span id="selectedDateTitle">Chi tiết Lịch Ngày 14/09/2026</span>
                        </h5>
                        <small class="text-muted fs-7" id="selectedDateSubtitle">Hiển thị toàn bộ sự kiện sắp xếp theo thứ tự thời gian</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary rounded-pill btn-sm px-3 fw-semibold" id="btnAddNewFromDayModal">
                            <i class="bi bi-plus-lg me-1"></i>Thêm sự kiện
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div id="dayEventsDetailList">
                        <!-- Dynamic render via calendar.js -->
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-2 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         5. BOOTSTRAP MODAL TẠO SỰ KIỆN MỚI (CREATE EVENT MODAL - 4 TAB)
         ========================================================================== -->
    <div class="modal fade modal-custom" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
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

                        <!-- TAB 1: LỊCH HỌC -->
                        <div class="tab-pane fade show active" id="tab-hoc-tap" role="tabpanel">
                            <form id="formCreateHocTap">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tên môn học</label>
                                    <input type="text" class="form-control rounded-3" id="createHocTapTitle" placeholder="Ví dụ: Lập trình Web & Laravel 13" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Ngày diễn ra</label>
                                        <select class="form-select rounded-3" id="createHocTapDay">
                                            @for($i=1; $i<=30; $i++)
                                                <option value="{{ $i }}" {{ $i == 14 ? 'selected' : '' }}>Ngày {{ $i }}/09/2026</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ bắt đầu</label>
                                        <input type="time" class="form-control rounded-3" id="createHocTapTime" value="08:00" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ kết thúc</label>
                                        <input type="time" class="form-control rounded-3" id="createHocTapEndTime" value="10:30" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Quy tắc lặp</label>
                                        <select class="form-select rounded-3" id="createHocTapRepeat">
                                            <option value="weekly" selected>🔄 Lặp hàng tuần</option>
                                            <option value="daily">📅 Lặp hàng ngày</option>
                                            <option value="monthly">📆 Lặp hàng tháng</option>
                                            <option value="once">📌 Sự kiện 1 lần</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Phòng học & Giảng viên</label>
                                    <input type="text" class="form-control rounded-3" id="createHocTapLocation" placeholder="Ví dụ: Phòng B2.04 • Thầy Nguyễn Văn A">
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: LỊCH TẬP -->
                        <div class="tab-pane fade" id="tab-tap-luyen" role="tabpanel">
                            <form id="formCreateTapLuyen">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tên buổi tập</label>
                                    <input type="text" class="form-control rounded-3" id="createTapLuyenTitle" placeholder="Ví dụ: Ngực - Vai - Tay sau" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Ngày bắt đầu tập</label>
                                        <select class="form-select rounded-3" id="createTapLuyenDay">
                                            @for($i=1; $i<=30; $i++)
                                                <option value="{{ $i }}" {{ $i == 14 ? 'selected' : '' }}>Ngày {{ $i }}/09/2026</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ bắt đầu</label>
                                        <input type="time" class="form-control rounded-3" id="createTapLuyenTime" value="18:00" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ kết thúc</label>
                                        <input type="time" class="form-control rounded-3" id="createTapLuyenEndTime" value="19:30" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Quy tắc lặp</label>
                                        <select class="form-select rounded-3" id="createTapLuyenRepeat">
                                            <option value="weekly" selected>🔄 Lặp hàng tuần</option>
                                            <option value="daily">📅 Lặp hàng ngày</option>
                                            <option value="monthly">📆 Lặp hàng tháng</option>
                                            <option value="once">📌 Sự kiện 1 lần</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Địa điểm tập / Ghi chú</label>
                                    <input type="text" class="form-control rounded-3" id="createTapLuyenLocation" placeholder="Ví dụ: Fitness Center • 4 hiệp Bench Press">
                                </div>
                            </form>
                        </div>

                        <!-- TAB 3: DEADLINE -->
                        <div class="tab-pane fade" id="tab-deadline" role="tabpanel">
                            <form id="formCreateDeadline">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tiêu đề Deadline / Bài tập</label>
                                    <input type="text" class="form-control rounded-3" id="createDeadlineTitle" placeholder="Ví dụ: Nộp đồ án Laravel 13 Life Planner" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Ngày hạn nộp</label>
                                        <select class="form-select rounded-3" id="createDeadlineDay">
                                            @for($i=1; $i<=30; $i++)
                                                <option value="{{ $i }}" {{ $i == 14 ? 'selected' : '' }}>Ngày {{ $i }}/09/2026</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Giờ nộp</label>
                                        <input type="time" class="form-control rounded-3" id="createDeadlineTime" value="23:59" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Quy tắc lặp</label>
                                        <select class="form-select rounded-3" id="createDeadlineRepeat">
                                            <option value="once" selected>📌 Sự kiện 1 lần</option>
                                            <option value="weekly">🔄 Lặp hàng tuần</option>
                                            <option value="daily">📅 Lặp hàng ngày</option>
                                            <option value="monthly">📆 Lặp hàng tháng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Ghi chú deadline</label>
                                    <input type="text" class="form-control rounded-3" id="createDeadlineLocation" placeholder="Ví dụ: Nộp file .zip trên LMS">
                                </div>
                            </form>
                        </div>

                        <!-- TAB 4: SỰ KIỆN CÁ NHÂN -->
                        <div class="tab-pane fade" id="tab-ca-nhan" role="tabpanel">
                            <form id="formCreateCaNhan">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Tên sự kiện</label>
                                    <input type="text" class="form-control rounded-3" id="createCaNhanTitle" placeholder="Ví dụ: Họp nhóm Đồ án / Sinh nhật bạn" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Ngày diễn ra</label>
                                        <select class="form-select rounded-3" id="createCaNhanDay">
                                            @for($i=1; $i<=30; $i++)
                                                <option value="{{ $i }}" {{ $i == 14 ? 'selected' : '' }}>Ngày {{ $i }}/09/2026</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Thời gian</label>
                                        <input type="time" class="form-control rounded-3" id="createCaNhanTime" value="14:00" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 text-secondary">Quy tắc lặp</label>
                                        <select class="form-select rounded-3" id="createCaNhanRepeat">
                                            <option value="once" selected>📌 Sự kiện 1 lần</option>
                                            <option value="weekly">🔄 Lặp hàng tuần</option>
                                            <option value="daily">📅 Lặp hàng ngày</option>
                                            <option value="monthly">📆 Lặp hàng tháng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 text-secondary">Địa điểm / Ghi chú</label>
                                    <input type="text" class="form-control rounded-3" id="createCaNhanLocation" placeholder="Ví dụ: Google Meet / Quán Cafe A">
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSaveNewEvent">Lưu vào Lịch</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         6. BOOTSTRAP MODAL SỬA SỰ KIỆN (EDIT EVENT MODAL)
         ========================================================================== -->
    <div class="modal fade modal-custom" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editEventModalLabel">
                        <i class="bi bi-pencil-square text-primary"></i>
                        <span>Chỉnh sửa Sự kiện</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditEvent">
                        <input type="hidden" id="editEventId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Phân loại sự kiện</label>
                            <select class="form-select rounded-3" id="editEventType">
                                <option value="hoc-tap">📘 Học tập</option>
                                <option value="tap-luyen">🏋️ Tập luyện</option>
                                <option value="deadline">⏰ Deadline</option>
                                <option value="ca-nhan">🎉 Cá nhân</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Tiêu đề sự kiện</label>
                            <input type="text" class="form-control rounded-3" id="editEventTitle" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-secondary">Ngày</label>
                                <select class="form-select rounded-3" id="editEventDay">
                                    @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}">Ngày {{ $i }}/09/2026</option>
                                        @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-secondary">Giờ bắt đầu</label>
                                <input type="time" class="form-control rounded-3" id="editEventTime" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-secondary">Giờ kết thúc</label>
                                <input type="time" class="form-control rounded-3" id="editEventEndTime">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Quy tắc lặp</label>
                            <select class="form-select rounded-3" id="editEventRepeat">
                                <option value="weekly">🔄 Lặp hàng tuần</option>
                                <option value="daily">📅 Lặp hàng ngày</option>
                                <option value="monthly">📆 Lặp hàng tháng</option>
                                <option value="once">📌 Sự kiện 1 lần</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 text-secondary">Địa điểm / Ghi chú</label>
                            <input type="text" class="form-control rounded-3" id="editEventLocation">
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="btnUpdateEvent">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         7. BOOTSTRAP MODAL XÁC NHẬN XÓA BUỔI LẶP HOẶC TẤT CẢ
         ========================================================================== -->
    <div class="modal fade modal-custom" id="deleteEventChoiceModal" tabindex="-1" aria-labelledby="deleteEventChoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="deleteEventChoiceModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <span>Tùy chọn Xóa Sự kiện Lặp</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-secondary fs-6 mb-1">Sự kiện này thuộc chuỗi lặp lại hàng tuần/tháng.</p>
                    <div class="fw-bold text-dark fs-5 mb-4" id="deleteEventChoiceTitle">Tên sự kiện</div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger rounded-pill py-2.5 fw-semibold" id="btnDeleteOnlyThisOccurrence">
                            <i class="bi bi-calendar-x me-2"></i>Chỉ xóa buổi Ngày <span id="deleteEventChoiceDay">14</span>/09
                        </button>
                        <button type="button" class="btn btn-danger rounded-pill py-2.5 fw-semibold" id="btnDeleteAllOccurrences">
                            <i class="bi bi-trash3-fill me-2"></i>Xóa tất cả các buổi lặp
                        </button>
                        <button type="button" class="btn btn-light rounded-pill py-2 text-secondary mt-1" data-bs-dismiss="modal">Hủy bỏ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Calendar JS Engine -->
    <script src="{{ asset('js/calendar.js') }}"></script>
</body>

</html>