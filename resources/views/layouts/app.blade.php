<!DOCTYPE html>
<html lang="vi" data-bs-theme="{{ Auth::check() && (Auth::user()->giao_dien === 'dark') ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Hồ sơ cá nhân' }} - Life Planner</title>

    <!-- Kịch bản Khởi tạo Giao diện Sáng/Tối chống giật trang (Anti-Flicker) -->
    <script>
        (function() {
            var userTheme = "{{ Auth::check() ? (Auth::user()->giao_dien ?? 'system') : 'system' }}";
            var savedTheme = localStorage.getItem('theme');
            var themeToApply = 'light';
            if (savedTheme && (savedTheme === 'dark' || savedTheme === 'light')) {
                themeToApply = savedTheme;
            } else if (userTheme && (userTheme === 'dark' || userTheme === 'light')) {
                themeToApply = userTheme;
            } else {
                themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', themeToApply);
            if (themeToApply === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        })();
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Dashboard dùng chung của hệ thống Student-Life -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>

<body>

    <!-- SIDEBAR BÊN TRÁI -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH 100VH) -->
    <div class="main-wrapper">

        <!-- TOPBAR TRÊN CÙNG -->
        @include('layouts.topbar', ['title' => $title ?? 'Hồ sơ'])

        <!-- CONTENT BODY (CUỘN ĐỘC LẬP) -->
        <main class="content-body">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Xử lý bật/tắt Sidebar Offcanvas trên thiết bị di động & tablet (< 992px)
            document.getElementById('sidebarToggle')?.addEventListener('click', function () {
                const offcanvasEl = document.getElementById('sidebarOffcanvas');
                if (offcanvasEl && typeof bootstrap !== 'undefined') {
                    const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                    bsOffcanvas.toggle();
                }
            });

            // 2. Xử lý Chuyển đổi Giao diện Dark/Light Mode nhanh toàn hệ thống
            const quickThemeBtn = document.getElementById('quickThemeBtn');
            const quickThemeIcon = document.getElementById('quickThemeIcon');

            function syncThemeIcon(theme) {
                if (!quickThemeIcon) return;
                if (theme === 'dark') {
                    quickThemeIcon.className = 'bi bi-sun-fill text-warning fs-5';
                } else {
                    quickThemeIcon.className = 'bi bi-moon-stars-fill text-primary fs-5';
                }
            }

            // Đảm bảo icon phù hợp với theme hiện tại khi tải trang
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            syncThemeIcon(currentTheme);

            if (quickThemeBtn) {
                quickThemeBtn.addEventListener('click', function () {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    if (newTheme === 'dark') {
                        document.documentElement.classList.add('dark-theme');
                    } else {
                        document.documentElement.classList.remove('dark-theme');
                    }
                    localStorage.setItem('theme', newTheme);
                    syncThemeIcon(newTheme);

                    // Báo sự kiện themeChanged cho các biểu đồ Chart.js & FullCalendar cập nhật màu
                    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));

                    // Lưu vào CSDL nếu người dùng đã đăng nhập
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch("{{ route('profile.theme.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ theme: newTheme })
                        }).catch(err => console.log('Không thể lưu cài đặt giao diện vào máy chủ:', err));
                    }
                });
            }

            // 3. Xử lý Bật/Tắt nhanh Thông báo từ Topbar Dropdown
            const btnQuickToggleNotif = document.getElementById('btnQuickToggleNotif');
            if (btnQuickToggleNotif) {
                btnQuickToggleNotif.addEventListener('change', function () {
                    const isEnabled = this.checked;
                    const mainBell = document.getElementById('mainNotifBellIcon');
                    const badgeDot = document.getElementById('notifBadgeDot');
                    const headerIcon = document.getElementById('notifHeaderIcon');

                    if (mainBell) mainBell.className = isEnabled ? 'bi bi-bell-fill' : 'bi bi-bell-slash-fill text-muted';
                    if (badgeDot) badgeDot.classList.toggle('d-none', !isEnabled);
                    if (headerIcon) headerIcon.className = isEnabled ? 'bi bi-bell-fill text-primary' : 'bi bi-bell-slash-fill text-muted';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch("{{ route('profile.notifications.quick-toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ enabled: isEnabled })
                        }).catch(err => console.log('Lỗi cập nhật thông báo:', err));
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>

