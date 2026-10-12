<!-- ==========================================================================
     FLOATING ACTION MENU - LIFE PLANNER (GÓC PHẢI BÊN DƯỚI)
     ========================================================================== -->
<div class="floating-menu-container d-none d-lg-block" id="floatingMenuContainer">
    <!-- POPUP MENU -->
    <div class="floating-menu-popup" id="floatingMenuPopup" role="menu" aria-orientation="vertical">
        <div class="floating-menu-header px-2 py-1 mb-2 d-flex align-items-center justify-content-between">
            <span class="floating-menu-header-title text-uppercase">Điều hướng</span>
            <span class="badge floating-menu-badge rounded-pill">Life Planner</span>
        </div>

        <ul class="floating-menu-list">
            <!-- 1. Lịch (Home chính) -->
            <li>
                <a href="{{ route('calendar.index') }}" 
                   class="floating-menu-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
                   title="Lịch cá nhân (Home)">
                    <span class="menu-item-icon icon-calendar">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <span class="menu-item-label">📅 Lịch (Home)</span>
                </a>
            </li>

            <!-- 2. Học tập -->
            <li>
                <a href="{{ route('mon-hoc.index') }}" 
                   class="floating-menu-item {{ request()->routeIs(['mon-hoc.*', 'bao-cao-hoc-tap.*', 'study.*']) ? 'active' : '' }}"
                   title="Học tập & Môn học">
                    <span class="menu-item-icon icon-study">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span class="menu-item-label">📚 Học tập</span>
                </a>
            </li>

            <!-- 3. Tập luyện -->
            <li>
                <a href="{{ route('tap-luyen.index') }}" 
                   class="floating-menu-item {{ request()->routeIs(['tap-luyen.*', 'bao-cao-tap-luyen.*', 'workout.*']) ? 'active' : '' }}"
                   title="Thể chất & Rèn luyện">
                    <span class="menu-item-icon icon-workout">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </span>
                    <span class="menu-item-label">🏋️ Tập luyện</span>
                </a>
            </li>

            <li class="floating-menu-divider"></li>

            <!-- 4. Hồ sơ cá nhân (Mở Modal, không chuyển trang) -->
            <li>
                <button type="button" 
                   class="floating-menu-item btn-open-profile-modal w-100 text-start border-0 bg-transparent cursor-pointer"
                   data-bs-toggle="modal"
                   data-bs-target="#profileModal"
                   title="Hồ sơ & Cài đặt (Popup)">
                    <span class="menu-item-icon icon-profile">
                        <i class="bi bi-person-fill-gear"></i>
                    </span>
                    <span class="menu-item-label">👤 Hồ sơ</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- NÚT TOGGLE MENU (FLOAT BUTTON) -->
    <button type="button" class="floating-menu-btn" id="floatingMenuToggle" aria-label="Menu điều hướng nhanh" title="Menu điều hướng nhanh">
        <i class="bi bi-compass-fill icon-open"></i>
        <i class="bi bi-x-lg icon-close"></i>
    </button>
</div>

<!-- PROFILE MODAL DÙNG CHUNG TOÀN HỆ THỐNG -->
@include('components.profile-modal')

<script>
    (function() {
        const container = document.getElementById('floatingMenuContainer');
        const toggleBtn = document.getElementById('floatingMenuToggle');

        if (!container || !toggleBtn) return;

        // Cung cấp các helper toàn cục để quản lý trạng thái Menu
        window.isFloatingMenuOpen = function() {
            return container.classList.contains('active');
        };

        window.closeFloatingMenu = function() {
            if (container.classList.contains('active')) {
                container.classList.remove('active');
                return true;
            }
            return false;
        };

        window.openFloatingMenu = function() {
            // Khi mở Menu: tự động đóng Chat nếu Chat đang mở
            if (typeof window.closeCommunityChat === 'function') {
                window.closeCommunityChat();
            } else {
                const chatPopup = document.getElementById('communityMessengerPopup');
                const chatBtn = document.getElementById('floatingCommunityToggle');
                if (chatPopup) chatPopup.classList.remove('active');
                if (chatBtn) chatBtn.classList.remove('active');
            }
            container.classList.add('active');
        };

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (container.classList.contains('active')) {
                window.closeFloatingMenu();
            } else {
                window.openFloatingMenu();
            }
        });

        // Đóng menu khi bấm vào bất kỳ mục nào (đặc biệt là nút Hồ sơ)
        container.querySelectorAll('.floating-menu-item').forEach(item => {
            item.addEventListener('click', function() {
                window.closeFloatingMenu();
            });
        });

        // Đóng menu khi bấm ra ngoài
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                window.closeFloatingMenu();
            }
        });

        // Đóng menu khi nhấn Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && container.classList.contains('active')) {
                window.closeFloatingMenu();
            }
        });
    })();
</script>

<!-- MOBILE BOTTOM NAVIGATION BAR (FLUTTER APP STYLE) -->
@include('layouts.bottom-nav')

