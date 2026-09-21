<!-- ==========================================================================
     SIDEBAR MENU BÊN TRÁI - LIFE PLANNER (CHỈ 4 MENU CHÍNH)
     ========================================================================== -->
<aside class="sidebar" id="sidebar">
    <!-- Logo thương hiệu -->
    <div class="brand-logo">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Life Planner</span>
    </div>

    <!-- Danh sách menu điều hướng chuẩn 4 mục -->
    <ul class="sidebar-menu">
        <!-- 1. Lịch (Màn hình chính Calendar First) -->
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
