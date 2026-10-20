<!-- PWA Web App Manifest -->
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Theme Color cho thanh trạng thái di động (Sáng / Tối đồng bộ màu Header) -->
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0f172a">
<meta name="theme-color" content="#ffffff">

<!-- Tối ưu cho thiết bị Apple iOS (iPhone/iPad) & Android tràn viền -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Sử dụng "default" để iOS hiển thị status bar có nền sạch, icon rõ nét không bị đè bởi Dynamic Island / Tai thỏ -->
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Life Planner">
<link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">

<!-- CSS Tối ưu Safe Area (Tai thỏ & Thanh Home Bar trên iPhone/Android tràn viền, Dynamic Island, Camera trước) -->
<style>
    /* 1. Thiết lập biến Safe Area toàn cục */
    :root {
        --safe-area-top: env(safe-area-inset-top, 0px);
        --safe-area-bottom: env(safe-area-inset-bottom, 0px);
        --safe-area-left: env(safe-area-inset-left, 0px);
        --safe-area-right: env(safe-area-inset-right, 0px);
    }

    /* 2. Quy tắc Safe Area toàn diện cho thiết bị di động & tablet (< 992px) */
    @media (max-width: 991.98px) {
        /* Topbar / Header trên tất cả các trang: Lịch, Môn học, Tập luyện, Công việc, Dự án, Hồ sơ */
        body .top-header,
        body header.top-header,
        body .study-topbar,
        body header.study-topbar {
            padding-top: max(env(safe-area-inset-top, 0px), 12px) !important;
            padding-bottom: 10px !important;
            padding-left: max(calc(16px + env(safe-area-inset-left, 0px)), 16px) !important;
            padding-right: max(calc(16px + env(safe-area-inset-right, 0px)), 16px) !important;
            height: auto !important;
            min-height: calc(56px + env(safe-area-inset-top, 0px)) !important;
            max-height: none !important;
            box-sizing: border-box !important;
        }

        /* Khi cài đặt PWA hoặc mở Fullscreen trên di động (tránh Dynamic Island, Tai thỏ và Camera đục lỗ Android) */
        @media (display-mode: standalone), (display-mode: fullscreen) {
            body .top-header,
            body header.top-header,
            body .study-topbar,
            body header.study-topbar {
                padding-top: max(env(safe-area-inset-top, 0px), 36px) !important;
                min-height: calc(58px + max(env(safe-area-inset-top, 0px), 36px)) !important;
            }

            .modal-fullscreen-sm-down .modal-header,
            .modal-content > .modal-header {
                padding-top: max(env(safe-area-inset-top, 0px), 36px) !important;
            }

            .offcanvas-header {
                padding-top: max(env(safe-area-inset-top, 0px), 36px) !important;
            }
        }

        /* Hỗ trợ iOS standalone (navigator.standalone) */
        html.pwa-standalone body .top-header,
        html.pwa-standalone body header.top-header,
        html.pwa-standalone body .study-topbar,
        html.pwa-standalone body header.study-topbar {
            padding-top: max(env(safe-area-inset-top, 0px), 36px) !important;
            min-height: calc(58px + max(env(safe-area-inset-top, 0px), 36px)) !important;
        }

        /* Thanh điều hướng chân trang (Bottom Navigation Bar) */
        .mobile-bottom-nav,
        .bottom-nav-container {
            padding-bottom: max(env(safe-area-inset-bottom, 0px), 8px) !important;
            height: calc(60px + max(env(safe-area-inset-bottom, 0px), 8px)) !important;
            min-height: calc(60px + max(env(safe-area-inset-bottom, 0px), 8px)) !important;
            box-sizing: border-box !important;
        }

        /* Khung nội dung chính cuộn mượt, không bị Bottom Nav che mất */
        .content-body,
        .study-container,
        .subject-detail-wrapper {
            padding-bottom: calc(85px + env(safe-area-inset-bottom, 0px)) !important;
            padding-left: max(env(safe-area-inset-left, 0px), 0px) !important;
            padding-right: max(env(safe-area-inset-right, 0px), 0px) !important;
        }

        /* Modal Dialog & Popups: Đảm bảo tiêu đề và nút Đóng (X) không bị che */
        .modal {
            padding-top: env(safe-area-inset-top, 0px) !important;
            padding-bottom: env(safe-area-inset-bottom, 0px) !important;
        }

        .modal-dialog {
            margin-top: max(calc(12px + env(safe-area-inset-top, 0px)), 14px) !important;
            margin-bottom: max(calc(16px + env(safe-area-inset-bottom, 0px)), 18px) !important;
        }

        .modal-header {
            padding-top: max(calc(12px + env(safe-area-inset-top, 0px)), 14px) !important;
            padding-left: max(calc(16px + env(safe-area-inset-left, 0px)), 16px) !important;
            padding-right: max(calc(16px + env(safe-area-inset-right, 0px)), 16px) !important;
        }

        /* Offcanvas Sidebar Menu */
        .offcanvas-start,
        .offcanvas-end,
        .offcanvas-top {
            padding-top: env(safe-area-inset-top, 0px) !important;
        }

        .offcanvas-header {
            padding-top: max(calc(12px + env(safe-area-inset-top, 0px)), 14px) !important;
            padding-left: max(calc(16px + env(safe-area-inset-left, 0px)), 16px) !important;
            padding-right: max(calc(16px + env(safe-area-inset-right, 0px)), 16px) !important;
        }

        .offcanvas-body {
            padding-bottom: max(calc(16px + env(safe-area-inset-bottom, 0px)), 20px) !important;
        }

        /* Thông báo Toasts / SweetAlert2 */
        .toast-container,
        .swal2-container.swal2-top,
        .swal2-container.swal2-top-start,
        .swal2-container.swal2-top-end {
            padding-top: max(calc(16px + env(safe-area-inset-top, 0px)), 20px) !important;
        }
    }
</style>

<script>
    (function() {
        // Tự động nhận diện PWA Standalone trên iOS / Android
        if (window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches) {
            document.documentElement.classList.add('pwa-standalone');
        }
    })();
</script>

<!-- Kịch bản quản lý PWA và Web Push -->
<script src="{{ asset('js/pwa-manager.js') }}?v={{ file_exists(public_path('js/pwa-manager.js')) ? filemtime(public_path('js/pwa-manager.js')) : time() }}" defer></script>

<!-- Kịch bản quản lý Giao diện Sáng/Tối (Theme Manager) -->
@include('layouts.theme-script')
