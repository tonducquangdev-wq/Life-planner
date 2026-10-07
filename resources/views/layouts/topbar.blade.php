<!-- ==========================================================================
     TOP HEADER THANH CÔNG CỤ TRÊN CÙNG - LIFE PLANNER (COMPACT 58PX)
     ========================================================================== -->
<header class="top-header">
    <!-- BÊN TRÁI: NÚT QUAY LẠI LỊCH & TIÊU ĐỀ TRANG -->
    <div class="topbar-left d-flex align-items-center gap-3">
        <!-- Nút Quay lại Lịch (Luôn đưa người dùng trực tiếp về /calendar) -->
        <a href="{{ route('calendar.index') }}" class="btn-back-to-calendar" title="Quay lại giao diện Lịch chính">
            <i class="bi bi-arrow-left"></i>
            <span>Quay lại Lịch</span>
        </a>

        <div class="page-title-box d-flex align-items-center gap-2">
            <h5 class="fw-bold topbar-title mb-0">{{ $title ?? 'Theo dõi thể chất' }}</h5>
        </div>
    </div>

    <!-- Ở GIỮA: THANH TÌM KIẾM NHANH (SEARCH) -->
    <div class="topbar-center d-none d-md-block">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Tìm kiếm bài tập, lịch trình...">
        </div>
    </div>

    <!-- BÊN PHẢI: DARK MODE, THÔNG BÁO & USER PROFILE DROPDOWN -->
    <div class="topbar-right header-actions d-flex align-items-center gap-2 gap-sm-3">
        <!-- Nút chuyển chế độ Sáng/Tối (Dark/Light Mode) nhanh -->
        <button type="button" class="theme-toggle-btn btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-2xs" id="quickThemeBtn" title="Đổi giao diện Sáng/Tối">
            <i class="bi {{ (Auth::user()->giao_dien ?? 'light') === 'dark' ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill text-primary' }} fs-6" id="quickThemeIcon"></i>
        </button>

        <!-- Dropdown Thông báo -->
        <div class="notification-dropdown dropdown">
            <a href="#" class="notification-btn position-relative text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo" id="notificationMenuBtn">
                <i class="bi {{ (Auth::user()->thong_bao_enabled ?? true) ? 'bi-bell-fill' : 'bi-bell-slash-fill text-muted' }}" id="mainNotifBellIcon"></i>
                <span class="badge-dot {{ (Auth::user()->thong_bao_enabled ?? true) ? '' : 'd-none' }}" id="notifBadgeDot"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="width: 360px; max-width: 90vw;">
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
                <div class="notification-list p-2" style="max-height: 300px; overflow-y: auto;">
                    <a href="#" class="notification-item unread d-flex align-items-start gap-2.5 p-2 rounded-3 text-decoration-none mb-1">
                        <div class="notif-icon bg-danger-subtle text-danger rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-exclamation-triangle-fill fs-7"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold fs-7 text-truncate">Deadline Nộp đồ án PHP & Laravel</div>
                            <div class="text-muted fs-8">Hạn nộp: 23:59 hôm nay (Còn 2 giờ)</div>
                            <small class="text-primary fs-8 fw-semibold">10 phút trước</small>
                        </div>
                        <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 6px; height: 6px;"></span>
                    </a>
                    <a href="#" class="notification-item unread d-flex align-items-start gap-2.5 p-2 rounded-3 text-decoration-none mb-1">
                        <div class="notif-icon bg-primary-subtle text-primary rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-book-fill fs-7"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold fs-7 text-truncate">Lịch học: Lập trình Web PHP</div>
                            <div class="text-muted fs-8">Phòng C.102 &bull; 07:30 - 11:30</div>
                            <small class="text-primary fs-8 fw-semibold">30 phút trước</small>
                        </div>
                        <span class="notif-dot bg-primary rounded-circle mt-1" style="width: 6px; height: 6px;"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar người dùng, Tên & Email & Dropdown -->
        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle topbar-user-link" data-bs-toggle="dropdown" aria-expanded="false">
                @if(Auth::check() && !empty(Auth::user()->avatar_url))
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar rounded-circle object-fit-cover">
                @else
                    <div class="user-avatar">
                        {{ Auth::check() ? Auth::user()->initials : 'U' }}
                    </div>
                @endif
                <div class="d-none d-lg-block text-start user-meta">
                    <div class="fw-bold fs-7 leading-tight user-display-name text-truncate">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                    <small class="text-muted fs-8 user-email text-truncate">{{ Auth::user()->email ?? 'user@lifeplanner.local' }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2">
                <li class="px-3 py-2 border-bottom d-lg-none">
                    <div class="fw-bold fs-7 text-truncate">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                    <small class="text-muted fs-8 text-truncate d-block">{{ Auth::user()->email ?? '' }}</small>
                </li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 rounded-2 mx-1 btn-open-profile-modal cursor-pointer" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="bi bi-person text-primary"></i><span>Hồ sơ cá nhân</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger rounded-2 mx-1">
                            <i class="bi bi-box-arrow-right"></i><span>Đăng xuất</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
