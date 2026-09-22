<!-- ==========================================================================
     TOP HEADER THANH CÔNG CỤ TRÊN CÙNG - LIFE PLANNER
     ========================================================================== -->
<header class="top-header">
    <div class="d-flex align-items-center gap-3">
        <!-- Nút bật/tắt Sidebar trên mobile & tablet (< 992px) -->
        <button class="btn btn-light d-lg-none p-1 px-2 border rounded-3" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Toggle Sidebar">
            <i class="bi bi-list fs-4 text-primary"></i>
        </button>

        @if(!empty($title))
            <h5 class="fw-bold topbar-title mb-0">{{ $title }}</h5>
        @else
            <!-- Ô tìm kiếm nhanh (mặc định nếu không truyền title riêng) -->
            <div class="search-box d-none d-md-block">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Tìm kiếm...">
            </div>
        @endif
    </div>

    <!-- Khu vực người dùng, Dark mode & Thông báo -->
    <div class="header-actions">
        <!-- Nút chuyển chế độ Sáng/Tối (Dark/Light Mode) nhanh -->
        <button type="button" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-xs text-dark" id="quickThemeBtn" title="Đổi giao diện Sáng/Tối">
            <i class="bi {{ (Auth::user()->giao_dien ?? 'light') === 'dark' ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill text-primary' }} fs-5" id="quickThemeIcon"></i>
        </button>

        <!-- Dropdown Thông báo -->
        <div class="notification-dropdown dropdown">
            <a href="#" class="notification-btn position-relative text-dark text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo" id="notificationMenuBtn">
                <i class="bi {{ (Auth::user()->thong_bao_enabled ?? true) ? 'bi-bell-fill' : 'bi-bell-slash-fill text-muted' }}" id="mainNotifBellIcon"></i>
                <span class="badge-dot {{ (Auth::user()->thong_bao_enabled ?? true) ? '' : 'd-none' }}" id="notifBadgeDot"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="width: 380px; max-width: 90vw;">
                <!-- Header Dropdown -->
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between rounded-top-4 notif-header-bg">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi {{ (Auth::user()->thong_bao_enabled ?? true) ? 'bi-bell-fill text-primary' : 'bi-bell-slash-fill text-muted' }}" id="notifHeaderIcon"></i>Thông báo
                        <span class="badge bg-danger rounded-pill fs-8" id="notifCountBadge">3 mới</span>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-1" title="Bật/tắt nhanh thông báo">
                            <input class="form-check-input mt-0 cursor-pointer" type="checkbox" role="switch" id="btnQuickToggleNotif" {{ (Auth::user()->thong_bao_enabled ?? true) ? 'checked' : '' }}>
                        </div>
                        <button type="button" class="btn btn-link text-decoration-none p-0 fs-8 text-primary fw-semibold" id="btnMarkAllRead">Đã đọc</button>
                    </div>
                </div>

                <!-- Danh sách thông báo thực tế -->
                <div class="notification-list p-2" style="max-height: 340px; overflow-y: auto;">
                    <a href="#" class="notification-item unread d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none mb-1">
                        <div class="notif-icon bg-danger-subtle text-danger rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold fs-7 text-truncate">Deadline Nộp đồ án PHP & Laravel</div>
                            <div class="text-muted fs-8">Hạn nộp: 23:59 hôm nay (Còn 2 giờ)</div>
                            <small class="text-primary fs-8 fw-semibold">10 phút trước</small>
                        </div>
                        <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 7px; height: 7px;"></span>
                    </a>
                    <a href="#" class="notification-item unread d-flex align-items-start gap-3 p-2.5 rounded-3 text-decoration-none mb-1">
                        <div class="notif-icon bg-primary-subtle text-primary rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-book-fill fs-6"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold fs-7 text-truncate">Lịch học: Lập trình Web PHP</div>
                            <div class="text-muted fs-8">Phòng C.102 &bull; 07:30 - 11:30</div>
                            <small class="text-primary fs-8 fw-semibold">30 phút trước</small>
                        </div>
                        <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 7px; height: 7px;"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar người dùng & dropdown -->
        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle topbar-user-name" data-bs-toggle="dropdown" aria-expanded="false">
                @if(Auth::check() && !empty(Auth::user()->avatar_url))
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                @else
                    <div class="user-avatar">
                        {{ Auth::check() ? Auth::user()->initials : 'U' }}
                    </div>
                @endif
                <div class="d-none d-md-block text-start">
                    <div class="fw-bold fs-6 leading-tight user-display-name">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                    <small class="text-muted fs-7">{{ Auth::user()->email ?? 'user@lifeplanner.local' }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2 text-primary"></i>Hồ sơ cá nhân
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

