<!-- ==========================================================================
     FLOATING ACTION MENU - LIFE PLANNER (GÓC PHẢI BÊN DƯỚI)
     ========================================================================== -->
<div class="floating-menu-container" id="floatingMenuContainer">
    <!-- POPUP MENU -->
    <div class="floating-menu-popup" id="floatingMenuPopup" role="menu" aria-orientation="vertical">
        <div class="px-2 py-1 mb-1 d-flex align-items-center justify-content-between">
            <span class="fs-8 fw-bold text-uppercase text-muted letter-spacing-1">Điều hướng</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill fs-8">Life Planner</span>
        </div>

        <ul class="floating-menu-list">
            <!-- 1. Lịch (Home chính) -->
            <li>
                <a href="{{ route('calendar.index') }}" 
                   class="floating-menu-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
                   title="Lịch cá nhân (Home)">
                    <span class="menu-item-icon bg-primary-subtle text-primary">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <span>📅 Lịch (Home)</span>
                </a>
            </li>

            <!-- 2. Học tập -->
            <li>
                <a href="{{ route('mon-hoc.index') }}" 
                   class="floating-menu-item {{ request()->routeIs(['mon-hoc.*', 'bao-cao-hoc-tap.*', 'study.*']) ? 'active' : '' }}"
                   title="Học tập & Môn học">
                    <span class="menu-item-icon bg-info-subtle text-info">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span>📚 Học tập</span>
                </a>
            </li>

            <!-- 3. Tập luyện -->
            <li>
                <a href="{{ route('tap-luyen.index') }}" 
                   class="floating-menu-item {{ request()->routeIs(['tap-luyen.*', 'bao-cao-tap-luyen.*', 'workout.*']) ? 'active' : '' }}"
                   title="Thể chất & Rèn luyện">
                    <span class="menu-item-icon bg-success-subtle text-success">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </span>
                    <span>🏋️ Tập luyện</span>
                </a>
            </li>

            <li class="floating-menu-divider"></li>

            <!-- 4. Hồ sơ cá nhân -->
            <li>
                <a href="{{ route('profile.edit') }}" 
                   class="floating-menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   title="Hồ sơ & Cài đặt">
                    <span class="menu-item-icon bg-secondary-subtle text-secondary">
                        <i class="bi bi-person-fill-gear"></i>
                    </span>
                    <span>👤 Hồ sơ</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- NÚT TOGGLE MENU (FLOAT BUTTON) -->
    <button type="button" class="floating-menu-btn" id="floatingMenuToggle" aria-label="Menu điều hướng nhanh" title="Menu điều hướng nhanh">
        <i class="bi bi-list icon-open"></i>
        <i class="bi bi-x-lg icon-close"></i>
    </button>
</div>

<script>
    (function() {
        const container = document.getElementById('floatingMenuContainer');
        const toggleBtn = document.getElementById('floatingMenuToggle');

        if (!container || !toggleBtn) return;

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            container.classList.toggle('active');
        });

        // Đóng menu khi bấm ra ngoài
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                container.classList.remove('active');
            }
        });

        // Đóng menu khi nhấn Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && container.classList.contains('active')) {
                container.classList.remove('active');
            }
        });
    })();
</script>
