<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Hồ sơ cá nhân' }} - Life Planner</title>

    <!-- Khởi tạo Giao diện Sáng (Light Theme Only) -->
    <script>
        (function() {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            document.documentElement.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light');
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

    <!-- MAIN WRAPPER (KHUNG NỘI DUNG CHÍNH FULL WIDTH - FLOATING MENU) -->
    <div class="main-wrapper main-wrapper-full">

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

            // 2. Xử lý Bật/Tắt nhanh Thông báo từ Topbar Dropdown
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
    <!-- Floating Action Menu (Góc phải bên dưới) -->
    <x-floating-menu />

    <!-- Mobile Bottom Navigation Bar (Flutter Style) -->
    @include('layouts.bottom-nav')
</body>

</html>


