<!-- ==========================================================================
     SIDEBAR MENU BÊN TRÁI - LIFE PLANNER (PREMIUM MODERN DASHBOARD)
     ========================================================================== -->

<!-- 1. DESKTOP SIDEBAR (CỐ ĐỊNH BÊN TRÁI TRÊN MÀN HÌNH MÁY TÍNH >= 992px) -->
<aside class="sidebar d-none d-lg-flex" id="sidebar">
    <!-- Logo thương hiệu Hiện đại & Tinh tế -->
    <div class="brand-logo">
        <a href="{{ route('calendar.index') }}" class="brand-link text-decoration-none d-flex align-items-center gap-3">
            <div class="brand-icon-wrapper">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <div class="brand-text-group">
                <span class="brand-name">Life Planner</span>
                <span class="brand-badge">PRO</span>
            </div>
        </a>
    </div>

    <!-- Danh mục Menu chính -->
    <div class="sidebar-menu-wrapper">
        <div class="sidebar-label">MENU</div>
        <ul class="sidebar-menu">
            <!-- 1. Lịch -->
            <li class="nav-item">
                <a href="{{ route('calendar.index') }}" 
                   class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                    <span class="nav-icon-box"><i class="bi bi-calendar3"></i></span>
                    <span class="nav-text">Lịch</span>
                </a>
            </li>

            <!-- 2. Báo cáo học tập -->
            <li class="nav-item">
                <a href="{{ Route::has('bao-cao-hoc-tap.index') ? route('bao-cao-hoc-tap.index') : '#' }}" 
                   class="nav-link {{ request()->routeIs(['bao-cao-hoc-tap.*', 'mon-hoc.*']) ? 'active' : '' }}">
                    <span class="nav-icon-box"><i class="bi bi-mortarboard-fill"></i></span>
                    <span class="nav-text">Báo cáo học tập</span>
                </a>
            </li>

            <!-- 3. Báo cáo tập luyện (Active trên cả tap-luyen và bao-cao-tap-luyen) -->
            <li class="nav-item">
                <a href="{{ route('tap-luyen.index') }}" 
                   class="nav-link {{ request()->routeIs(['tap-luyen.*', 'bao-cao-tap-luyen.*']) ? 'active' : '' }}">
                    <span class="nav-icon-box"><i class="bi bi-heart-pulse-fill"></i></span>
                    <span class="nav-text">Báo cáo tập luyện</span>
                </a>
            </li>

            <!-- 4. Hồ sơ cá nhân -->
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}" 
                   class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="nav-icon-box"><i class="bi bi-person-fill-gear"></i></span>
                    <span class="nav-text">Hồ sơ</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- User Profile ở đáy Sidebar -->
    <div class="sidebar-user-footer">
        <a href="{{ route('profile.edit') }}" class="sidebar-user-card text-decoration-none">
            <div class="sidebar-user-avatar">
                @if(Auth::check() && !empty(Auth::user()->avatar_url))
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="avatar-img">
                @else
                    <span class="avatar-initials">{{ Auth::check() ? Auth::user()->initials : 'U' }}</span>
                @endif
                <span class="user-status-dot" title="Trực tuyến"></span>
            </div>
            <div class="sidebar-user-info">
                <div class="user-name text-truncate">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                <div class="user-email text-truncate">{{ Auth::user()->email ?? 'user@lifeplanner.local' }}</div>
            </div>
            <div class="sidebar-user-action">
                <i class="bi bi-chevron-right fs-8"></i>
            </div>
        </a>
    </div>
</aside>

<!-- 2. MOBILE & TABLET OFFCANVAS SIDEBAR (MỞ BẰNG NÚT TOGGLE TRÊN MÀN HÌNH < 992px) -->
<div class="offcanvas offcanvas-start border-0 shadow-lg d-lg-none modern-offcanvas" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header p-3 px-4 border-bottom">
        <a href="{{ route('calendar.index') }}" class="text-decoration-none d-flex align-items-center gap-3">
            <div class="brand-icon-wrapper">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <div class="brand-text-group">
                <span class="brand-name">Life Planner</span>
                <span class="brand-badge">PRO</span>
            </div>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
        <div>
            <div class="sidebar-label px-2 mb-2">MENU</div>
            <ul class="nav nav-pills flex-column gap-1">
                <!-- 1. Lịch -->
                <li class="nav-item">
                    <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }} d-flex align-items-center gap-3 py-2 px-3 rounded-3">
                        <i class="bi bi-calendar3 fs-5"></i>
                        <span>Lịch</span>
                    </a>
                </li>

                <!-- 2. Báo cáo học tập -->
                <li class="nav-item">
                    <a href="{{ Route::has('bao-cao-hoc-tap.index') ? route('bao-cao-hoc-tap.index') : '#' }}" class="nav-link {{ request()->routeIs(['bao-cao-hoc-tap.*', 'mon-hoc.*']) ? 'active' : '' }} d-flex align-items-center gap-3 py-2 px-3 rounded-3">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                        <span>Báo cáo học tập</span>
                    </a>
                </li>

                <!-- 3. Báo cáo tập luyện -->
                <li class="nav-item">
                    <a href="{{ route('tap-luyen.index') }}" class="nav-link {{ request()->routeIs(['tap-luyen.*', 'bao-cao-tap-luyen.*']) ? 'active' : '' }} d-flex align-items-center gap-3 py-2 px-3 rounded-3">
                        <i class="bi bi-heart-pulse-fill fs-5"></i>
                        <span>Báo cáo tập luyện</span>
                    </a>
                </li>

                <!-- 4. Hồ sơ -->
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }} d-flex align-items-center gap-3 py-2 px-3 rounded-3">
                        <i class="bi bi-person-fill-gear fs-5"></i>
                        <span>Hồ sơ</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Mobile User Profile -->
        <div class="pt-3 border-top mt-auto">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none min-w-0">
                    <div class="sidebar-user-avatar">
                        @if(Auth::check() && !empty(Auth::user()->avatar_url))
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="avatar-img" style="width: 36px; height: 36px;">
                        @else
                            <span class="avatar-initials" style="width: 36px; height: 36px; font-size: 0.85rem;">{{ Auth::check() ? Auth::user()->initials : 'U' }}</span>
                        @endif
                    </div>
                    <div class="text-truncate">
                        <div class="fw-semibold fs-7 text-truncate">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
                        <small class="text-muted fs-8 text-truncate d-block">{{ Auth::user()->email ?? '' }}</small>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" title="Đăng xuất">
                        <i class="bi bi-box-arrow-right fs-6"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
