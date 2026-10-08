<!-- ==========================================================================
     TOP HEADER THANH CÔNG CỤ TRÊN CÙNG - LIFE PLANNER (COMPACT 58PX)
     ========================================================================== -->
<header class="top-header">
    <!-- BÊN TRÁI: NÚT TOGGLE MENU, QUAY LẠI LỊCH & TIÊU ĐỀ TRANG -->
    <div class="topbar-left d-flex align-items-center gap-2 gap-sm-3">
        <!-- Nút Toggle Mobile Offcanvas Menu (Màn hình < 1024px) -->
        <button class="btn btn-light rounded-3 p-1.5 px-2.5 border-0 shadow-2xs topbar-sidebar-toggle" id="sidebarToggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Mở Menu danh mục">
            <i class="bi bi-list fs-5 text-dark"></i>
        </button>

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

    <!-- BÊN PHẢI: USER PROFILE DROPDOWN -->
    <div class="topbar-right header-actions d-flex align-items-center gap-2 gap-sm-3">

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
