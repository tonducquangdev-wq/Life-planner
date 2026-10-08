<!-- ==========================================================================
     MOBILE BOTTOM NAVIGATION BAR - LIFE PLANNER (FLUTTER APP LOOK & FEEL)
     ========================================================================== -->
<nav class="mobile-bottom-nav d-flex d-lg-none" id="mobileBottomNav">
    <!-- 1. LỊCH -->
    <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-calendar3"></i>
        </div>
        <span>Lịch</span>
    </a>

    <!-- 2. MÔN HỌC -->
    <a href="{{ route('mon-hoc.index') }}" class="nav-item {{ request()->routeIs(['mon-hoc.*', 'bao-cao-hoc-tap.*', 'study.*']) ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <span>Học tập</span>
    </a>

    <!-- 3. CENTER FLOATING ACTION BUTTON (THÊM SỰ KIỆN NHANH) -->
    <div class="nav-fab-wrapper">
        <button type="button" class="nav-fab-btn" id="mobileFabBtn" title="Thêm sự kiện mới" data-bs-toggle="modal" data-bs-target="#createEventModal">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fabBtn = document.getElementById('mobileFabBtn');
            if (fabBtn) {
                fabBtn.addEventListener('click', function(e) {
                    const createModalEl = document.getElementById('createEventModal');
                    if (createModalEl) {
                        e.preventDefault();
                        if (typeof openCreateModalWithDay === 'function') {
                            const today = new Date().getDate();
                            openCreateModalWithDay(today);
                        } else {
                            const modal = bootstrap.Modal.getOrCreateInstance(createModalEl);
                            modal.show();
                        }
                    } else {
                        window.location.href = "{{ route('calendar.index') }}?action=create";
                    }
                });
            }
        });
    </script>

    <!-- 4. TẬP LUYỆN -->
    <a href="{{ route('tap-luyen.index') }}" class="nav-item {{ request()->routeIs(['tap-luyen.*', 'bao-cao-tap-luyen.*', 'workout.*']) ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <span>Tập luyện</span>
    </a>

    <!-- 5. HỒ SƠ CÁ NHÂN -->
    <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-person-fill"></i>
        </div>
        <span>Hồ sơ</span>
    </a>
</nav>
