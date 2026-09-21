<!-- ==========================================================================
     SIDEBAR MENU BÊN TRÁI - LIFE PLANNER (CHỈ 4 MENU CHÍNH CHUẨN BOOTSTRAP 5.3)
     ========================================================================== -->

<!-- 1. DESKTOP SIDEBAR (CỐ ĐỊNH BÊN TRÁI TRÊN MÀN HÌNH MÁY TÍNH >= 992px) -->
<aside class="sidebar d-none d-lg-block" id="sidebar">
    <!-- Logo thương hiệu -->
    <div class="brand-logo">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Life Planner</span>
    </div>

    <!-- Danh sách menu điều hướng 4 mục chuẩn -->
    <ul class="sidebar-menu">
        <!-- 1. Lịch (Calendar First) -->
        <li class="nav-item">
            <a href="{{ route('calendar.index') }}" 
               class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Lịch</span>
            </a>
        </li>

        <!-- 2. Báo cáo học tập -->
        <li class="nav-item">
            <a href="{{ Route::has('bao-cao-hoc-tap.index') ? route('bao-cao-hoc-tap.index') : '#' }}" 
               class="nav-link {{ request()->routeIs('bao-cao-hoc-tap.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>Báo cáo học tập</span>
            </a>
        </li>

        <!-- 3. Báo cáo tập luyện -->
        <li class="nav-item">
            <a href="{{ Route::has('bao-cao-tap-luyen.index') ? route('bao-cao-tap-luyen.index') : '#' }}" 
               class="nav-link {{ request()->routeIs('bao-cao-tap-luyen.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i>
                <span>Báo cáo tập luyện</span>
            </a>
        </li>

        <!-- 4. Hồ sơ cá nhân -->
        <li class="nav-item">
            <a href="{{ route('profile.edit') }}" 
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Hồ sơ</span>
            </a>
        </li>
    </ul>
</aside>

<!-- 2. MOBILE & TABLET OFFCANVAS SIDEBAR (MỞ BẰNG NÚT TOGGLE TRÊN MÀN HÌNH < 992px) -->
<div class="offcanvas offcanvas-start border-0 shadow-lg rounded-end-4 d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel" style="width: 280px;">
    <div class="offcanvas-header p-4 border-bottom bg-light rounded-top-end-4">
        <h5 class="offcanvas-title fw-bold text-primary d-flex align-items-center gap-2" id="sidebarOffcanvasLabel">
            <i class="bi bi-journal-bookmark-fill fs-4"></i>
            <span>Life Planner</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <ul class="nav nav-pills flex-column gap-2">
            <!-- 1. Lịch -->
            <li class="nav-item">
                <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : 'text-dark' }} rounded-3 d-flex align-items-center gap-3 py-2.5 px-3 fw-semibold">
                    <i class="bi bi-calendar3 fs-5 text-primary"></i>
                    <span>Lịch</span>
                </a>
            </li>

            <!-- 2. Báo cáo học tập -->
            <li class="nav-item">
                <a href="{{ Route::has('bao-cao-hoc-tap.index') ? route('bao-cao-hoc-tap.index') : '#' }}" class="nav-link {{ request()->routeIs('bao-cao-hoc-tap.*') ? 'active' : 'text-dark' }} rounded-3 d-flex align-items-center gap-3 py-2.5 px-3 fw-semibold">
                    <i class="bi bi-bar-chart-line-fill fs-5 text-info"></i>
                    <span>Báo cáo học tập</span>
                </a>
            </li>

            <!-- 3. Báo cáo tập luyện -->
            <li class="nav-item">
                <a href="{{ Route::has('bao-cao-tap-luyen.index') ? route('bao-cao-tap-luyen.index') : '#' }}" class="nav-link {{ request()->routeIs('bao-cao-tap-luyen.*') ? 'active' : 'text-dark' }} rounded-3 d-flex align-items-center gap-3 py-2.5 px-3 fw-semibold">
                    <i class="bi bi-activity fs-5 text-success"></i>
                    <span>Báo cáo tập luyện</span>
                </a>
            </li>

            <!-- 4. Hồ sơ -->
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : 'text-dark' }} rounded-3 d-flex align-items-center gap-3 py-2.5 px-3 fw-semibold">
                    <i class="bi bi-person-circle fs-5 text-secondary"></i>
                    <span>Hồ sơ</span>
                </a>
            </li>
        </ul>
    </div>
</div>
