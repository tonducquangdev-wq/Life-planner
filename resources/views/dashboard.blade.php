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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
                <!-- Nút bật/tắt Sidebar trên mobile & tablet (< 992px) -->
                <button class="btn btn-light d-lg-none p-1 px-2 border rounded-3" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>

                <!-- Thanh tìm kiếm nhanh -->
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm môn học, deadline, mục tiêu...">
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
                                {{ Auth::check() ? Auth::user()->initials : 'U' }}
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
             3. NỘI DUNG CHÍNH (MAIN CONTENT)
             ========================================================================== -->
        <main class="content-body">
            
            <!-- Tiêu đề chào mừng & Ngày tháng hiện tại -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Xin chào, {{ Auth::user()->ho_ten ?? 'Tôn Đức Quang' }}! 👋</h4>
                    <p class="text-muted mb-0 fs-7">Tổng quan nhanh về tình trạng công việc, học tập và tập luyện của bạn trong ngày.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm fs-7">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>{{ \Carbon\Carbon::now()->locale('vi')->translatedFormat('l, d/m/Y') }}
                    </span>
                    <button class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 fs-7 fw-semibold">
                        <i class="bi bi-plus-lg"></i>
                        <span>Thêm kế hoạch</span>
                    </button>
                </div>
            </div>

            <!-- ==========================================================================
                 HÀNG 1: 4 CARD THỐNG KÊ (STAT CARDS)
                 ========================================================================== -->
            <div class="row g-3 mb-4">
                <!-- 1. Tổng số môn học -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper bg-indigo-subtle text-primary" style="background-color: #e0e7ff; color: #4f46e5;">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <div>
                                <div class="stat-card-title">Tổng môn học</div>
                                <div class="stat-card-value">{{ $tongMonHoc ?? 5 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Deadline chưa hoàn thành -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper text-warning" style="background-color: #fef3c7; color: #d97706;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div>
                                <div class="stat-card-title">Deadline chưa nộp</div>
                                <div class="stat-card-value">{{ $baiTapChuaHoanThanh ?? 3 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Sự kiện hôm nay -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper text-info" style="background-color: #e0f2fe; color: #0284c7;">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <div>
                                <div class="stat-card-title">Sự kiện hôm nay</div>
                                <div class="stat-card-value">{{ $suKienHomNay ?? 3 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Buổi tập tuần này -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper text-success" style="background-color: #d1fae5; color: #059669;">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div>
                                <div class="stat-card-title">Buổi tập tuần này</div>
                                <div class="stat-card-value">{{ $buoiTapTuanNay ?? 4 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================================================
                 HÀNG 2: 2 CỘT (CỘT TRÁI: HÔM NAY - CỘT PHẢI: SẮP ĐẾN HẠN)
                 ========================================================================== -->
            <div class="row g-4 mb-4">
                
                <!-- CỘT TRÁI (70%): CARD "HÔM NAY" TIMELINE -->
                <div class="col-lg-7 col-xl-8">
                    <div class="card custom-card h-100 mb-0">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Hôm nay có gì?</span>
                            </h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-7 fw-semibold">
                                {{ count($homNayTimeline ?? []) }} Lịch trình
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="today-timeline">
                                @forelse($homNayTimeline ?? [] as $item)
                                    <div class="timeline-row">
                                        <div class="timeline-dot {{ $item['dot_class'] ?? '' }}"></div>
                                        <div class="timeline-card">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                                <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                                    <i class="bi {{ $item['icon'] ?? 'bi-calendar-event' }} text-primary"></i>
                                                    <span>{{ $item['tieu_de'] }}</span>
                                                </div>
                                                <span class="badge {{ $item['badge_class'] }} rounded-pill px-3 py-1 fs-7 fw-semibold">
                                                    {{ $item['trang_thai_text'] }}
                                                </span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-3 text-muted fs-7">
                                                <span><i class="bi bi-clock me-1 text-secondary"></i>{{ $item['thoi_gian'] }}</span>
                                                <span><i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $item['mo_ta'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted fs-7">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                        Không có sự kiện nào đặt cho hôm nay.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI (30%): CARD "SẮP ĐẾN HẠN" -->
                <div class="col-lg-5 col-xl-4">
                    <div class="card custom-card h-100 mb-0">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-hourglass-split text-warning"></i>
                                <span>Sắp đến hạn</span>
                            </h5>
                            <a href="#" class="text-decoration-none small text-primary fw-semibold fs-7">Xem tất cả</a>
                        </div>
                        <div class="card-body p-3">
                            @forelse($sapDenHan ?? [] as $deadline)
                                <div class="deadline-item">
                                    <div>
                                        <div class="fw-bold text-dark fs-6 mb-1">{{ $deadline['tieu_de'] }}</div>
                                        <small class="text-muted d-block fs-7"><i class="bi bi-journal-text me-1"></i>{{ $deadline['mon_hoc'] }}</small>
                                    </div>
                                    <span class="badge {{ $deadline['badge_class'] }} rounded-pill px-3 py-2 fs-7 fw-bold">
                                        <i class="bi bi-clock-fill me-1"></i>{{ $deadline['con_lai'] }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted fs-7">
                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                    Tuyệt vời! Không có deadline nào sắp đến hạn.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <!-- ==========================================================================
                 HÀNG 3: 2 CỘT (CỘT TRÁI: MỤC TIÊU ĐANG THỰC HIỆN - CỘT PHẢI: HOẠT ĐỘNG GẦN ĐÂY)
                 ========================================================================== -->
            <div class="row g-4">
                
                <!-- CỘT TRÁI: CARD "MỤC TIÊU ĐANG THỰC HIỆN" -->
                <div class="col-lg-7 col-xl-7">
                    <div class="card custom-card h-100 mb-0">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-trophy-fill text-primary"></i>
                                <span>Mục tiêu đang thực hiện</span>
                            </h5>
                            <a href="#" class="text-decoration-none small text-primary fw-semibold fs-7">Chi tiết mục tiêu</a>
                        </div>
                        <div class="card-body p-4">
                            @forelse($mucTieuDangThucHien ?? [] as $goal)
                                <div class="goal-item">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div>
                                            <span class="fw-bold text-dark fs-6">{{ $goal['tieu_de'] }}</span>
                                            <small class="text-muted ms-2 fs-7">({{ $goal['chi_tiet'] }})</small>
                                        </div>
                                        <span class="fw-bold text-primary fs-7">{{ $goal['phan_tram'] }}%</span>
                                    </div>
                                    <div class="goal-progress-bar">
                                        <div class="progress-bar {{ $goal['bar_class'] ?? 'bg-primary' }}" 
                                             role="progressbar" 
                                             style="width: {{ $goal['phan_tram'] }}%;" 
                                             aria-valuenow="{{ $goal['phan_tram'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted fs-7">
                                    Chưa có mục tiêu nào đang thực hiện.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: CARD "HOẠT ĐỘNG GẦN ĐÂY" -->
                <div class="col-lg-5 col-xl-5">
                    <div class="card custom-card h-100 mb-0">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="bi bi-activity text-info"></i>
                                <span>Hoạt động gần đây</span>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @forelse($hoatDongGanDay ?? [] as $act)
                                <div class="activity-item">
                                    <div class="activity-icon-box">
                                        <i class="bi {{ $act['icon'] ?? 'bi-check-circle-fill text-success' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium text-dark fs-6 leading-snug">{{ $act['noi_dung'] }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-light text-secondary border fs-7 py-0 px-2 rounded">{{ $act['badge'] ?? 'Hệ thống' }}</span>
                                            <small class="text-muted fs-7"><i class="bi bi-clock me-1"></i>{{ $act['thoi_gian'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted fs-7">
                                    Chưa có hoạt động gần đây.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script tương tác giao diện (Bật/tắt Sidebar trên mobile) -->
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
        });
    </script>
</body>
</html>

