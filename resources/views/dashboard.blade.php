<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Planner - Dashboard Tổng Quan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Tùy chỉnh Dashboard -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <!-- ==========================================================================
         1. SIDEBAR BÊN TRÁI (LEFT SIDEBAR)
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
                <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Thanh tìm kiếm -->
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm môn học, lịch trình, nhiệm vụ...">
                </div>
            </div>

            <!-- Khu vực người dùng & thông báo -->
            <div class="header-actions">
                <!-- Nút thông báo -->
                <div class="notification-btn" title="Thông báo">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge-dot"></span>
                </div>

                <!-- Avatar người dùng -->
                <div class="user-profile dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            <div class="user-avatar">
                                {{ Auth::check() ? Auth::user()->initials : 'U' }}
                            </div>
                        @endif
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'Tôn Đức Quang' }}</div>
                            <small class="text-muted fs-7">{{ Auth::user()->email ?? 'quang@gmail.com' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Chỉnh sửa hồ sơ</a></li>
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
             3. NỘI DUNG CHÍNH (MAIN CONTENT)
             ========================================================================== -->
        <main class="content-body">
            
            <!-- Tiêu đề chào mừng -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Xin chào, {{ Auth::user()->ho_ten ?? 'Tôn Đức Quang' }}! 👋</h4>
                    <p class="text-muted mb-0">Hôm nay là Thứ Hai, ngày 14 tháng 09 năm 2026. Hãy kiểm tra lịch trình của bạn.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tạo lập kế hoạch</span>
                </button>
            </div>

            <!-- 4 Thẻ Thống kê Số liệu Tổng quan -->
            <div class="row g-3 mb-4">
                <!-- 1. Tổng số môn học -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-primary-subtle text-primary rounded-3">
                                <i class="bi bi-book-half fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Tổng môn học</small>
                                <h3 class="fw-bold text-dark mb-0">{{ $tongMonHoc ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Bài tập chưa hoàn thành -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-warning-subtle text-warning rounded-3">
                                <i class="bi bi-exclamation-square-fill fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Bài tập chưa nộp</small>
                                <h3 class="fw-bold text-dark mb-0">{{ $baiTapChuaHoanThanh ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Sự kiện hôm nay -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-info-subtle text-info rounded-3">
                                <i class="bi bi-calendar-event-fill fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Sự kiện hôm nay</small>
                                <h3 class="fw-bold text-dark mb-0">{{ $suKienHomNay ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Buổi tập tuần này -->
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-success-subtle text-success rounded-3">
                                <i class="bi bi-activity fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-semibold">Buổi tập tuần này</small>
                                <h3 class="fw-bold text-dark mb-0">{{ $buoiTapTuanNay ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <!-- ==========================================================================
                     CỘT BÊN TRÁI (LEFT COLUMN - 7 COLS)
                     ========================================================================== -->
                <div class="col-lg-7">

                    <!-- CARD: LỊCH THÁNG (CALENDAR WIDGET) -->
                    <div class="card custom-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-calendar-event text-primary"></i>
                                <span>Tháng 9, 2026</span>
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">Hôm nay</button>
                                <button class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-chevron-left"></i></button>
                                <button class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <table class="calendar-table">
                                <thead>
                                    <tr>
                                        <th>T2</th>
                                        <th>T3</th>
                                        <th>T4</th>
                                        <th>T5</th>
                                        <th>T6</th>
                                        <th>T7</th>
                                        <th>CN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="calendar-day other-month">31</td>
                                        <td class="calendar-day">1</td>
                                        <td class="calendar-day">2</td>
                                        <td class="calendar-day">3</td>
                                        <td class="calendar-day">4</td>
                                        <td class="calendar-day">5</td>
                                        <td class="calendar-day">6</td>
                                    </tr>
                                    <tr>
                                        <td class="calendar-day">7</td>
                                        <td class="calendar-day">8</td>
                                        <td class="calendar-day has-event">9</td>
                                        <td class="calendar-day">10</td>
                                        <td class="calendar-day">11</td>
                                        <td class="calendar-day">12</td>
                                        <td class="calendar-day">13</td>
                                    </tr>
                                    <tr>
                                        <!-- Ngày 14 đang chọn (active) -->
                                        <td class="calendar-day active has-event">14</td>
                                        <td class="calendar-day">15</td>
                                        <td class="calendar-day has-event">16</td>
                                        <td class="calendar-day">17</td>
                                        <td class="calendar-day">18</td>
                                        <td class="calendar-day">19</td>
                                        <td class="calendar-day">20</td>
                                    </tr>
                                    <tr>
                                        <td class="calendar-day">21</td>
                                        <td class="calendar-day has-event">22</td>
                                        <td class="calendar-day">23</td>
                                        <td class="calendar-day">24</td>
                                        <td class="calendar-day">25</td>
                                        <td class="calendar-day">26</td>
                                        <td class="calendar-day">27</td>
                                    </tr>
                                    <tr>
                                        <td class="calendar-day">28</td>
                                        <td class="calendar-day">29</td>
                                        <td class="calendar-day">30</td>
                                        <td class="calendar-day other-month">1</td>
                                        <td class="calendar-day other-month">2</td>
                                        <td class="calendar-day other-month">3</td>
                                        <td class="calendar-day other-month">4</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CARD: HIỂN THỊ CÁC SỰ KIỆN TRONG NGÀY -->
                    <div class="card custom-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Sự kiện ngày 14/09/2026</span>
                            </h5>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">3 Sự kiện</span>
                        </div>
                        <div class="card-body p-3">
                            <!-- Sự kiện 1: Học tập -->
                            <div class="event-item hoc-tap">
                                <div class="p-2 bg-indigo-subtle rounded-3 text-indigo">
                                    <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1">Báo cáo Đồ án Tốt nghiệp</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>Phòng B2.04 - 08:00 đến 11:30</small>
                                </div>
                                <span class="badge bg-indigo-subtle text-primary border border-primary-subtle">Học tập</span>
                            </div>

                            <!-- Sự kiện 2: Thể chất -->
                            <div class="event-item the-chat">
                                <div class="p-2 bg-emerald-subtle rounded-3 text-emerald">
                                    <i class="bi bi-heart-pulse-fill fs-4 text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1">Tập Gym - Buổi tập Chân & Vai</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>CLB Fitness Center - 17:30 đến 19:00</small>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Thể chất</span>
                            </div>

                            <!-- Sự kiện 3: Cá nhân -->
                            <div class="event-item ca-nhan">
                                <div class="p-2 bg-amber-subtle rounded-3 text-amber">
                                    <i class="bi bi-person-fill-check fs-4 text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1">Họp nhóm sinh hoạt CLB</div>
                                    <small class="text-muted"><i class="bi bi-camera-video me-1"></i>Google Meet - 20:00 đến 21:00</small>
                                </div>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Cá nhân</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ==========================================================================
                     CỘT BÊN PHẢI (RIGHT COLUMN - 5 COLS)
                     ========================================================================== -->
                <div class="col-lg-5">

                    <!-- CARD: LỊCH TRÌNH HÔM NAY (TODAY'S SCHEDULE TIMELINE) -->
                    <div class="card custom-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-list-task text-primary"></i>
                                <span>Lịch trình hôm nay</span>
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="fw-bold text-dark">08:00 - 10:00</div>
                                    <div class="text-secondary small">Học Kiến trúc Phần mềm Laravel 13</div>
                                </div>
                                <div class="timeline-item">
                                    <div class="fw-bold text-dark">14:00 - 16:00</div>
                                    <div class="text-secondary small">Review bài lab Thiết kế CSDL MySQL</div>
                                </div>
                                <div class="timeline-item">
                                    <div class="fw-bold text-dark">17:30 - 19:00</div>
                                    <div class="text-secondary small">Tập thể lực & Chạy bộ 5km</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD: NHIỆM VỤ SẮP ĐẾN HẠN (UPCOMING DEADLINES) -->
                    <div class="card custom-card">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                <span>Nhiệm vụ sắp đến hạn</span>
                            </h5>
                            <a href="#" class="text-decoration-none small text-primary fw-semibold">Xem tất cả</a>
                        </div>
                        <div class="card-body p-3">
                            <!-- Task 1 -->
                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded border-bottom">
                                <div class="form-check">
                                    <input class="form-check-input task-checkbox" type="checkbox" id="task1">
                                    <label class="form-check-label fw-medium text-dark ms-1" for="task1">Nộp Báo cáo Lập trình Web</label>
                                    <div class="text-muted fs-7 ms-1">Hạn: Hôm nay, 23:59</div>
                                </div>
                                <span class="badge-priority cao">Cao</span>
                            </div>

                            <!-- Task 2 -->
                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded border-bottom">
                                <div class="form-check">
                                    <input class="form-check-input task-checkbox" type="checkbox" id="task2" checked>
                                    <label class="form-check-label fw-medium text-dark ms-1" for="task2">Hoàn thiện Slide Thuyết minh</label>
                                    <div class="text-muted fs-7 ms-1">Hạn: Ngày mai, 12:00</div>
                                </div>
                                <span class="badge-priority trung_binh">Trung bình</span>
                            </div>

                            <!-- Task 3 -->
                            <div class="d-flex align-items-center justify-content-between p-2 rounded">
                                <div class="form-check">
                                    <input class="form-check-input task-checkbox" type="checkbox" id="task3">
                                    <label class="form-check-label fw-medium text-dark ms-1" for="task3">Đọc tài liệu Clean Code</label>
                                    <div class="text-muted fs-7 ms-1">Hạn: 18/09/2026</div>
                                </div>
                                <span class="badge-priority thap">Thấp</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD: SỰ KIỆN NỔI BẬT / GHI CHÚ QUAN TRỌNG -->
                    <div class="featured-card shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                            <span class="fw-bold text-uppercase fs-7 tracking-wider">Mục tiêu nổi bật</span>
                        </div>
                        <h5 class="fw-bold mb-2">Hoàn thành Đồ án Đạt GPA 3.8 🎯</h5>
                        <p class="fs-7 opacity-90 mb-3">Duy trì tiến độ học tập và rèn luyện thể chất mỗi ngày để đạt mục tiêu cuối kỳ.</p>
                        <button class="btn btn-light btn-sm rounded-pill fw-semibold text-primary px-3">Xem chi tiết mục tiêu</button>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT TƯƠNG TÁC GIAO DIỆN ĐƠN GIẢN (BẬT/TẮT SIDEBAR MOBILE) -->
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
        });
    </script>
</body>
</html>
