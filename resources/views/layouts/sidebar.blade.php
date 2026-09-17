<!-- ==========================================================================
     SIDEBAR MENU BÊN TRÁI - LIFE PLANNER
     ========================================================================== -->
<aside class="sidebar" id="sidebar">
    <!-- Logo thương hiệu -->
    <div class="brand-logo">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Life Planner</span>
    </div>

    <!-- Danh sách menu điều hướng chính -->
    <ul class="sidebar-menu">
        <!-- 1. Tổng quan (Dashboard) -->
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" 
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Tổng quan</span>
            </a>
        </li>

        <!-- 2. Học tập (Môn học & Bài tập) -->
        <li class="nav-item">
            <a href="{{ route('mon-hoc.index') }}" 
               class="nav-link {{ request()->routeIs('mon-hoc.*') || request()->routeIs('bai-tap.*') ? 'active' : '' }}">
                <i class="bi bi-book-half"></i>
                <span>Học tập</span>
            </a>
        </li>

        <!-- 3. Lịch (Sự kiện & Lịch trình) -->
        <li class="nav-item">
            <a href="#" 
               class="nav-link {{ request()->routeIs('lich.*') || request()->routeIs('su-kien.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Lịch</span>
            </a>
        </li>

        <!-- 4. Thể chất (Bài tập & Kế hoạch tập luyện) -->
        <li class="nav-item">
            <a href="{{ route('tap-luyen.index') }}" 
               class="nav-link {{ request()->routeIs('tap-luyen.*') || request()->routeIs('the-chat.*') || request()->routeIs('buoi-tap.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i>
                <span>Thể chất</span>
            </a>
        </li>

        <!-- 5. Hồ sơ cá nhân -->
        <li class="nav-item">
            <a href="{{ route('profile.edit') }}" 
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Hồ sơ</span>
            </a>
        </li>

        <!-- 6. Cài đặt hệ thống -->
        <li class="nav-item">
            <a href="#" 
               class="nav-link {{ request()->routeIs('settings.*') || request()->routeIs('cai-dat.*') ? 'active' : '' }}">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Cài đặt</span>
            </a>
        </li>
    </ul>
</aside>
