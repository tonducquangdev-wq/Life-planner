<!-- ==========================================================================
     TOP HEADER THANH CÔNG CỤ TRÊN CÙNG - LIFE PLANNER
     ========================================================================== -->
<header class="top-header">
    <div class="d-flex align-items-center gap-3">
        <!-- Nút bật/tắt Sidebar trên mobile -->
        <button class="btn btn-light d-lg-none p-1 px-2 border" id="sidebarToggle" type="button" aria-label="Toggle Sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

        @if(!empty($title))
            <h5 class="fw-bold text-dark mb-0">{{ $title }}</h5>
        @else
            <!-- Ô tìm kiếm nhanh (mặc định nếu không truyền title riêng) -->
            <div class="search-box d-none d-md-block">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Tìm kiếm...">
            </div>
        @endif
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
                    <div class="fw-bold text-dark fs-6 leading-tight">{{ Auth::user()->ho_ten ?? 'Người dùng' }}</div>
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
